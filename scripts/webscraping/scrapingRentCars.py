import sys
import json
import time
import re
import traceback
from datetime import datetime
from playwright.sync_api import sync_playwright, TimeoutError as PlaywrightTimeout

# Configuração de encoding UTF-8
sys.stdout.reconfigure(encoding='utf-8', errors='replace')
sys.stderr.reconfigure(encoding='utf-8', errors='replace')

# --- [NOVO] MAPA DE MESES PARA O DATEPICKER ---
# Necessário para localizar o mês correto no calendário
MESES_MAP = {
    1: "janeiro", 2: "fevereiro", 3: "março", 4: "abril",
    5: "maio", 6: "junho", 7: "julho", 8: "agosto",
    9: "setembro", 10: "outubro", 11: "novembro", 12: "dezembro"
}

def log_info(message):
    """Log de informação"""
    print(f"[INFO] {message}", file=sys.stderr)


def log_error(message):
    """Log de erro"""
    print(f"[ERROR] {message}", file=sys.stderr)


def safe_print(text):
    """Print seguro com encoding"""
    try:
        print(text, flush=True)
    except UnicodeEncodeError:
        print(text.encode('utf-8', errors='replace').decode('utf-8'), flush=True)


def parse_arguments():
    """
    Argumentos esperados:
    1. local_retirada (string) - Ex: "Jaguariúna, SP, Brasil"
    2. data_retirada (YYYY-MM-DD) - Ex: "2025-10-18"
    3. hora_retirada (HH:MM) - Ex: "10:00"
    4. data_devolucao (YYYY-MM-DD) - Ex: "2025-10-19"
    5. hora_devolucao (HH:MM) - Ex: "10:00"
    """
    if len(sys.argv) != 6:
        log_error(f"Argumentos incorretos. Recebidos: {len(sys.argv)-1}, esperados: 5")
        log_error(f"Args: {sys.argv}")
        sys.exit(1)
    
    return {
        'local_retirada': sys.argv[1],
        'data_retirada': sys.argv[2],
        'hora_retirada': sys.argv[3],
        'data_devolucao': sys.argv[4],
        'hora_devolucao': sys.argv[5]
    }

# --- [NOVO] FUNÇÃO PARA SELECIONAR DATAS ---
def selecionar_datas(page, data_retirada_str, data_devolucao_str):
    """
    Clica no calendário e seleciona a data de retirada e devolução.
    Baseado no datepicker.html fornecido.
    """
    try:
        log_info(f"Iniciando seleção de datas: {data_retirada_str} a {data_devolucao_str}")

        # 1. Converter strings para objetos datetime
        data_retirada = datetime.strptime(data_retirada_str, '%Y-%m-%d')
        data_devolucao = datetime.strptime(data_devolucao_str, '%Y-%m-%d')

        dia_retirada = str(data_retirada.day)
        dia_devolucao = str(data_devolucao.day)
        
        # Formato "mês ano" (ex: "outubro 2025")
        mes_ano_retirada = f"{MESES_MAP[data_retirada.month]} {data_retirada.year}"
        
        # 2. Clicar no botão para abrir o datepicker
        # Baseado no formulario.html
        log_info("Clicando no botão 'Data de retirada' para abrir o calendário...")
        page.locator('[data-testid="button-date-search-pickup"]').click()

        # 3. Aguardar o calendário aparecer
        page.wait_for_selector('.rdp-multiple_months', timeout=10000)
        log_info("Calendário aberto.")

        # 4. Navegar até o mês/ano correto
        # O datepicker.html mostra dois meses, vamos checar o primeiro (rdp-caption_start)
        max_tentativas = 12 # Evitar loop infinito
        tentativa = 0
        while tentativa < max_tentativas:
            # Pega o texto do primeiro mês visível
            mes_visivel = page.locator('.rdp-caption_start .text-large').first.inner_text().strip().lower()
            log_info(f"Mês visível: '{mes_visivel}', buscando por: '{mes_ano_retirada}'")
            
            if mes_visivel == mes_ano_retirada:
                log_info("Mês de retirada encontrado!")
                break
            
            # Se não for o mês certo, clica em "próximo mês"
            log_info("Mês incorreto, clicando em 'próximo mês'...")
            page.locator('button[name="next-month"]').click()
            time.sleep(0.5) # Pequena pausa para o calendário atualizar
            
            tentativa += 1
            if tentativa >= max_tentativas:
                raise Exception(f"Não foi possível encontrar o mês {mes_ano_retirada} após 12 tentativas.")

        # 5. Clicar no dia de RETIRADA
        # Seletor: botão com nome "day", que não esteja desabilitado, e com o texto do dia
        log_info(f"Clicando no dia de retirada: {dia_retirada}")
        page.locator(
            f'button[name="day"]:not([disabled]):text-is("{dia_retirada}")'
        ).first.click()

        # 6. Clicar no dia de DEVOLUÇÃO
        # O calendário pode ou não mudar; assumindo que a data de devolução
        # está no mesmo mês ou no próximo (que já está visível).
        log_info(f"Clicando no dia de devolução: {dia_devolucao}")
        page.locator(
            f'button[name="day"]:not([disabled]):text-is("{dia_devolucao}")'
        ).first.click()

        # 7. Aguardar o calendário fechar
        page.wait_for_selector('.rdp-multiple_months', state='hidden', timeout=5000)
        log_info("Datas selecionadas e calendário fechado.")

    except Exception as e:
        log_error(f"Erro ao selecionar datas: {str(e)}")
        raise

