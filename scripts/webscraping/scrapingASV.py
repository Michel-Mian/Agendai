import sys, io, time
from playwright.sync_api import sync_playwright

# Força saída UTF-8
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

# Validação da quantidade de argumentos
if len(sys.argv) != 12:
    print("Uso: python scrapingASV.py <categoria> <destino> <data-inicio> <data-final> <nome> <email> <telefone> <pax_0_64> <pax_65_70> <pax_71_80> <pax_81_85>")
    sys.exit(1)

categoria, destino, data_inicio, data_final, nome, email, telefone = sys.argv[1:8]
pax_0_64, pax_65_70, pax_71_80, pax_81_85 = map(int, sys.argv[8:12])

inicio = time.time()

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context()
    # Bloqueia imagens e fontes para acelerar
    context.route("**/*.{png,jpg,jpeg,svg,woff,woff2}", lambda route: route.abort())
    page = context.new_page()

    try:
        page.goto("https://www.affinityseguro.com.br/home/", timeout=20000)
        page.wait_for_selector("#categoria", timeout=10000)
    except Exception:
        browser.close()
        sys.exit(1)

    try:
        page.select_option("#categoria", value=categoria)
    except Exception:
        browser.close()
        sys.exit(1)

    try:
        page.wait_for_selector("#destino", timeout=10000)
        page.wait_for_function("""
            (destino) => {
                const select = document.querySelector('#destino');
                return Array.from(select.options).some(opt => opt.value === destino);
            }
        """, arg=destino, timeout=10000)
        page.select_option("#destino", value=destino)
    except Exception:
        browser.close()
        sys.exit(1)

    try:
        page.evaluate(f"""
            () => {{
                const inicioInput = document.querySelector('#data-inicio');
                const finalInput = document.querySelector('#data-final');
                if(inicioInput) inicioInput.removeAttribute('readonly');
                if(finalInput) finalInput.removeAttribute('readonly');
                if(inicioInput) inicioInput.value = '{data_inicio}';
                if(finalInput) finalInput.value = '{data_final}';
            }}
        """)
    except Exception:
        browser.close()
        sys.exit(1)

    def clicar(id, vezes):
        btn = f'button[onclick*="increment(\'{id}\')"]'
        for _ in range(vezes):
            try:
                page.click(btn)
                page.wait_for_timeout(60)
            except Exception:
                break

    try:
        page.click("#showPopoverButton")
        page.wait_for_selector("#popoverContent", state="visible", timeout=6000)
    except Exception:
        browser.close()
        sys.exit(1)

    clicar("inputFirst", pax_0_64)
    clicar("inputSecond", pax_65_70)
    clicar("inputThird", pax_71_80)
    clicar("inputFourth", pax_81_85)

    try:
        page.click("#showPopoverButton")
    except Exception:
        pass

    try:
        page.click("button[onclick*='nextStep(2)']")
    except Exception:
        browser.close()
        sys.exit(1)

    try:
        page.fill("#nomeCotacao", nome)
        page.fill("#emailCotacao", email)
        page.fill("#telefoneCotacao", telefone)
    except Exception:
        browser.close()
        sys.exit(1)

    try:
        page.click("button#submit")
    except Exception:
        browser.close()
        sys.exit(1)

    try:
        page.wait_for_selector(".cardiculo", timeout=7000)
    except Exception:
        browser.close()
        sys.exit(0)

    cards = page.locator(".cardiculo")
    total = cards.count()

    for i in range(total):
        c = cards.nth(i)
        try:
            print("affinityseguro")
            print(c.locator(".card-header h4").inner_text().strip())
            print("Total à vista: R$ " + c.locator(".precoTo strong").inner_text().strip())
            print(c.locator(".list-group-item:nth-child(1) span").first.inner_text().strip())
            print(c.locator(".list-group-item:nth-child(2) span").first.inner_text().strip())
            print(c.locator(".list-group-item:nth-child(3) span").first.inner_text().strip())

            try:
                link = c.locator("a").first.get_attribute("href")
                if link:
                    if not link.startswith("http"):
                        link = "https://www.affinityseguro.com.br" + link
                    print(link)
                else:
                    print("https://www.affinityseguro.com.br")
            except Exception as e:
                print("https://www.affinityseguro.com.br")

            print("=====")
            sys.stdout.flush()
        except Exception:
            pass

    browser.close()