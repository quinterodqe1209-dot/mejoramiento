const moneda = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0,
});

let graficoVentas;
let graficoCategorias;
let graficoClientes;

async function dibujarGraficos() {
    const desde = document.querySelector('#desde').value;
    const hasta = document.querySelector('#hasta').value;
    const respuesta = await fetch(`api/graficos.php?desde=${encodeURIComponent(desde)}&hasta=${encodeURIComponent(hasta)}`, {
        credentials: 'same-origin',
    });
    const datos = await respuesta.json();
    if (!respuesta.ok) throw new Error(datos.error || 'No fue posible cargar los gráficos.');

    graficoVentas?.destroy();
    graficoVentas = new Chart(document.querySelector('#grafico-ventas'), {
        type: 'bar',
        data: {
            labels: datos.ventasMes.etiquetas,
            datasets: [{
                label: 'Ventas por mes',
                data: datos.ventasMes.valores,
                backgroundColor: '#008A00',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { callback: valor => moneda.format(valor) } } },
        },
    });

    graficoCategorias?.destroy();
    graficoCategorias = new Chart(document.querySelector('#grafico-categorias'), {
        type: 'doughnut',
        data: {
            labels: datos.categorias.etiquetas,
            datasets: [{
                label: 'Ventas por categoría',
                data: datos.categorias.valores,
                backgroundColor: ['#008A00', '#15506B', '#D97700', '#7AAE62', '#B3261E'],
            }],
        },
        options: { responsive: true, maintainAspectRatio: false },
    });

    graficoClientes?.destroy();
    graficoClientes = new Chart(document.querySelector('#grafico-clientes'), {
        type: 'line',
        data: {
            labels: datos.clientes.etiquetas,
            datasets: [{
                label: 'Compra acumulada',
                data: datos.clientes.valores,
                borderColor: '#15506B',
                backgroundColor: '#DDEAF0',
                fill: true,
                tension: 0.25,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { callback: valor => moneda.format(valor) } } },
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.querySelector('#filtro-graficos');
    formulario?.addEventListener('submit', evento => {
        evento.preventDefault();
        dibujarGraficos().catch(error => {
            document.querySelector('#error-graficos').textContent = error.message;
        });
    });
    dibujarGraficos().catch(error => {
        document.querySelector('#error-graficos').textContent = error.message;
    });
});
