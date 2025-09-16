import sys
import io
import time
import traceback
from datetime import datetime, timedelta
from playwright.sync_api import sync_playwright

# CORREÇÃO: Forçar UTF-8 de forma mais robusta
sys.stdout.reconfigure(encoding='utf-8', errors='replace')
sys.stderr.reconfigure(encoding='utf-8', errors='replace')

def log_error(message):
    """Log de erro padronizado"""
    print(f"[ERRO] {message}", file=sys.stderr, flush=True)

def log_info(message):
    """Log de info padronizado"""
    print(f"[INFO] {message}", file=sys.stderr, flush=True)

def safe_print(text):
    """Print seguro com encoding"""
    try:
        print(text, flush=True)
    except UnicodeEncodeError:
        # Se falhar, usar ASCII com replace
        print(text.encode('ascii', 'replace').decode('ascii'), flush=True)

def selecionar_data(page, data_obj, seletor_input):
    """
    Seleciona a data no calendário react-datepicker clicando no mês/ano correto e no dia.
    """
    meses = {
        'janeiro': 1, 'fevereiro': 2, 'março': 3, 'abril': 4, 'maio': 5,
        'junho': 6, 'julho': 7, 'agosto': 8, 'setembro': 9,
        'outubro': 10, 'novembro': 11, 'dezembro': 12
    }

    page.click(seletor_input)
    page.wait_for_selector(".react-datepicker__day", timeout=5000)

    while True:
        mes_ano_texto = page.locator(".react-datepicker__current-month").first.inner_text().lower()
        mes_atual, ano_atual = mes_ano_texto.split()
        ano_atual = int(ano_atual)
        mes_atual_num = meses[mes_atual]

        if mes_atual_num == data_obj.month and ano_atual == data_obj.year:
            break

        if (ano_atual < data_obj.year) or (ano_atual == data_obj.year and mes_atual_num < data_obj.month):
            page.click("button[aria-label='Next Month']")
        else:
            page.click("button[aria-label='Previous Month']")

        page.wait_for_selector(".react-datepicker__day", timeout=2000)

    dias_validos = page.locator('.react-datepicker__day[aria-disabled="false"]:not(.react-datepicker__day--outside-month)')
    
    encontrado = False
    dia_str = str(data_obj.day)

    for i in range(dias_validos.count()):
        el = dias_validos.nth(i)
        texto = el.text_content().strip()
        if texto == dia_str:
            el.click()
            encontrado = True
            break

    if not encontrado:
        log_error(f"Dia {data_obj.day} não encontrado ou está desabilitado no calendário.")
        raise Exception(f"Dia {data_obj.day} não encontrado ou está desabilitado no calendário.")

    page.wait_for_timeout(200)

