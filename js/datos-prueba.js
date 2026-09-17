document.addEventListener("DOMContentLoaded", () => {
    fetch('obtener-productos.php')
        .then(response => response.json())
        .then(productos => {
            const tbody = document.querySelector('tbody');
            if (!tbody) return;
            
            tbody.innerHTML = '';

            productos.forEach(producto => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${producto.id}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.categoria}</td>
                    <td>$${producto.precio}</td>
                    <td>${producto.stock}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error al cargar los productos:', error));
});