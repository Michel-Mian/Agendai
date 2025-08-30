import sys
import io
import time
from datetime import datetime, timedelta
from playwright.sync_api import sync_playwright

# Forçar UTF-8 na saída
if sys.stdout.encoding != 'utf-8':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

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

    # Seleciona todos os dias do mês atual (incluindo desabilitados)
    todos_dias = page.locator('.react-datepicker__day:not(.react-datepicker__day--outside-month)')
    for i in range(todos_dias.count()):
        el = todos_dias.nth(i)
        texto = el.text_content().strip()
        aria_disabled = el.get_attribute("aria-disabled")
        # print(f"[DEBUG] Dia: {texto} | aria-disabled={aria_disabled} | html={el.inner_html()}")

    # Seleciona apenas dias clicáveis do mês atual
    dias_validos = page.locator('.react-datepicker__day[aria-disabled="false"]:not(.react-datepicker__day--outside-month)')
    for i in range(dias_validos.count()):
        el = dias_validos.nth(i)
        # print(f"[DEBUG] Dia disponível: texto={el.text_content().strip()} html={el.inner_html()}")

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
        print(f"[ERRO] Dia {data_obj.day} não encontrado ou está desabilitado no calendário.", file=sys.stderr)
        print("[DEBUG] Dias disponíveis:", file=sys.stderr)
        for i in range(dias_validos.count()):
            el = dias_validos.nth(i)
            print(el.text_content().strip(), file=sys.stderr)
        raise Exception(f"Dia {data_obj.day} não encontrado ou está desabilitado no calendário.")

    page.wait_for_timeout(200)  # pequena pausa para registrar o clique

def main():
    inicio = time.time()

    # Verifica se recebeu os parâmetros necessários
    if len(sys.argv) != 7:
        # print("Uso: python scrapingSP.py <destino> <data_ida> <data_volta> <nome> <email> <celular>")
        # Sugere datas futuras automaticamente
        hoje = datetime.today()
        data_ida = (hoje + timedelta(days=30)).strftime("%Y-%m-%d")
        data_volta = (hoje + timedelta(days=37)).strftime("%Y-%m-%d")
        print(f"Exemplo de uso com datas futuras:")
        print(f"python scripts/webscraping/scrapingSP.py \"Europa\" {data_ida} {data_volta} \"SeuNome\" \"seu@email.com\" \"11999999999\"")
        return

    destino = sys.argv[1]
    data_ida = sys.argv[2]
    data_volta = sys.argv[3]
    nome = sys.argv[4]
    email = sys.argv[5]
    celular = sys.argv[6]

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context()
        # Bloqueia imagens e fontes para acelerar
        context.route("**/*.{png,jpg,jpeg,svg,woff,woff2}", lambda route: route.abort())
        page = context.new_page()
        page.goto("https://www.segurospromo.com.br", timeout=20000)

        # Seleciona destino na lista
        page.click("#destinationSp")
        page.wait_for_selector("li.styles_option__hh1To", timeout=3000)

        opcoes = page.locator("li.styles_option__hh1To")
        total = opcoes.count()
        encontrado = False
        for i in range(total):
            texto = opcoes.nth(i).text_content().strip()
            if texto.lower() == destino.lower():
                opcoes.nth(i).click()
                encontrado = True
                break

        if not encontrado:
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

        # Espera os cards carregarem
        try:
            page.wait_for_selector(".show-up-plan", timeout=6000)
        except:
            browser.close()
            return

        cards = page.locator(".show-up-plan")
        total_cards = cards.count()

        for i in range(total_cards):
            card = cards.nth(i)
            nome_plano = card.locator(".header span").text_content().strip()
            preco_pix = card.locator(".price-pix span").nth(0).text_content().strip()
            preco_cartao = card.locator(".price-atually").text_content().strip()
            cobertura_medica = card.locator(".plan-info").nth(0).locator(".plan-info-benefit").text_content().strip()
            cobertura_bagagem = card.locator(".plan-info").nth(1).locator(".plan-info-benefit").text_content().strip()
            link = card.locator(".link a").get_attribute("href")

            # Imprime os dados organizados imediatamente
            print("SeguroPromo")
            print(nome_plano)
            print(f"Despesa médica hospitalar: {cobertura_medica}")
            print(f"Seguro bagagem: {cobertura_bagagem}")
            print(preco_pix)
            print(preco_cartao)
            print("https://www.segurospromo.com.br" + link if link else "")
            print("=====")
            sys.stdout.flush()

        browser.close()

if __name__ == "__main__":
    main()