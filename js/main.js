document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const sidebar = document.querySelector('.sidebar');
    const companionToggle = document.querySelector('.dashboard-companion-toggle');
    const companionClose = document.querySelector('.dashboard-companion-close');
    const avatarWrapper = document.querySelector('.avatar-wrapper');
    const dropdown = document.getElementById('topDropdown');

    let sidebarBackdrop = document.querySelector('.sidebar-backdrop');
    if (!sidebarBackdrop && sidebar) {
        sidebarBackdrop = document.createElement('div');
        sidebarBackdrop.className = 'sidebar-backdrop';
        document.body.appendChild(sidebarBackdrop);
    }

    let mobileBackdrop = document.querySelector('.mobile-menu-backdrop');
    if (!mobileBackdrop) {
        mobileBackdrop = document.createElement('div');
        mobileBackdrop.className = 'mobile-menu-backdrop';
        document.body.appendChild(mobileBackdrop);
    }

    const closeMenu = () => {
        mobileMenu?.classList.remove('active');
        sidebar?.classList.remove('active');
        sidebarBackdrop?.classList.remove('active');
        mobileBackdrop?.classList.remove('active');
        document.body.classList.remove('menu-open');
        if (hamburger) {
            hamburger.setAttribute('aria-expanded', 'false');
        }
        if (companionToggle) {
            companionToggle.setAttribute('aria-expanded', 'false');
        }
    };

    const toggleMenu = () => {
        const shouldOpenSidebar = !!sidebar && window.innerWidth <= 1024;
        if (shouldOpenSidebar) {
            const isOpen = sidebar.classList.toggle('active');
            sidebarBackdrop?.classList.toggle('active', isOpen);
            document.body.classList.toggle('menu-open', isOpen);
            if (hamburger) {
                hamburger.setAttribute('aria-expanded', String(isOpen));
            }
            if (companionToggle) {
                companionToggle.setAttribute('aria-expanded', String(isOpen));
            }
            return;
        }

        const mobileOpen = mobileMenu?.classList.toggle('active') ?? false;
        mobileBackdrop?.classList.toggle('active', mobileOpen);
        document.body.classList.toggle('menu-open', mobileOpen);
        if (hamburger) {
            hamburger.setAttribute('aria-expanded', String(mobileOpen));
        }
    };

    if (hamburger) {
        hamburger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });
    }

    if (companionToggle) {
        companionToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });
    }

    if (companionClose) {
        companionClose.addEventListener('click', (e) => {
            e.stopPropagation();
            closeMenu();
        });
    }

    document.addEventListener('click', (e) => {
        const clickedInsideSidebar = sidebar?.contains(e.target);
        const clickedInsideMenu = mobileMenu?.contains(e.target);
        if (!hamburger?.contains(e.target) && !clickedInsideSidebar && !clickedInsideMenu) {
            closeMenu();
        }
    });

    sidebarBackdrop?.addEventListener('click', closeMenu);
    mobileBackdrop?.addEventListener('click', closeMenu);

    mobileMenu?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    sidebar?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            closeMenu();
        }
    });

    if (avatarWrapper && dropdown) {
        avatarWrapper.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'flex';
            dropdown.style.display = isVisible ? 'none' : 'flex';
        });

        document.addEventListener('click', () => {
            dropdown.style.display = 'none';
        });
    }

    document.querySelectorAll('.alert').forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    document.querySelectorAll('.password-toggle').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const targetId = toggle.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            toggle.textContent = isPassword ? '🙈' : '👁';
            toggle.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    const roleSelect = document.getElementById('roleSelect');
    const roleEyebrow = document.getElementById('roleEyebrow');
    const roleHeadline = document.getElementById('roleHeadline');
    const roleDescription = document.getElementById('roleDescription');
    const roleBenefits = document.getElementById('roleBenefits');
    const registerTitle = document.getElementById('registerTitle');
    const registerSubtitle = document.getElementById('registerSubtitle');
    const submitBtn = document.getElementById('submitBtn');
    const roleInfoCard = document.getElementById('roleInfoCard');

    const updateRoleUI = () => {
        const isCompanion = roleSelect?.value === 'companion';
        if (!isCompanion && roleEyebrow) {
            roleEyebrow.textContent = 'Daftar Sekarang';
            roleHeadline.textContent = 'Buat akun dan mulai gunakan layanan TemanCOD dengan lebih praktis.';
            roleDescription.textContent = 'Jadilah pengguna yang nyaman atau companion yang siap membantu perjalanan pelanggan.';
            if (roleBenefits) {
                roleBenefits.innerHTML = '<li>🧭 Booking lebih terarah</li><li>💬 Komunikasi lebih mudah</li><li>✅ Status order tetap terpantau</li>';
            }
            if (registerTitle) registerTitle.textContent = 'Buat Akun Baru';
            if (registerSubtitle) registerSubtitle.textContent = 'Bergabung dan mulai gunakan layanan TemanCOD';
            if (submitBtn) submitBtn.textContent = 'Daftar Sekarang';
            if (roleInfoCard) {
                roleInfoCard.innerHTML = '<div class="auth-role-pill">👤 Pengguna</div><div><strong>Alur akun</strong><br>Akun kamu akan dipakai untuk memesan dan mengikuti status pesanan secara terarah.</div>';
            }
        } else if (isCompanion) {
            roleEyebrow.textContent = 'Companion';
            roleHeadline.textContent = 'Siap membantu customer saat COD?';
            roleDescription.textContent = 'Saat customer memesan, admin akan memverifikasi pembayaran lalu menghubungi kamu lewat WhatsApp untuk mulai membantu.';
            if (roleBenefits) {
                roleBenefits.innerHTML = '<li>🤝 Terhubung dengan admin</li><li>📲 Siap menerima order lewat WhatsApp</li><li>💰 Dapatkan pendapatan sesuai tarif</li>';
            }
            if (registerTitle) registerTitle.textContent = 'Daftar Jadi Companion';
            if (registerSubtitle) registerSubtitle.textContent = 'Bergabung sebagai companion dan bantu customer saat proses COD.';
            if (submitBtn) submitBtn.textContent = 'Daftar Jadi Companion';
            if (roleInfoCard) {
                roleInfoCard.innerHTML = '<div class="auth-role-pill">🤝 Companion</div><div><strong>Hubungan dengan admin</strong><br>Admin akan memverifikasi pembayaran dan menghubungi kamu lewat WhatsApp saat ada order yang masuk.</div>';
            }
        }
    };

    roleSelect?.addEventListener('change', updateRoleUI);
    updateRoleUI();

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.step-card, .companion-card, .testi-card').forEach((el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });

    document.querySelectorAll('[data-confirm]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            if (!confirm(btn.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});