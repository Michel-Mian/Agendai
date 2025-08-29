import sys
import io
import time
from datetime import datetime
from playwright.sync_api import sync_playwright

# Força saída em UTF-8
if sys.stdout.encoding != 'utf-8':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def selecionar_data_jquery_ui(page, seletor_input, data_obj):
    """
    Seleciona data usando o calendário jQuery UI.
    Navega meses/anos até o desejado e clica no dia.
    """
    page.click(seletor_input)
    page.wait_for_selector("#ui-datepicker-div", timeout=10000)

    ano = data_obj.year
    dia = data_obj.day
    mes = data_obj.month - 1  # jQuery UI usa mês zero-based

    mes_atual = int(page.evaluate("() => document.querySelector('#ui-datepicker-div .ui-datepicker-month').value"))
    ano_atual = int(page.evaluate("() => document.querySelector('#ui-datepicker-div .ui-datepicker-year').textContent").strip())

    # Avança ou retrocede meses até data desejada
    while (ano_atual < ano) or (ano_atual == ano and mes_atual < mes):
        page.click("#ui-datepicker-div .ui-datepicker-next")
        page.wait_for_timeout(200)
        mes_atual = int(page.evaluate("() => document.querySelector('#ui-datepicker-div .ui-datepicker-month').value"))
        ano_atual = int(page.evaluate("() => document.querySelector('#ui-datepicker-div .ui-datepicker-year').textContent").strip())

    while (ano_atual > ano) or (ano_atual == ano and mes_atual > mes):
        page.click("#ui-datepicker-div .ui-datepicker-prev")
        page.wait_for_timeout(200)
        mes_atual = int(page.evaluate("() => document.querySelector('#ui-datepicker-div .ui-datepicker-month').value"))
        ano_atual = int(page.evaluate("() => document.querySelector('#ui-datepicker-div .ui-datepicker-year').textContent").strip())

    seletor_dia = f"#ui-datepicker-div td[data-month='{mes}'][data-year='{ano}'] a:text-is('{dia}')"
    page.wait_for_selector(seletor_dia, timeout=10000)
    page.click(seletor_dia)
    page.wait_for_timeout(200)

def main():
    if len(sys.argv) != 8:
        print("Uso: python scrapingAV.py <destino_id> <data_ida> <data_volta> <nome> <email> <telefone> <idades_com_virgula>")
        return

    inicio = time.time()

    destino = sys.argv[1]
    data_ida = sys.argv[2]
    data_volta = sys.argv[3]
    nome = sys.argv[4]
    email = sys.argv[5]
    telefone = sys.argv[6]
    idades = sys.argv[7].split(",")

    qtd_viajantes = len(idades)

    data_ida_obj = datetime.strptime(data_ida, "%Y-%m-%d")
    data_volta_obj = datetime.strptime(data_volta, "%Y-%m-%d")

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context()
        # Bloqueia imagens e fontes para acelerar
        context.route("**/*.{png,jpg,jpeg,svg,woff,woff2}", lambda route: route.abort())
        page = context.new_page()

        page.goto("https://assistentedeviagem.com.br/seguro-viagem/cotacao-seguro-viagem.php", timeout=20000)

        page.click("#destino + .nice-select")
        page.wait_for_selector(f"#destino + .nice-select .option[data-value='{destino}']", timeout=3000)
        page.click(f"#destino + .nice-select .option[data-value='{destino}']")

        selecionar_data_jquery_ui(page, "#from", data_ida_obj)
        page.wait_for_function(f'document.querySelector("#from").value === "{data_ida_obj.strftime("%d/%m/%Y")}"')

        selecionar_data_jquery_ui(page, "#to", data_volta_obj)
        page.wait_for_function(f'document.querySelector("#to").value === "{data_volta_obj.strftime("%d/%m/%Y")}"')

        page.click(".box-qtd .nice-select")
        page.wait_for_selector(f".box-qtd .nice-select .option[data-value='{qtd_viajantes}']", timeout=3000)
        page.click(f".box-qtd .nice-select .option[data-value='{qtd_viajantes}']")

        page.wait_for_selector("div.js-ages input[type='tel']", timeout=6000)
        campos_idade = page.locator("div.js-ages input[type='tel']")
        total_inputs = campos_idade.count()
        tentativas = 0
        while total_inputs < qtd_viajantes and tentativas < 6:
            page.wait_for_timeout(200)
            total_inputs = campos_idade.count()
            tentativas += 1

        for i, idade in enumerate(idades):
            try:
                campo = campos_idade.nth(i)
                campo.fill("")
                campo.fill(idade)
                page.wait_for_timeout(60)
            except Exception:
                pass

        page.fill('input#name[type="text"]', nome)
        page.fill('input#phone[type="tel"]', telefone)
        page.fill('input#email[type="email"]', email)

        page.click("#btnCotacao")

        try:
            page.wait_for_selector(".new-plano-destaque", timeout=8000)
        except:
            browser.close()
            return

        cards = page.locator(".new-plano-destaque")
        total_cards = cards.count()

        for i in range(total_cards):
            card = cards.nth(i)
            try:
                cobertura_medica = card.locator("span.new-plano-destaque-tituloCobertura").text_content().strip()
                valor_cobertura = card.locator("span.new-plano-destaque-valorCobertura").text_content().strip()
                cobertura_bagagem = card.locator("span.new-plano-destaque-tituloBagagem").text_content().strip()
                valor_bagagem = card.locator("span.new-plano-destaque-valorBagagem").text_content().strip()
                preco_pix = card.locator("div.new-plano-destaque-textoPix > span").text_content().strip()
                preco_cartao = card.locator("div.new-plano-destaque-textoCartao > span").text_content().strip()
                link = card.locator("div.new-plano-destaque-detalhes-container a").get_attribute("href")
                seguradora_img = card.locator("div.img-plano-destaque img").get_attribute("alt")

                print("Assistentedeviagem")
                print(f"Seguradora: {seguradora_img}")
                print(f"{cobertura_medica}: {valor_cobertura}")
                print(f"{cobertura_bagagem}: {valor_bagagem}")
                print(f"Preço PIX: {preco_pix}")
                print(f"Preço Cartão: {preco_cartao}")
                print(link if link else "")
                print("=====")
                sys.stdout.flush()

            except Exception:
                pass

        browser.close()

if __name__ == "__main__":
    main()