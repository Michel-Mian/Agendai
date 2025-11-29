(function(){
  const TYPES = {
    success: { base: 'bg-green-600 text-white', icon: 'fa-check-circle' },
    error: { base: 'bg-red-600 text-white', icon: 'fa-exclamation-circle' },
    warning: { base: 'bg-amber-500 text-white', icon: 'fa-exclamation-triangle' },
    info: { base: 'bg-blue-600 text-white', icon: 'fa-info-circle' },
  }

  function ensureContainer() {
    let c = document.getElementById('toast-container')
    if (!c) {
      c = document.createElement('div')
      c.id = 'toast-container'
      c.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-3'
      document.body.appendChild(c)
    }
    return c
  }

  function show(message, type = 'info', opts = {}) {
    const container = ensureContainer()
    const t = document.createElement('div')
    const { base, icon } = TYPES[type] || TYPES.info
    const duration = opts.duration ?? 3500

    t.className = `${base} shadow-lg rounded-lg px-4 py-3 flex items-start gap-3 w-80 animate-fade-in`;
    t.innerHTML = `
      <i class="fa-solid ${icon} mt-0.5"></i>
      <div class="text-sm leading-5 flex-1">${message}</div>
      <button class="ml-2 text-white/80 hover:text-white" aria-label="Fechar">&times;</button>
    `

    const closeBtn = t.querySelector('button')
    closeBtn.addEventListener('click', () => dismiss(t))

    container.appendChild(t)
    if (duration > 0) {
      setTimeout(() => dismiss(t), duration)
    }
    return t
  }

  function confirm(message, options = {}) {
    const container = ensureContainer()
    const t = document.createElement('div')
    const { base, icon } = TYPES.warning
    const confirmText = options.confirmText || 'Continuar'
    const cancelText = options.cancelText || 'Cancelar'

    t.className = `${base} shadow-lg rounded-lg px-4 py-3 w-96`;
    t.innerHTML = `
      <div class="flex items-start gap-3">
        <i class="fa-solid ${icon} mt-0.5"></i>
        <div class="flex-1">
          <div class="text-sm leading-5 mb-3">${message}</div>
          <div class="flex justify-end gap-2">
            <button class="px-3 py-1.5 rounded bg-white/10 hover:bg-white/20">${cancelText}</button>
            <button class="px-3 py-1.5 rounded bg-white text-amber-700 hover:bg-gray-100">${confirmText}</button>
          </div>
        </div>
      </div>
    `
    container.appendChild(t)

    return new Promise((resolve) => {
      const [cancelBtn, okBtn] = t.querySelectorAll('button')
      cancelBtn.addEventListener('click', () => { dismiss(t); resolve(false) })
      okBtn.addEventListener('click', () => { dismiss(t); resolve(true) })
    })
  }

  function dismiss(node) {
    if (!node) return
    node.style.opacity = '0'
    node.style.transform = 'translateY(-4px)'
    setTimeout(() => node.remove(), 150)
  }

  window.Toast = {
    show,
    success: (m, o) => show(m, 'success', o),
    error: (m, o) => show(m, 'error', o),
    warning: (m, o) => show(m, 'warning', o),
    info: (m, o) => show(m, 'info', o),
    confirm,
  }
})();