from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
import sys
import io

# Forçar UTF-8 na saída
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

options = Options()
options.add_argument("--headless")
options.add_argument("--disable-gpu")
options.add_argument("--window-size=1920,1080")

driver = webdriver.Chrome(options=options)

try:
    url = "https://hellosafe.com.br/seguro-viagem"
    driver.get(url)

    WebDriverWait(driver, 15).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, "div.tw-relative.tw-grid-cols-10.tw-bg-white.tw-grid"))
    )

    html = driver.page_source
    soup = BeautifulSoup(html, "html.parser")

    seguros = soup.select("div.tw-relative.tw-grid-cols-10.tw-bg-white.tw-grid")

    for seguro in seguros:
        # Nome e link do seguro
        link_tag = seguro.select_one("a[href]")
        link = link_tag['href'] if link_tag else "N/A"
        nome_tag = seguro.select_one("a > div[data-testid='logo-subtitle']")
        nome = nome_tag.get_text(strip=True) if nome_tag else "N/A"

        # Avaliação
        avaliacao_tag = seguro.select_one("div.tw-text-cultured-900.tw-flex.items-center.tw-font-bold.tw-text-2xl")
        avaliacao = avaliacao_tag.get_text(strip=True) if avaliacao_tag else "N/A"

        # Preço — pode estar em "div" com texto que contenha "Preço" ou similar, vamos procurar:
        preco = "N/A"
        # Algumas vezes o preço pode estar na div de benefícios, vamos buscar por texto próximo de preço
        divs_texto = seguro.find_all("div", string=lambda text: text and "R$" in text or "Preço" in text)
        if divs_texto:
            preco = divs_texto[0].get_text(strip=True)

        # Cobertura e info de bagagem
        # No HTML, os benefícios principais estão em "div.tw-flex.tw-gap-2"
        coberturas = []
        infos_bagagem = []
        beneficios = seguro.select("div.tw-flex.tw-gap-2")
        for beneficio in beneficios:
            textos = beneficio.stripped_strings
            textos_lista = list(textos)
            # Aqui tentamos separar os itens de cobertura e bagagem pelo texto
            texto_concatenado = " ".join(textos_lista).lower()
            if "bagagem" in texto_concatenado:
                infos_bagagem.append(" ".join(textos_lista))
            else:
                coberturas.append(" ".join(textos_lista))

        print(f"Seguro: {nome}")
        print(f"Avaliação: {avaliacao}")
        print(f"Preço: {preco}")
        print(f"Link: {link}")
        print("Cobertura:")
        for c in coberturas:
            print(f" - {c}")
        print("Informações de bagagem:")
        for b in infos_bagagem:
            print(f" - {b}")
        print("-" * 50)

finally:
    driver.quit()
