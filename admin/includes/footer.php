<div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        // Fungsi Buka/Tutup Menu
        function toggleMenu() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // 1. Saat tombol menu diklik
        if(menuToggle){
            menuToggle.addEventListener('click', (e) => {
                e.stopPropagation(); // Mencegah klik tembus
                toggleMenu();
            });
        }

        // 2. Saat overlay (area gelap) diklik, menu menutup
        if(overlay){
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
    </script>
</body>
</html>