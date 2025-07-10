import sys
import io
import time
import unicodedata
from datetime import datetime
from urllib.parse import urlparse
from playwright.sync_api import sync_playwright

# Garantir codificação UTF-8
if sys.stdout.encoding != 'utf-8':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

# Função para remover acentos
def remover_acentos(txt):
    return unicodedata.normalize("NFKD", txt).encode("ASCII", "ignore").decode("utf-8")

# Função para extrair domínio limpo de uma URL
def extrair_nome_site(url):
    try:
        host = urlparse(url).netloc.lower()
        if host.startswith("www."):
            host = host[4:]
        for sufixo in [".com.br", ".com", ".net", ".org", ".br"]:
            if host.endswith(sufixo):
                host = host[:-len(sufixo)]
        return remover_acentos(host)
    except:
        return "site"

# Função principal
def main():
    if len(sys.argv) < 14:
        print("Uso: python scrapingESV.py <motivo> <destino> <data_ida> <data_volta> <qtd_passageiros> <idade1> ... <idade8>")
        return

    motivo = sys.argv[1]
    destino = sys.argv[2]
    data_ida = sys.argv[3]
    data_volta = sys.argv[4]
    qtd_passageiros = int(sys.argv[5])
    idades = sys.argv[6:14]

    data_ida_obj = datetime.strptime(data_ida, "%Y-%m-%d")
    data_volta_obj = datetime.strptime(data_volta, "%Y-%m-%d")

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False)  # headless=True se quiser sem abrir janela
        page = browser.new_page()
        page.goto("https://www.easyseguroviagem.com.br", timeout=60000)

        # Preenchendo o formulário
        page.select_option("#MainContent_Cotador_ddlMotivoDaViagem", motivo)
        page.select_option("#MainContent_Cotador_selContinente", destino)

        page.click("#MainContent_Cotador_daterange")
        time.sleep(1)

        page.locator(f"//td[contains(@class, 'available') and text()='{data_ida_obj.day}']").nth(0).click()
        time.sleep(0.5)
        page.locator(f"//td[contains(@class, 'available') and text()='{data_volta_obj.day}']").nth(1).click()
        time.sleep(0.5)

        page.click(".applyBtn")

        page.select_option("#MainContent_Cotador_selQtdCliente", str(qtd_passageiros))
        for i in range(1, qtd_passageiros + 1):
            idade = idades[i - 1] if i - 1 < len(idades) else "0"
            campo_id = f"#txtIdadePassageiro{i}"
            page.fill(campo_id, idade)

        page.locator("body").click()
        page.click("#MainContent_Cotador_btnComprar")

        if page.is_visible(".divMsgErro"):
            print("[ERRO] Nenhum resultado encontrado.")
        else:
            page.wait_for_selector(".card-produto", timeout=10000)
            cards = page.locator(".card-produto")
            total = cards.count()

            for i in range(total):
                card = cards.nth(i)
                texto = card.text_content().replace("Veja os detalhes da cobertura", "").strip()

                # Simula o link da cobertura (site é sempre o mesmo nesse caso)
                link = "https://www.easyseguroviagem.com.br"
                site = extrair_nome_site(link)

                print(site)
                print(texto)
                print(link)
                print("=====")

        browser.close()

if __name__ == "__main__":
    main()