def esperar_carregamento_completo(page, timeout=60000):
    """Espera o carregamento completo removendo loaders"""
    try:
        page.wait_for_load_state('networkidle', timeout=timeout)
        return True
    except PlaywrightTimeout:
        log_error("Timeout aguardando carregamento")
        return False


def scroll_incremental(page, max_scrolls=15):
    """Scroll incremental para carregar mais resultados"""
    for i in range(max_scrolls):
        altura_antes = page.evaluate('document.body.scrollHeight')
        page.evaluate('window.scrollTo(0, document.body.scrollHeight)')
        time.sleep(1) 
        altura_depois = page.evaluate('document.body.scrollHeight')
        
        if altura_antes == altura_depois:
            log_info(f"Fim do conteúdo após {i+1} scrolls")
            break
        
        log_info(f"Scroll {i+1}/{max_scrolls} - carregando mais resultados...")


def extrair_texto_seguro(elemento, seletor):
    """Extrai texto de forma segura"""
    try:
        elem = elemento.locator(seletor).first
        if elem.count() > 0:
            return elem.inner_text().strip()
    except:
        pass
    return None


def extrair_atributo_seguro(elemento, seletor, atributo):
    """Extrai atributo de forma segura"""
    try:
        elem = elemento.locator(seletor).first
        if elem.count() > 0:
            return elem.get_attribute(atributo)
    except:
        pass
    return None


def extrair_configuracoes(card):
    """Extrai configurações do veículo (passageiros, malas, etc)"""
    configs = {
        'passageiros': None,
        'malas': None,
        'ar_condicionado': False,
        'cambio': None,
        'quilometragem': None
    }
    
    try:
        itens = card.locator('.booking-configurations__item')
        
        for i in range(itens.count()):
            try:
                item = itens.nth(i)
                desc = item.locator('.booking-configurations__item--description')
                
                if desc.count() > 0:
                    texto = desc.first.inner_text().strip()
                    img = item.locator('img')
                    if img.count() > 0:
                        alt = img.first.get_attribute('alt') or ''
                        
                        if 'passageiro' in alt.lower():
                            try:
                                configs['passageiros'] = int(texto)
                            except:
                                pass
                        elif 'mala' in alt.lower():
                            configs['malas'] = texto
                        elif 'cambio' in alt.lower():
                            configs['cambio'] = texto
                        elif 'km' in alt.lower():
                            configs['quilometragem'] = texto
            except:
                continue
        
        ar_elem = card.locator('img[alt*="ac"], img[alt*="condicionado"]')
        configs['ar_condicionado'] = ar_elem.count() > 0
            
    except Exception as e:
        log_error(f"Erro ao extrair configurações: {str(e)}")
    
    return configs


def extrair_diferenciais(card):
    """Extrai lista de diferenciais/proteções"""
    diferenciais = []
    
    try:
        itens = card.locator('.booking-differentials__item')
        for i in range(itens.count()):
            texto = itens.nth(i).inner_text().strip()
            if texto:
                diferenciais.append(texto)
    except:
        pass
    
    return diferenciais


