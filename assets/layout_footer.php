        </div> <!-- END PAGE CONTENT (flex-1 overflow-auto p-6) -->
    </div> <!-- END MAIN CONTENT AREA -->
</div> <!-- END FLEX CONTAINER (sidebar + main) -->

<!-- Tailwind Script -->
<script>
    // Tailwind Script (untuk dynamic class)
    function initTailwind() {
        // Jika ada kebutuhan konfigurasi tambahan
    }
    
    // Toggle Sidebar (untuk mobile)
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('-translate-x-full');
        }
    }

    // Close sidebar when clicking outside (mobile)
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const menuBtn = document.querySelector('[onclick="toggleSidebar()"]');
        
        if (window.innerWidth < 1024 && 
            sidebar && 
            !sidebar.contains(e.target) && 
            !menuBtn.contains(e.target)) {
            sidebar.classList.add('-translate-x-full');
        }
    });

    // Search functionality (contoh sederhana)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const keyword = this.value.trim();
                if (keyword !== '') {
                    alert('Mencari: ' + keyword + '\n(Fitur pencarian global akan diimplementasikan)');
                    // window.location.href = `...?search=${keyword}`;
                }
            }
        });
    }

    // Initialize
    window.onload = function() {
        // Tambahkan class responsive untuk sidebar di mobile
        const sidebar = document.getElementById('sidebar');
        if (sidebar && window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
        }
    };
</script>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>

</body>
</html>