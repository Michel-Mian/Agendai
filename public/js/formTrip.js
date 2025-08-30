document.addEventListener('DOMContentLoaded', function () {
    const steps = document.querySelectorAll('.form-step');
    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    const indicators = document.querySelectorAll('.step-indicator');
    let currentStep = 0;

    function showStep(index) {
        steps.forEach((step, i) => step.classList.toggle('active', i === index));
        indicators.forEach((ind, i) => ind.classList.toggle('active', i <= index));
    }

    nextBtns.forEach(btn => btn.addEventListener('click', () => {
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    }));

    prevBtns.forEach(btn => btn.addEventListener('click', () => {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    }));

    // Preferências e seguro viagem seleção visual
    document.querySelectorAll('.pref-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.pref-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
        });
    });
    document.querySelectorAll('.insurance-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.insurance-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
        });
    });

    showStep(currentStep);
});