def extrair_tags(card):
    """Extrai tags especiais (proteções, promoções)"""
    tags = []
    
    try:
        tags_elem = card.locator('.info-tag_1Roy6dYj span, .tag_2COg8phM span')
        for i in range(tags_elem.count()):
            texto = tags_elem.nth(i).inner_text().strip()
            if texto:
                tags.append(texto)
    except:
        pass
    
    return tags

# --- [VERIFICADO] FUNÇÃO DE EXTRAÇÃO DE LOCAL ---
def extrair_local_retirada(card):
    """Extrai informações do local de retirada"""
    local_info = {
        'endereco': None,
        'tipo': None,
        'nome': None
    }
    
    try:
        # Seu seletor .booking-pickup-service__address estava CORRETO
        # com base no card.html fornecido.
        endereco_elem = card.locator('.booking-pickup-service__address')
        if endereco_elem.count() > 0:
            local_info['endereco'] = endereco_elem.first.inner_text().strip()
        else:
            # Adicionado log para debug, caso algum card não tenha
            log_info("  -> (Debug) Endereço de retirada não encontrado neste card.")
        
        tipo_elem = card.locator('.booking-pickup-service__service')
        if tipo_elem.count() > 0:
            local_info['tipo'] = tipo_elem.first.inner_text().strip()
        
    except Exception as e:
        log_error(f"Erro ao extrair local: {str(e)}")
    
    return local_info


def extrair_info_locadora(card):
    """Extrai informações da locadora"""
    locadora_info = {
        'nome': None,
        'logo': None,
        'avaliacao': None
    }
    
    try:
        logo_elem = card.locator('.rental-company-evaluation-img_3FvMRZD5 img, .rental-company-evaluation img')
        if logo_elem.count() > 0:
            locadora_info['logo'] = logo_elem.first.get_attribute('src')
            locadora_info['nome'] = logo_elem.first.get_attribute('alt')
        
        av_elem = card.locator('.evaluation-value_gQkFUU98')
        if av_elem.count() > 0:
            try:
                av_text = av_elem.first.inner_text().strip()
                if av_text and av_text.lower() != 'false':
                    locadora_info['avaliacao'] = float(av_text)
            except:
                pass
                
    except Exception as e:
        log_error(f"Erro ao extrair locadora: {str(e)}")
    
    return locadora_info

# --- [CORRIGIDO] FUNÇÃO DE EXTRAÇÃO DE PREÇO ---
def extrair_preco(card):
    """
    Extrai informações de preço.
    Função reescrita para usar o seletor específico do card.html,
    que é muito mais confiável que o regex anterior.
    """
    preco_info = {
        'total': None,
        'moeda': 'BRL'
    }
    
    try:
        # Seletor específico do card.html: .total-amount_1XUQg1Kt
        preco_elem = card.locator('.total-amount_1XUQg1Kt').first
        
        if preco_elem.count() > 0:
            # Ex: "R$ 197,77"
            texto_preco = preco_elem.inner_text().strip() 
            
            # Limpa (remove "R$", espaços, &nbsp;) e normaliza para float (troca "," por ".")
            # \s* cobre espaço normal e &nbsp;
            valor_str = re.sub(r'[^\d,]', '', texto_preco) # Resultado: "197,77"
            
            if valor_str:
                valor_float = float(valor_str.replace(',', '.'))
                preco_info['total'] = valor_float
        else:
            log_info("  -> (Debug) Preço total não encontrado neste card.")
                
    except Exception as e:
        log_error(f"Erro ao extrair preço: {str(e)}")
    
    return preco_info


def verificar_alerta_localizacao(page):
    """
    Verifica se há alerta de busca em localização alternativa
    Retorna dict com informações ou None
    """
    try:
        alerta = page.locator('.bfc-alert-new-search_2vYKROLg').first
        
        if alerta.is_visible(timeout=2000):
            texto_alerta = alerta.inner_text()
            
            match_original = re.search(r'destino\s+([^,]+)', texto_alerta, re.IGNORECASE)
            local_original = match_original.group(1).strip() if match_original else None
            
            match_alternativo = re.search(r'localização\s+([^,]+)', texto_alerta, re.IGNORECASE)
            local_alternativo = match_alternativo.group(1).strip() if match_alternativo else None
            
            match_distancia = re.search(r'à\s+(\d+\s*km)', texto_alerta, re.IGNORECASE)
            distancia = match_distancia.group(1).strip() if match_distancia else None
            
            return {
                'local_original': local_original,
                'local_alternativo': local_alternativo,
                'distancia': distancia,
                'mensagem_completa': texto_alerta
            }
    except:
        pass
    
    return None


