import requests
from bs4 import Beautifulsoup

pagina = requests.get('https://quotes.toscrape.com/')
dados_pagina = Beautifulsoup(pagina.text, 'html.parser')

print(dados_pagina.prettify())