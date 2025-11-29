document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ver-detalhes-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = btn.getAttribute('data-target');
            const detalhes = document.getElementById(targetId);
            if (detalhes) {
                detalhes.classList.toggle('hidden');
            }
        });
    });
});