def coletar_resultados(page):
    """Coleta todos os cards de veículos da página"""
    resultados = []
    
    # Localizar todos os cards
    cards = page.locator('.card-vehicle-border_u5Q5DR7a') #
    total_cards = cards.count()
    
    log_info(f"Total de veículos encontrados: {total_cards}")
    
    for i in range(total_cards):
        try:
            card = cards.nth(i)
            
            # === INFORMAÇÕES BÁSICAS ===
            nome = extrair_texto_seguro(card, '.card-vehicle-title_1x3XzWOV') #
            categoria = extrair_texto_seguro(card, '.card-vehicle-title-complementary_2r1d60_k') #
            imagem = extrair_atributo_seguro(card, '.card-vehicle-container-left-middle_3eJmF9mL img', 'src') #
            
            # === EXTRAIR LINK "CONTINUAR" ===
            link_continuar = extrair_atributo_seguro(card, 'a[data-testid^="list-page__btn-continue-"]', 'href') #
            
            # === CONFIGURAÇÕES DO VEÍCULO ===
            configs = extrair_configuracoes(card) #
            
            # === DIFERENCIAIS/PROTEÇÕES ===
            diferenciais = extrair_diferenciais(card) #
            
            # === TAGS ===
            tags = extrair_tags(card) #
            
            # === LOCAL DE RETIRADA ===
            local_info = extrair_local_retirada(card) #
            
            # === LOCADORA ===
            locadora_info = extrair_info_locadora(card) #
            
            # === PREÇO ===
            preco_info = extrair_preco(card) #
            
            # Montar objeto do veículo
            veiculo = {
                'nome': nome,
                'categoria': categoria,
                'imagem': imagem,
                'link_continuar': link_continuar, 
                'configuracoes': configs,
                'diferenciais': diferenciais,
                'tags': tags,
                'local_retirada': local_info,
                'locadora': locadora_info,
                'preco': preco_info
            }
            
            resultados.append(veiculo)
            
            # Log de progresso
            if (i + 1) % 5 == 0:
                log_info(f"Processados {i + 1}/{total_cards} veículos")
                
        except Exception as e:
            log_error(f"Erro ao processar card {i}: {str(e)}")
            continue
    
    return {
        'total': len(resultados),
        'veiculos': resultados
    }


