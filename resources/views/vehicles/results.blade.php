<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <span id="total-vehicles">0</span> veículos encontrados
        </h2>
        <div class="text-sm text-gray-600">
            <i class="fas fa-info-circle mr-1"></i>
            Role para ver mais opções
        </div>
    </div>
    
    <!-- Grid de Veículos -->
    <div id="vehicles-grid" class="grid grid-cols-1 gap-6">
        <!-- Cards serão inseridos aqui via JavaScript -->
    </div>
    
    <!-- Botão Ver mais -->
    <div id="vehicles-load-more-wrap" class="mt-6 text-center hidden">
        <button id="vehicles-load-more" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Ver mais
        </button>
    </div>
    
    <!-- Mensagem de sem resultados -->
    <div id="no-vehicles-message" class="hidden text-center py-12">
        <i class="fas fa-car text-gray-300 text-6xl mb-4"></i>
        <p class="text-xl text-gray-600">Nenhum veículo encontrado para os critérios selecionados.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const GRID_ID = 'vehicles-grid';
    const LOAD_MORE_ID = 'vehicles-load-more';
    const LOAD_MORE_WRAP = 'vehicles-load-more-wrap';
    const PAGE_SIZE = 6;

    function applyPagination() {
        const grid = document.getElementById(GRID_ID);
        if (!grid) return;

        const cards = Array.from(grid.children);
        if (cards.length === 0) {
            document.getElementById('vehicles-load-more-wrap').classList.add('hidden');
            return;
        }

        // Se cards ainda não tiverem sido "paginated", inicializa atributos
        let visibleCount = parseInt(grid.getAttribute('data-visible') || '0', 10);
        if (visibleCount === 0) {
            visibleCount = PAGE_SIZE;
            grid.setAttribute('data-visible', visibleCount);
        }

        // Esconder todos além do visibleCount
        cards.forEach((c, idx) => {
            if (idx < visibleCount) {
                c.classList.remove('hidden');
            } else {
                c.classList.add('hidden');
            }
        });

        // Mostrar/ocultar botão "Ver mais"
        const loadMoreWrap = document.getElementById(LOAD_MORE_WRAP);
        if (cards.length > visibleCount) {
            loadMoreWrap.classList.remove('hidden');
        } else {
            loadMoreWrap.classList.add('hidden');
        }

        // Atualizar contador
        const totalSpan = document.getElementById('total-vehicles');
        if (totalSpan) totalSpan.textContent = cards.length;
    }

    function showMore() {
        const grid = document.getElementById(GRID_ID);
        if (!grid) return;
        const cards = Array.from(grid.children);
        let visibleCount = parseInt(grid.getAttribute('data-visible') || '0', 10);
        visibleCount = Math.min(visibleCount + PAGE_SIZE, cards.length);
        grid.setAttribute('data-visible', visibleCount);
        applyPagination();

        // Se já mostrou todos, esconder botão
        if (visibleCount >= cards.length) {
            document.getElementById(LOAD_MORE_WRAP).classList.add('hidden');
        }
    }

    // Ligar evento ao botão
    document.getElementById(LOAD_MORE_ID).addEventListener('click', function(e) {
        e.preventDefault();
        showMore();
    });

    // Observar alterações no container para aplicar paginação quando novos cards chegarem
    const target = document.getElementById(GRID_ID);
    if (target) {
        const observer = new MutationObserver((mutationsList, observer) => {
            // Pequeno debounce
            setTimeout(() => applyPagination(), 50);
        });
        observer.observe(target, { childList: true, subtree: false });
        // Initial run in case items already present
        applyPagination();
    }
});
</script>
