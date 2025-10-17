// Modo Noturno - JavaScript para controle do tema e atualização visual do switch
window.nightMode = {
    // Referência aos elementos visuais do switch
    themeSwitchButton: null,
    switchIndicator: null,

    // Função central para aplicar o tema e atualizar o estado visual
    apply: (theme) => {
        const body = document.body;
        const html = document.documentElement;
        const isNightMode = (theme === "night" || theme === "dark");

        if (isNightMode) {
            body.classList.add("night-mode");
            html.classList.add("night-mode");
        } else {
            body.classList.remove("night-mode");
            html.classList.remove("night-mode");
        }

        // =======================================================
        // DEFINIÇÕES DE CORES (Ajustadas para Borda Branca no Dark Mode)
        // =======================================================
        
        // MODO CLARO (Mantidas)
        const colorNormalContour = "#f9fafb";
        const colorNormalBackground = "#e2e8f0";
        const colorNormalIconActive = "oklch(37.3% 0.034 259.733)"; 
        const colorNormalIconInactive = "#737373";

        // MODO ESCURO 
        const colorDarkBackground = "#0d1117";      // Fundo principal
        const colorDarkContour = "#ffffff";         // Borda: BRANCO PURO (SOLICITADO)
        const colorDarkIndicatorBackground = "#21262d"; // Fundo de cards (para o indicador)
        const colorDarkIconActive = "#f0f6fc";      // Texto principal (Lua ativa)
        const colorDarkIconInactive = "#7d8590";    // Texto secundário (Sol inativo)
        
        // Translação ajustada para w-16 (64px) e w-5 (20px)
        const TRANSLATE_DISTANCE = "41px"; 

        
        // --- ATUALIZAÇÃO VISUAL DO SWITCH ---
        if (window.nightMode.themeSwitchButton && window.nightMode.switchIndicator) {
            
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');

            if (isNightMode) {
                // ================== MODO ESCURO ==================
                // Translação
                window.nightMode.switchIndicator.style.transform = `translateX(${TRANSLATE_DISTANCE})`;
                
                window.nightMode.themeSwitchButton.style.backgroundColor = colorDarkBackground + ' !important'; 
                window.nightMode.themeSwitchButton.style.borderColor = colorDarkContour + ' !important'; 

                window.nightMode.switchIndicator.style.backgroundColor = colorDarkIndicatorBackground + ' !important'; 
                window.nightMode.switchIndicator.style.borderColor = colorDarkContour + ' !important';

                if (sunIcon) sunIcon.style.color = colorDarkIconInactive + ' !important';
                if (moonIcon) moonIcon.style.color = colorDarkIconActive + ' !important';
                
            } else {
                // ================== MODO CLARO (NORMAL) ==================
                // Translação
                window.nightMode.switchIndicator.style.transform = `translateX(0px)`;
                
                // Switch Principal
                window.nightMode.themeSwitchButton.style.backgroundColor = colorNormalBackground; 
                window.nightMode.themeSwitchButton.style.borderColor = colorNormalContour;

                // Indicador Deslizante
                window.nightMode.switchIndicator.style.backgroundColor = '#ffffff'; 
                window.nightMode.switchIndicator.style.borderColor = colorNormalContour;
                
                // Cores dos ícones
                if (sunIcon) sunIcon.style.color = colorNormalIconActive; 
                if (moonIcon) moonIcon.style.color = colorNormalIconInactive; 
            }
        }
        
        // Salva a preferência no localStorage
        localStorage.setItem("siteTheme", theme);

        // Dispara evento personalizado
        window.dispatchEvent(
            new CustomEvent("themeChanged", {
                detail: { theme: theme },
            }),
        );
    },

    // Função para alternar o tema (chamada no clique)
    toggle: function () {
        const currentTheme = localStorage.getItem("siteTheme") || "light";
        const newTheme = currentTheme === "light" ? "dark" : "light";
        this.apply(newTheme);
        return newTheme;
    },

    // Inicializa o tema ao carregar a página
    init: function () {
        // Pega as referências DOM e armazena no objeto nightMode
        window.nightMode.themeSwitchButton = document.getElementById("themeSwitch");
        window.nightMode.switchIndicator = document.getElementById("switch-indicator");

        // Aplica o tema salvo ao carregar a página
        const savedTheme = localStorage.getItem("siteTheme") || "light";
        this.apply(savedTheme);

        // Atualiza o select de tema se existir
        const themeSelect = document.getElementById("theme");
        if (themeSelect) {
            themeSelect.value = savedTheme;
        }

        // Adiciona o listener de clique ao novo botão switch
        if (window.nightMode.themeSwitchButton) {
            window.nightMode.themeSwitchButton.addEventListener("click", () => {
                window.nightMode.toggle();
            });
        }
        
        // Adiciona listener para o select de tema (se existir)
        if (themeSelect) {
            themeSelect.addEventListener("change", (e) => {
                const selectedTheme = e.target.value;
                window.nightMode.apply(selectedTheme);
            });
        }
    },
};

// Inicializa o modo noturno quando o DOM estiver carregado
document.addEventListener("DOMContentLoaded", () => {
    window.nightMode.init();
});