def scrape_rentcars(params):
    """Função principal de scraping"""
    with sync_playwright() as p:
        log_info("Inicializando navegador...")
        browser = p.chromium.launch(
            headless=False,  # Mude para False para ver o navegador
            args=[
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--disable-web-security',
                '--window-size=1920,1080'
            ]
        )
        
        log_info("Criando contexto do navegador...")
        context = browser.new_context(
            user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            viewport={'width': 1920, 'height': 1080}
        )
        page = context.new_page()
        page.set_default_timeout(60000)
        
        log_info("Navegador pronto!")
        
        try:
            # 1. ACESSAR SITE
            log_info("Acessando RentCars...")
            page.goto("https://www.rentcars.com/pt-br/", timeout=60000)
            page.wait_for_selector('#searchInputDesk', timeout=15000)
            log_info("Site carregado!")
            
            # 2. PREENCHER LOCAL DE RETIRADA
            log_info(f"Preenchendo local: {params['local_retirada']}")
            
            try:
                input_local = page.wait_for_selector('#searchInputDesk', timeout=10000)
                input_local.click()
                input_local.fill('')
                input_local.type(params['local_retirada'], delay=50)
                
            except Exception as e:
                log_error(f"Erro ao preencher input: {str(e)}")
                raise Exception(f"Falha ao preencher local de retirada: {str(e)}")
            
            # --- [SEÇÃO CORRIGIDA] ---
            # 3. SELECIONAR PRIMEIRO RESULTADO DO DROPDOWN
            log_info("Aguardando dropdown de locais...")
            
            try:
                # Esperar o container geral de opções aparecer
                page.wait_for_selector('.DropdownList_option__EdtLh', timeout=10000)
                log_info("Dropdown encontrado!")
                
                # [CORREÇÃO]
                # Selecionar o *primeiro item clicável* (PlaceOption_container__1a_xv)
                # que está dentro do container da lista (.DropdownList_option__EdtLh)
                # Isso evita clicar no sub-item "Núcleo Santa Barbara"
                primeiro_item = page.locator(
                    '.DropdownList_option__EdtLh .PlaceOption_container__1a_xv'
                ).first
                
                if primeiro_item.count() > 0:
                    # Pegar o texto do item correto para o log
                    local_texto = primeiro_item.locator('.PlaceOption_label__5blsB').first.inner_text()
                    log_info(f"Selecionando primeiro item: {local_texto[:100]}")
                    
                    # Clicar no item correto
                    primeiro_item.click()
                    log_info("Item selecionado com sucesso!")
                    
                    # Aguardar que o dropdown desapareça
                    page.wait_for_selector('.DropdownList_option__EdtLh', state='hidden', timeout=5000)
                else:
                    log_error("Nenhum item encontrado no dropdown")
                    raise Exception("Dropdown vazio")
                    
            except Exception as e:
                log_error(f"Erro ao selecionar dropdown: {str(e)}")
                log_info("Tentando continuar sem selecionar do dropdown...")
            # --- [FIM DA CORREÇÃO] ---
            
            # 4. SELECIONAR DATAS
            selecionar_datas(
                page,
                params['data_retirada'],
                params['data_devolucao']
            )
            
            # 5. SELECIONAR HORAS
            log_info(f"Usando horas padrão (ou as pré-selecionadas pelo site)")
            
            # 6. CLICAR NO BOTÃO PESQUISAR
            log_info("Clicando em pesquisar...")
            
            try:
                btn_pesquisar = page.locator('[data-testid="button-search-desktop"]')
                
                if btn_pesquisar.count() > 0:
                    log_info("Botão de pesquisar encontrado, clicando...")
                    btn_pesquisar.click()
                    log_info("Botão clicado com sucesso!")
                else:
                    log_error("Botão Pesquisar não encontrado!")
                    raise Exception("Botão Pesquisar não encontrado")
                
            except Exception as e:
                log_error(f"Erro ao clicar no botão: {str(e)}")
                raise
            
            # 7. AGUARDAR CARREGAMENTO
            log_info("Aguardando carregamento dos resultados (pode demorar)...")
            try:
                page.wait_for_selector('.card-vehicle-border_u5Q5DR7a', timeout=120000)
                log_info("Cards de veículos detectados!")
            except PlaywrightTimeout:
                log_error("Timeout ao aguardar cards de veículos.")
                if not esperar_carregamento_completo(page, timeout=30000):
                    log_error("Timeout ao aguardar resultados, tentando continuar...")
            
            # 8. VERIFICAR ALERTA
            alerta_info = verificar_alerta_localizacao(page)
            if alerta_info:
                log_info(f"Alerta detectado: {alerta_info['mensagem_completa']}")
            
            # 9. SCROLL
            log_info("Fazendo scroll para carregar todos os resultados...")
            scroll_incremental(page, max_scrolls=15)
            
            # 10. VERIFICAR SE HÁ CARDS
            log_info("Verificando se há resultados na página...")
            cards = page.locator('.card-vehicle-border_u5Q5DR7a')
            total_cards = cards.count()
            log_info(f"Total de cards encontrados: {total_cards}")
            
            if total_cards == 0:
                log_error("ATENÇÃO: Nenhum card de veículo encontrado na página!")
                log_info(f"URL atual: {page.url}")
            
            # 11. COLETAR TODOS OS RESULTADOS
            log_info("Coletando resultados...")
            # (Assumindo que você já atualizou esta função com a coleta do 'link_continuar')
            resultados = coletar_resultados(page)
            
            if alerta_info:
                resultados['alerta'] = alerta_info
            
            # 12. IMPRIMIR RESULTADOS
            safe_print(json.dumps(resultados, ensure_ascii=False, indent=2))
            
        except Exception as e:
            log_error(f"Erro durante scraping: {str(e)}")
            log_error(traceback.format_exc())
            sys.exit(1)
        finally:
            log_info("Fechando navegador.")
            browser.close()


if __name__ == "__main__":
    try:
        params = parse_arguments()
        log_info(f"Iniciando scraping com parâmetros: {params}")
        scrape_rentcars(params)
    except Exception as e:
        log_error(f"Erro fatal: {str(e)}")
        log_error(traceback.format_exc())
        sys.exit(1)