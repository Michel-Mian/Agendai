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
