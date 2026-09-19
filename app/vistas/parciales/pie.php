    <footer class="panel__pie">
        <p>&copy; 2026 ZDTecnoc. Todos los derechos reservados.</p>
    </footer>
</div>
<script>
    const botonMenu = document.querySelector('.btn-menu');
    const menuLateral = document.querySelector('#menu-lateral');

    if (botonMenu && menuLateral) {
        const alternarMenu = () => {
            const abierto = menuLateral.classList.toggle('abierto');
            botonMenu.setAttribute('aria-expanded', String(abierto));
        };
        botonMenu.addEventListener('click', alternarMenu);
        botonMenu.addEventListener('keydown', (evento) => {
            if (evento.key === 'Enter' || evento.key === ' ') {
                evento.preventDefault();
                alternarMenu();
            }
        });
    }
</script>
</body>
</html>