def main():
    try:
        if len(sys.argv) != 7:
            log_error(f"Argumentos incorretos. Recebidos: {len(sys.argv)-1}, esperados: 6")
            log_error(f"Args: {sys.argv}")
            return

        destino = sys.argv[1]
        data_ida = sys.argv[2]
        data_volta = sys.argv[3]
        nome = sys.argv[4]
        email = sys.argv[5]
        celular = sys.argv[6]

        log_info(f"Iniciando scraping para destino: {destino}")
        log_info(f"Datas: {data_ida} até {data_volta}")

        with sync_playwright() as p:
            browser = p.chromium.launch(
                headless=False, # Alterado para False para facilitar a depuração visual
                args=[
                    '--no-sandbox', 
                    '--disable-dev-shm-usage',
                    '--disable-gpu',
                    '--disable-web-security',
                    '--disable-features=VizDisplayCompositor'
                ]
            )
            
            context = browser.new_context(
                user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            )
            
            page = context.new_page()
            page.set_default_timeout(30000)
            
            try:
                log_info("Acessando SeguroPromo...")
                page.goto("https://www.segurospromo.com.br", timeout=30000)
                
                page.wait_for_selector("#destinationSp", timeout=10000)
                
                page.click("#destinationSp")
                page.wait_for_selector("li.styles_option__hh1To", timeout=3000)

                opcoes = page.locator("li.styles_option__hh1To")
                encontrado = False
                for i in range(opcoes.count()):
                    texto = opcoes.nth(i).text_content().strip()
                    if texto.lower() == destino.lower():
                        opcoes.nth(i).click()
                        encontrado = True
                        break

                if not encontrado:
                    log_error(f"Destino '{destino}' não encontrado")
                    browser.close()
                    return

                dia_ida = datetime.strptime(data_ida, "%Y-%m-%d")
                dia_volta = datetime.strptime(data_volta, "%Y-%m-%d")

                selecionar_data(page, dia_ida, "#departureDateInput")
                selecionar_data(page, dia_volta, "#returningDateInput")

                page.fill("#nameSp", nome)
                page.fill("#emailSp", email)
                page.fill("#cellphoneSp", celular)

                page.click("button[type='submit']")

                page.wait_for_load_state("networkidle", timeout=15000)

                try:
                    page.wait_for_selector(".box-plans", timeout=10000)
                except Exception as e:
                    log_error(f"Timeout aguardando seguros: {str(e)}")
                    browser.close()
                    return

                page.wait_for_timeout(2000)

                cards = page.locator(".box-plans")
                total_cards = cards.count()

                if total_cards == 0:
                    log_error("Nenhum seguro encontrado")
                    browser.close()
                    return

                for i in range(total_cards):
                    try:
                        card = cards.nth(i)
                        
                        # --- INÍCIO DAS MELHORIAS ---

                        # 1. Nome da Seguradora (extraído do SRC da imagem)
                        seguradora_element = card.locator(".box-container-image img").first
                        seguradora = "N/A"
                        if seguradora_element.count() > 0:
                            try:
                                src = seguradora_element.get_attribute("src")
                                if src:
                                    # Exemplo: /assets/images/seguradoras/my-travel-assist.png -> my-travel-assist
                                    file_name = src.split('/')[-1].split('.')[0]
                                    seguradora = file_name.replace('-', ' ').title()
                            except Exception:
                                seguradora = "Erro ao extrair seguradora"

                        # 2. Nome do Plano
                        nome_plano_element = card.locator(".names-plans").first
                        nome_plano = nome_plano_element.text_content().strip() if nome_plano_element.count() > 0 else "N/A"
                        
                        # 3. Faixa Etária e Detalhes
                        faixa_etaria_element = card.locator(".prices-high").first
                        faixa_etaria = "N/A"
                        if faixa_etaria_element.count() > 0:
                            # inner_text() do Playwright lida bem com <br> convertendo para \n
                            faixa_etaria = faixa_etaria_element.inner_text().replace('\n', ' | ').strip()
                        
                        # 4. Despesa médica
                        cobertura_medica = "N/A"
                        infos_plans = card.locator(".infos-plans")
                        for j in range(infos_plans.count()):
                            info = infos_plans.nth(j)
                            strong_element = info.locator("strong").first
                            if "Despesa Médica" in strong_element.text_content():
                                div_element = info.locator("div").last
                                cobertura_medica = div_element.text_content().strip()
                                break
                        
                        # 5. Seguro bagagem
                        cobertura_bagagem = "N/A"
                        for j in range(infos_plans.count()):
                            info = infos_plans.nth(j)
                            strong_element = info.locator("strong").first
                            if "bagagem" in strong_element.text_content().lower():
                                div_element = info.locator("div").last
                                cobertura_bagagem = div_element.text_content().strip()
                                break
                        
                        # 6. Preço PIX
                        preco_pix_element = card.locator(".price-pix span").first
                        preco_pix = preco_pix_element.text_content().strip() if preco_pix_element.count() > 0 else "N/A"
                        
                        # 7. Preço Cartão e Parcelamento (lógica melhorada)
                        preco_cartao_element = card.locator(".price-atually").first
                        preco_cartao_valor = "N/A"
                        preco_cartao_parcelas = "N/A"
                        if preco_cartao_element.count() > 0:
                            valor_bold_element = preco_cartao_element.locator("b")
                            if valor_bold_element.count() > 0:
                                preco_cartao_valor = valor_bold_element.text_content().strip()
                                # O resto do texto é a informação de parcelamento
                                texto_completo = preco_cartao_element.text_content().strip()
                                preco_cartao_parcelas = texto_completo.replace(preco_cartao_valor, '').strip()
                            else:
                                # Caso não haja a tag <b>, pega o texto completo
                                preco_cartao_valor = preco_cartao_element.text_content().strip()

                        # 8. Link para mais detalhes
                        link_element = card.locator(".url-roof").first
                        link = link_element.get_attribute("href") if link_element.count() > 0 else ""
                        link_completo = "https://www.segurospromo.com.br" + link if link else ""

                        # --- FIM DAS MELHORIAS ---

                        # Impressão no formato solicitado, agora com todos os dados
                        print("SeguroPromo")
                        print(f"Seguradora: {seguradora}")
                        print(f"Plano: {nome_plano}")
                        print(f"Detalhes etários: {faixa_etaria}")
                        print(f"Despesa médica hospitalar: {cobertura_medica}")
                        print(f"Seguro bagagem: {cobertura_bagagem}")
                        print(f"Preço PIX: {preco_pix}")
                        print(f"Preço Cartão: {preco_cartao_valor}")
                        print(f"Parcelamento Cartão: {preco_cartao_parcelas}")
                        print(f"Link: {link_completo}")
                        print("=====")
                        sys.stdout.flush()
                        
                    except Exception as e:
                        log_error(f"Erro ao processar seguro {i}: {str(e)}")
                        continue

                log_info("Scraping concluído com sucesso")
                
            except Exception as e:
                log_error(f"Erro durante scraping: {str(e)}")
                log_error(f"Traceback: {traceback.format_exc()}")
                raise
            finally:
                browser.close()
                
    except Exception as e:
        log_error(f"Erro crítico no main: {str(e)}")
        log_error(f"Traceback completo: {traceback.format_exc()}")
        sys.exit(1)

if __name__ == "__main__":
    main()