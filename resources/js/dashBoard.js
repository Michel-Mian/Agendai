document.addEventListener('DOMContentLoaded', function() {
    let graficoCriado = false;
    let chartInstance = null;

    document.getElementById('modal-currency').onclick = function() {
        document.getElementById('currency-modal').classList.remove('hidden');
        const dias = document.getElementById('conversion-period').value || 60;

        if (chartInstance) {
            chartInstance.destroy();
        }

        fetch(`/dashboard/historico?dias=${dias}`)
            .then(response => response.json())
            .then(json => {
                const ctx = document.getElementById('graficoMoeda').getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: json.labels,
                        datasets: [{
                            label: 'USD/BRL',
                            data: json.data,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37,99,235,0.1)',
                            tension: 0.3
                        }]
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Valor: ' + Number(context.parsed.y).toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return Number(value).toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
            });
    };

    document.getElementById('conversion-period').addEventListener('input', function() {
        const dias = this.value || 60;
        fetch(`/dashboard/historico?dias=${dias}`)
            .then(response => response.json())
            .then(json => {
                if (chartInstance) {
                    chartInstance.data.labels = json.labels;
                    chartInstance.data.datasets[0].data = json.data;
                    chartInstance.update();
                }
            });
    });

    // Fechar modal
    document.getElementById('close-currency-modal').onclick = function() {
        document.getElementById('currency-modal').classList.add('hidden');
    };
    // Fechar ao clicar fora do modal
    document.getElementById('currency-modal').onclick = function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    };
});
