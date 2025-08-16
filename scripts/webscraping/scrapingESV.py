import sys 
import io
from datetime import datetime
from urllib.parse import urlparse
from playwright.sync_api import sync_playwright

# Força saída em UTF-8 para evitar problemas com caracteres especiais
if sys.stdout.encoding != 'utf-8':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def extrair_nome_site(url):
    """
    Extrai o nome do site a partir da URL removendo prefixos e sufixos comuns.
    Exemplo: www.easyseguroviagem.com.br -> easyseguroviagem
    """
    try:
        host = urlparse(url).netloc.lower()
        if host.startswith("www."):
            host = host[4:]
        for sufixo in [".com.br", ".com", ".net", ".org", ".br"]:
            if host.endswith(sufixo):
                host = host[:-len(sufixo)]
        return host
    except:
        return "site"

def main():
    # Validação básica da quantidade de argumentos
    if len(sys.argv) < 7:
        # print("Uso: python scrapingESV.py <motivo> <destino> <data_ida> <data_volta> <qtd_passageiros> <idade1> ... <idadeN>")
        return

    motivo = sys.argv[1]
    destino = sys.argv[2]
    data_ida = sys.argv[3]
    data_volta = sys.argv[4]
    qtd_passageiros = int(sys.argv[5])
    idades = sys.argv[6:]

    # Validação de passageiros
    if qtd_passageiros < 1 or qtd_passageiros > 8:
        # print("Número de passageiros deve ser entre 1 e 8.")
        return

    if len(idades) < qtd_passageiros:
        # print(f"Você informou {len(idades)} idades, mas selecionou {qtd_passageiros} passageiros.")
        return

    data_ida_obj = datetime.strptime(data_ida, "%Y-%m-%d")
    data_volta_obj = datetime.strptime(data_volta, "%Y-%m-%d")

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context()

        # Bloqueia carregamento de imagens e fontes para acelerar
        context.route("**/*.{png,jpg,jpeg,svg,woff,woff2}", lambda route: route.abort())
        page = context.new_page()

        page.goto("https://www.easyseguroviagem.com.br", timeout=20000)

        # Preenche formulário: motivo e destino
        page.select_option("#MainContent_Cotador_ddlMotivoDaViagem", motivo)
        page.select_option("#MainContent_Cotador_selContinente", destino)

        # Seleciona datas no calendário
        page.click("#MainContent_Cotador_daterange")
        page.wait_for_selector("//td[contains(@class, 'available')]", timeout=5000)

        xpath_ida = f"//td[contains(@class, 'available') and text()='{data_ida_obj.day}']"
        page.locator(xpath_ida).nth(0).click()

        xpath_volta = f"//td[contains(@class, 'available') and text()='{data_volta_obj.day}']"
        page.locator(xpath_volta).nth(1).click()

        page.click(".applyBtn")

        # Define quantidade de passageiros e preenche idades
        page.select_option("#MainContent_Cotador_selQtdCliente", str(qtd_passageiros))
        page.wait_for_selector(f"#txtIdadePassageiro{qtd_passageiros}", timeout=3000)

        for i in range(1, qtd_passageiros + 1):
            campo_id = f"#txtIdadePassageiro{i}"
            page.fill(campo_id, idades[i - 1])
            page.dispatch_event(campo_id, "change")
            page.dispatch_event(campo_id, "blur")

        page.locator("body").click()
        page.wait_for_selector("#MainContent_Cotador_btnComprar:enabled", timeout=5000)
        page.click("#MainContent_Cotador_btnComprar")

        # Espera os cards aparecerem
        try:
            page.wait_for_selector(".card-produto", timeout=8000)
        except:
            # Nenhum plano carregado
            browser.close()
            return

        url_resultado = page.url
        cards = page.locator(".card-produto")
        total = cards.count()

        for i in range(total):
            card = cards.nth(i)
            texto = card.text_content().replace("Veja os detalhes da cobertura", "").strip()
            site = extrair_nome_site(url_resultado)

            # Imprime informações organizadas
            print(site)
            for linha in texto.split("\n"):
                linha = linha.strip()
                if linha:
                    print(linha)
            print(url_resultado)
            print("=====")

        browser.close()

if __name__ == "__main__":
    main()