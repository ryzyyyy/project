// Show/hide password toggle & forgot password modal
window.addEventListener('DOMContentLoaded', function () {
    try {
        var pwField = document.getElementById('password-field');
        var toggle = document.getElementById('toggle-password');
        if (pwField && toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                // Toggle password visibility
                // Use emoji for visibility toggle
                // Fallback if emoji not supported
                if (pwField.type === 'password') {
                    pwField.type = 'text';
                    toggle.textContent = '🙈'; // U+1F648
                } else {
                    pwField.type = 'password';
                    toggle.textContent = '👁️'; // U+1F441 U+FE0F
                }
            });
        }

        // Forgot password modal
        var forgotLink = document.getElementById('forgot-password-link');
        var forgotModal = document.getElementById('forgot-password-modal');
        var closeForgot = document.getElementById('close-forgot-modal');
        if (forgotLink && forgotModal && closeForgot) {
            forgotLink.addEventListener('click', function (e) {
                e.preventDefault();
                forgotModal.classList.add('show');
            });
            closeForgot.addEventListener('click', function () {
                forgotModal.classList.remove('show');
            });
            forgotModal.addEventListener('click', function (e) {
                if (e.target === forgotModal) forgotModal.classList.remove('show');
            });
        }
    } catch (err) {
        // Jika error, tampilkan di console
        console.error('Login enhancement error:', err);
    }
});
// sistem_surat/assets/js/main.js

// Toggle Sidebar (mobile)
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
}

// Modal functions
function openModal(id) {
    document.getElementById(id).classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

// Close modal on overlay click
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('show');
    }
});

// Confirm delete
function confirmDelete(url, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus "${name}"?`)) {
        window.location.href = url;
    }
}

// Auto-dismiss alert
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert[data-dismiss]');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.4s';
            setTimeout(() => alert.remove(), 400);
        }, 3000);
    });

    // Set active menu
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-menu a').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
});

// Preview uploaded file
function previewFile(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    if (file && preview) {
        preview.textContent = file.name;
    }
}

// Format tanggal Indonesia
function formatTanggal(tanggal) {
    const bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const d = new Date(tanggal);
    return d.getDate() + ' ' + bulan[d.getMonth() + 1] + ' ' + d.getFullYear();
}

// AJAX search (opsional)
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#' + tableId + ' tbody tr');

    rows.forEach(function (row) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
