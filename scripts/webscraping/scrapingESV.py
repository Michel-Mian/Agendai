from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from datetime import datetime
import time
import sys
import io
from urllib.parse import urlparse

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def extrair_nome_site(url):
    try:
        host = urlparse(url).netloc.lower()  # ex: www.easyseguroviagem.com.br
        if host.startswith("www."):
            host = host[4:]  # easyseguroviagem.com.br

        # remove os sufixos comuns de domínio (.com.br, .com, .br, .net etc)
        sufixos = ['.com.br', '.com', '.br', '.net', '.org']
        for sufixo in sufixos:
            if host.endswith(sufixo):
                host = host[:-len(sufixo)]

        return host  # retorna easyseguroviagem
    except:
        return "site"

url = "https://www.easyseguroviagem.com.br"
print(extrair_nome_site(url))  # Saída: easyseguroviagem

def clicar_data(driver, wait, data_str):
    """
    Clica na data do calendário.
    data_str no formato 'YYYY-MM-DD'.
    """
    data = datetime.strptime(data_str, "%Y-%m-%d")
    dia = data.day

    # O calendário pode mostrar dois meses, precisamos achar o botão do dia ativo.
    # Vamos buscar o botão que contenha o texto do dia, esteja habilitado (classe 'available') e visível.
    xpath = f"//td[contains(@class, 'available') and not(contains(@class, 'disabled')) and text()='{dia}']"

    # Espera e clica
    elem = wait.until(EC.element_to_be_clickable((By.XPATH, xpath)))
    elem.click()

def main():
    if len(sys.argv) < 14:
        print("Uso: python scrapingESV.py <motivo> <destino> <data_ida> <data_volta> <qtd_passageiros> <idade1> ... <idade8>")
        return

    motivo = sys.argv[1]
    destino = sys.argv[2]
    data_ida = sys.argv[3]
    data_volta = sys.argv[4]
    qtd_passageiros = sys.argv[5]
    idades = sys.argv[6:14]  # idade1 até idade8

    options = webdriver.ChromeOptions()
    options.add_argument("--start-maximized")
    options.add_argument("--disable-gpu")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--no-sandbox")
    options.add_argument("--headless=new")
    options.add_argument("--window-size=1920,1080")

    driver = webdriver.Chrome(options=options)
    wait = WebDriverWait(driver, 20)

    try:
        driver.get("https://www.easyseguroviagem.com.br")

        # Motivo
        Select(wait.until(EC.presence_of_element_located((By.ID, "MainContent_Cotador_ddlMotivoDaViagem")))).select_by_value(motivo)

        # Destino
        Select(wait.until(EC.presence_of_element_located((By.ID, "MainContent_Cotador_selContinente")))).select_by_value(destino)

        # Abrir calendário
        data_input = wait.until(EC.element_to_be_clickable((By.ID, "MainContent_Cotador_daterange")))
        data_input.click()
        time.sleep(2)

        # Clicar nas datas no calendário para ida e volta
        clicar_data(driver, wait, data_ida)
        time.sleep(1)
        clicar_data(driver, wait, data_volta)
        time.sleep(1)

        # Clicar no botão aplicar (fechar calendário)
        try:
            aplicar = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, ".applyBtn")))
            aplicar.click()
            time.sleep(2)
        except:
            pass

        # Quantidade de passageiros
        Select(wait.until(EC.presence_of_element_located((By.ID, "MainContent_Cotador_selQtdCliente")))).select_by_value(qtd_passageiros)

        # Preencher idades
        for i in range(1, int(qtd_passageiros) + 1):
            campo_id = f"txtIdadePassageiro{i}"
            idade = idades[i - 1] if i - 1 < len(idades) else "0"
            try:
                campo = wait.until(EC.presence_of_element_located((By.ID, campo_id)))
                campo.clear()
                campo.send_keys(idade)
            except:
                print(f"[ERRO] Campo de idade {campo_id} não encontrado.")
                continue

        # Clicar em "Comprar"
        botao = wait.until(EC.element_to_be_clickable((By.ID, "MainContent_Cotador_btnComprar")))
        botao.click()

        # Captura os cards
        for i in range(1, 5):
            try:
                card = driver.find_element(By.CSS_SELECTOR, f"#ctl01 > div.divMainContent > div.container > div.div-m > div.divMainCotacao > div.cards > div:nth-child({i})")
                texto = card.text

                # Remove "VEJA OS DETALHES DA COBERTURA"
                texto = texto.replace("VEJA OS DETALHES DA COBERTURA", "").strip()

                # Captura o link
                try:
                    link = card.find_element(By.CSS_SELECTOR, "a.btn-cobertura").get_attribute("href")
                except:
                    link = "https://www.easyseguroviagem.com.br"

                site = extrair_nome_site(link)

                print(site)
                print(texto)
                print(link)
                print("=====")
            except:
                continue

    except Exception as e:
        print("Erro durante o scraping:", e)

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
