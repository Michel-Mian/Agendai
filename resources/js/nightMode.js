// Modo Noturno - JavaScript para controle do tema
window.nightMode = {
  apply: (theme) => {
    const body = document.body
    const html = document.documentElement

    if (theme === "night" || theme === "dark") {
      body.classList.add("night-mode")
      html.classList.add("night-mode")
      console.log("[v0] Modo noturno ativado")
    } else {
      body.classList.remove("night-mode")
      html.classList.remove("night-mode")
      console.log("[v0] Modo claro ativado")
    }

    // Salva a preferência no localStorage
    localStorage.setItem("siteTheme", theme)

    // Dispara evento personalizado para outros componentes
    window.dispatchEvent(
      new CustomEvent("themeChanged", {
        detail: { theme: theme },
      }),
    )
  },

  toggle: function () {
    const currentTheme = localStorage.getItem("siteTheme") || "light"
    const newTheme = currentTheme === "light" ? "dark" : "light"
    this.apply(newTheme)
    return newTheme
  },

  init: function () {
    // Aplica o tema salvo ao carregar a página
    const savedTheme = localStorage.getItem("siteTheme") || "light"
    this.apply(savedTheme)

    // Atualiza o select se existir
    const themeSelect = document.getElementById("theme")
    if (themeSelect) {
      themeSelect.value = savedTheme
    }
  },
}

// Inicializa o modo noturno quando o DOM estiver carregado
document.addEventListener("DOMContentLoaded", () => {
  window.nightMode.init()
})

document.addEventListener("DOMContentLoaded", () => {
  window.nightMode.init()

  const themeSelect = document.getElementById("theme")
  if (themeSelect) {
    themeSelect.addEventListener("change", (e) => {
      const selectedTheme = e.target.value
      window.nightMode.apply(selectedTheme)
    })
  }
})

document.addEventListener("DOMContentLoaded", () => {
  // Inicializa tema salvo
  window.nightMode.init()

  // Pega o ícone toggle
  const themeToggle = document.getElementById("themeToggle")
  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const newTheme = window.nightMode.toggle()

      // Opcional: mudar ícone (toggle-on / toggle-off) conforme tema
      if (newTheme === "dark") {
        themeToggle.classList.remove("fa-toggle-on")
        themeToggle.classList.add("fa-toggle-off")
      } else {
        themeToggle.classList.remove("fa-toggle-off")
        themeToggle.classList.add("fa-toggle-on")
      }
    })
  }
})
