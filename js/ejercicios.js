// js/ejercicios.js

// Formateador de moneda en pesos colombianos
const formatoMoneda = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
});

console.log("=== RESULTADOS DE LA ACTIVIDAD - DÍA 7 ==_\n");

// 1. Producto más caro (Día 7)
const productoMasCaro = datosPrueba.productos.reduce((prev, actual) => 
    (prev.precio > actual.precio) ? prev : actual
);
console.log("1. Producto más caro:");
console.log(`- ${productoMasCaro.nombre} (${formatoMoneda.format(productoMasCaro.precio)})`);

// 2. Total de unidades por categoría (Día 7)
const stockPorCategoria = datosPrueba.productos.reduce((acumulador, producto) => {
    acumulador[producto.categoria] = (acumulador[producto.categoria] || 0) + producto.stock;
    return acumulador;
}, {});
console.log("\n2. Total de unidades por categoría:");
console.log(stockPorCategoria);

// 3. Listado de productos con stock menor a 15 (Día 7)
const stockBajo = datosPrueba.productos.filter(p => p.stock < 15);
console.log("\n3. Productos con stock menor a 15:");
console.log(stockBajo.map(p => `${p.nombre} (Stock: ${p.stock})`));

// 4. Promedio de precio de los productos (Día 7)
const sumaPrecios = datosPrueba.productos.reduce((acum, p) => acum + p.precio, 0);
const promedioPrecio = sumaPrecios / datosPrueba.productos.length;
console.log("\n4. Promedio de precio de los productos:");
console.log(formatoMoneda.format(promedioPrecio));


// ==========================================
// --- ACTIVIDAD DÍA 8 (Todo integrado y seguro) ---
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
    const tbody = document.querySelector("tbody");

    // --- Punto 1: Pintar la tabla de productos dinámicamente ---
    if (tbody) {
        tbody.innerHTML = "";
        datosPrueba.productos.forEach(prod => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>PROD-00${prod.id}</td>
                <td>${prod.nombre}</td>
                <td>${prod.categoria}</td>
                <td>${formatoMoneda.format(prod.precio)}</td>
                <td>${prod.stock}</td>
            `;
            tbody.appendChild(fila);
        });
    }

    // --- Punto 2: Buscador en vivo con input y filter ---
    const inputBuscador = document.querySelector("input[type='text']") || document.querySelector("#buscador");
    if (inputBuscador && tbody) {
        inputBuscador.addEventListener("input", (e) => {
            const texto = e.target.value.toLowerCase();
            const filtrados = datosPrueba.productos.filter(prod => 
                prod.nombre.toLowerCase().includes(texto) || 
                prod.categoria.toLowerCase().includes(texto)
            );

            tbody.innerHTML = "";
            filtrados.forEach(prod => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>PROD-00${prod.id}</td>
                    <td>${prod.nombre}</td>
                    <td>${prod.categoria}</td>
                    <td>${formatoMoneda.format(prod.precio)}</td>
                    <td>${prod.stock}</td>
                `;
                tbody.appendChild(fila);
            });
        });
    }

    // --- Punto 3: Menú lateral con clic y tecla Enter ---
    const botonMenu = document.querySelector(".menu-btn") || document.querySelector("aside button") || document.querySelector("[aria-label='Navegación principal']");
    if (botonMenu) {
        const alternarMenu = () => {
            const menu = document.querySelector(".panel__menu");
            if (menu) {
                menu.classList.toggle("abierto");
            }
        };

        botonMenu.addEventListener("click", alternarMenu);
        botonMenu.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                alternarMenu();
            }
        });
    }

    // --- Punto 4: Validación de formulario sin alert ---
    const formulario = document.querySelector("form");
    if (formulario) {
        // SOLUCIÓN: Desactivamos la validación nativa del navegador desde JS sin tocar el HTML
        formulario.setAttribute("novalidate", "true");

        formulario.addEventListener("submit", (e) => {
            e.preventDefault();
            let esValido = true;

            document.querySelectorAll(".error-validacion").forEach(el => el.remove());

            // 1. Validar Nombre
            const inputNombre = formulario.querySelector("input[name='nombre']") || formulario.querySelector("input[type='text']");
            if (inputNombre && inputNombre.value.trim().length < 3) {
                mostrarError(inputNombre, "El nombre debe tener al menos 3 caracteres.");
                esValido = false;
            }

            // 2. Validar Precio
            const inputPrecio = formulario.querySelector("input[name='precio']") || formulario.querySelector("input[type='number']");
            if (inputPrecio && (inputPrecio.value === "" || Number(inputPrecio.value) <= 0)) {
                mostrarError(inputPrecio, "El precio debe ser mayor a cero.");
                esValido = false;
            }

            // 3. Validar Stock
            const inputStock = formulario.querySelector("input[name='stock']");
            if (inputStock && (inputStock.value === "" || Number(inputStock.value) < 0 || !Number.isInteger(Number(inputStock.value)))) {
                mostrarError(inputStock, "El stock debe ser un número entero no negativo.");
                esValido = false;
            }

            // 4. Validar Categoría
            const selectCategoria = formulario.querySelector("select");
            if (selectCategoria && selectCategoria.value === "") {
                mostrarError(selectCategoria, "Debe seleccionar una categoría.");
                esValido = false;
            }

            if (esValido) {
                console.log("Formulario validado con éxito. Sin alertas.");
                formulario.submit(); 
            }
        });
    }

    function mostrarError(elemento, mensaje) {
        const error = document.createElement("span");
        error.className = "error-validacion";
        error.style.color = "#ff4d4d";
        error.style.fontSize = "0.85rem";
        error.style.display = "block";
        error.style.marginTop = "4px";
        error.innerText = mensaje;
        elemento.insertAdjacentElement("afterend", error);
    }
});