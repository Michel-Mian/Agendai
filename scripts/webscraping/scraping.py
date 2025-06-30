from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
import sys
import time

def main():
    if len(sys.argv) < 9:
        print("Uso: python scraping.py <motivo> <destino> <data_ida> <data_volta> <qtd_passageiros> <idade1> <idade2> <idade3>")
        return

    motivo = sys.argv[1]  # valor 1 a 4
    destino = sys.argv[2]  # valor 1 a 11
    data_ida = sys.argv[3]  # formato: DD/MM/AAAA
    data_volta = sys.argv[4]  # formato: DD/MM/AAAA
    qtd_passageiros = sys.argv[5]
    idade1 = sys.argv[6]
    idade2 = sys.argv[7]
    idade3 = sys.argv[8]

    options = webdriver.ChromeOptions()
    options.add_argument("--start-maximized")
    options.add_argument("--disable-gpu")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--no-sandbox")

    driver = webdriver.Chrome(options=options)
    wait = WebDriverWait(driver, 20)

    try:
        driver.get("https://www.easyseguroviagem.com.br")
        print("[LOG] Página aberta")

        # motivo
        motivo_select = wait.until(EC.presence_of_element_located((By.ID, "MainContent_Cotador_ddlMotivoDaViagem")))
        print("[LOG] Motivo encontrado")
        Select(motivo_select).select_by_value(motivo)
        print("[LOG] Motivo selecionado")

        # destino
        destino_select = wait.until(EC.presence_of_element_located((By.ID, "MainContent_Cotador_selContinente")))
        print("[LOG] Destino encontrado")
        Select(destino_select).select_by_value(destino)
        print("[LOG] Destino selecionado")

        # data
        data_input = wait.until(EC.element_to_be_clickable((By.ID, "MainContent_Cotador_daterange")))
        print("[LOG] Campo data encontrado")
        data_input.click()
        print("[LOG] Calendário aberto")
        time.sleep(2)

        # clique na data de ida e volta (ajuste os seletores se necessário)
        data_ida_element = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, ".available[data-title='r5c1']")))
        data_ida_element.click()
        print("[LOG] Data de ida clicada")

        data_volta_element = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, ".available[data-title='r5c6']")))
        data_volta_element.click()
        print("[LOG] Data de volta clicada")

        driver.execute_script("document.querySelector('.applyBtn').click()")
        print("[LOG] Datas aplicadas")

        # qtd passageiros
        qtd_input = wait.until(EC.presence_of_element_located((By.ID, "MainContent_Cotador_selQtdCliente")))
        print("[LOG] Campo de passageiros encontrado")
        Select(qtd_input).select_by_value(qtd_passageiros)
        print("[LOG] Passageiros preenchido")

        # idades
        wait.until(EC.presence_of_element_located((By.ID, "txtIdadePassageiro1"))).send_keys(idade1)
        wait.until(EC.presence_of_element_located((By.ID, "txtIdadePassageiro2"))).send_keys(idade2)
        wait.until(EC.presence_of_element_located((By.ID, "txtIdadePassageiro3"))).send_keys(idade3)
        print("[LOG] Idades preenchidas")

        # botão
        botao = wait.until(EC.element_to_be_clickable((By.ID, "MainContent_Cotador_btnComprar")))
        print("[LOG] Botão encontrado")
        botao.click()
        print("[LOG] Botão clicado")

        # aguardar resultados (ajustar se necessário)
        time.sleep(5)
        print("[LOG] Aguardando resultados...")

        # exemplo de extração
        titulo = driver.find_element(By.ID, "MainContent_rptProdutos_txtTituloDoProduto_0").text
        preco = driver.find_element(By.ID, "MainContent_rptProdutos_txtTotalAVista_0").text
        cobertura = driver.find_element(By.CSS_SELECTOR, "#ctl01 > div.divMainContent > div.container > div.div-m > div.divMainCotacao > div.cards > div:nth-child(1) > div > div.divCardBeneficio > div").text
        link = driver.find_element(By.ID, "MainContent_rptProdutos_btnComprar_0").get_attribute("href")

        print(titulo)
        print(preco)
        print(cobertura)
        print(link)

    except Exception as e:
        print("Erro durante o scraping:", e)

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
