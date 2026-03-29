// ============================================
// FILE: assets/js/script.js
// Fungsi: Interaktivitas website Buku Tamu Digital
// ============================================

document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // SIDEBAR TOGGLE (untuk mobile)
    // =========================================
    const hamburger = document.querySelector('.hamburger');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (hamburger && sidebar) {
        hamburger.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('show');
        });

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }
    }

    // =========================================
    // AUTO-HIDE ALERT setelah 4 detik
    // =========================================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.4s ease';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });

    // =========================================
    // KONFIRMASI HAPUS
    // =========================================
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Apakah kamu yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });

    // =========================================
    // MODAL
    // =========================================
    // Buka modal
    document.querySelectorAll('[data-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('show');
        });
    });

    // Tutup modal
    document.querySelectorAll('.modal-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const modal = this.closest('.modal-overlay');
            if (modal) modal.classList.remove('show');
        });
    });

    // Klik di luar modal untuk tutup
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });

    // =========================================
    // ANIMASI COUNTER STATISTIK
    // =========================================
    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-target')) || 0;
        const duration = 1500;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                el.textContent = target;
                clearInterval(timer);
            } else {
                el.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Gunakan IntersectionObserver agar animasi hanya berjalan saat terlihat
    const counterEls = document.querySelectorAll('[data-target]');
    if (counterEls.length > 0) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counterEls.forEach(el => observer.observe(el));
    }

    // =========================================
    // NAVBAR SCROLL EFFECT
    // =========================================
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // =========================================
    // SMOOTH SCROLL UNTUK ANCHOR LINKS
    // =========================================
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // =========================================
    // AKTIFKAN LINK SIDEBAR SESUAI HALAMAN
    // =========================================
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-nav a').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href').split('/').pop())) {
            link.classList.add('active');
        }
    });

    // =========================================
    // PREVIEW TEXTAREA KARAKTER
    // =========================================
    const pesanTextarea = document.getElementById('pesan');
    const charCount = document.getElementById('char-count');
    if (pesanTextarea && charCount) {
        pesanTextarea.addEventListener('input', function () {
            charCount.textContent = this.value.length;
        });
    }

    // =========================================
    // TOGGLE PASSWORD VISIBILITY
    // =========================================
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const inputId = this.getAttribute('data-target');
            const input = document.getElementById(inputId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🙈';
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            }
        });
    });

});
