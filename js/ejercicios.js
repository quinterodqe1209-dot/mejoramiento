// js/ejercicios.js

// Importamos o asumimos los datos (si trabajas en un solo script o usas <script> en HTML en orden)

// Formateador de moneda en pesos colombianos (Punto 3)
const formatoMoneda = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
});

console.log("=== RESULTADOS DE LA ACTIVIDAD - DÍA 7 ==_\n");

// 1. Producto más caro (Usando reduce, sin for)
const productoMasCaro = datosPrueba.productos.reduce((prev, actual) => 
    (prev.precio > actual.precio) ? prev : actual
);
console.log("1. Producto más caro:");
console.log(`- ${productoMasCaro.nombre} (${formatoMoneda.format(productoMasCaro.precio)})`);


// 2. Total de unidades por categoría (Usando reduce)
const stockPorCategoria = datosPrueba.productos.reduce((acumulador, producto) => {
    acumulador[producto.categoria] = (acumulador[producto.categoria] || 0) + producto.stock;
    return acumulador;
}, {});
console.log("\n2. Total de unidades por categoría:");
console.log(stockPorCategoria);


// 3. Listado de productos con stock menor a 5 (Usando filter)
// (Nota: En nuestros datos de prueba iniciales todos tienen más de 5, 
//  vamos a buscar los menores a 15 para el ejemplo o ajustamos uno en el JSON).
const stockBajo = datosPrueba.productos.filter(p => p.stock < 15);
console.log("\n3. Productos con stock menor a 15 (ejemplo de stock bajo):");
console.log(stockBajo.map(p => `${p.nombre} (Stock: ${p.stock})`));


// 4. Promedio de precio de los productos (Usando reduce)
const sumaPrecios = datosPrueba.productos.reduce((acum, p) => acum + p.precio, 0);
const promedioPrecio = sumaPrecios / datosPrueba.productos.length;
console.log("\n4. Promedio de precio de los productos:");
console.log(formatoMoneda.format(promedioPrecio));