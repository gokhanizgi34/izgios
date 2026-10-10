document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('#mobile-menu-btn');
    const sidebar = document.querySelector('#izgios-sidebar');
    const overlay = document.querySelector('#sidebar-overlay');
    const closeButton = document.querySelector('#sidebar-close');

    if (!menuButton || !sidebar) {
        return;
    }

    const setMenuState = (isOpen) => {
        sidebar.classList.toggle('active', isOpen);
        overlay?.classList.toggle('active', isOpen);
        document.body.classList.toggle('menu-open', isOpen);
        menuButton.setAttribute('aria-expanded', String(isOpen));

        if (window.innerWidth <= 1024) {
            sidebar.setAttribute('aria-hidden', String(!isOpen));
        } else {
            sidebar.removeAttribute('aria-hidden');
        }
    };

    menuButton.addEventListener('click', () => {
        setMenuState(!sidebar.classList.contains('active'));
    });

    const closeMenuAndRestoreFocus = () => {
        setMenuState(false);
        menuButton.focus();
    };

    overlay?.addEventListener('click', closeMenuAndRestoreFocus);
    closeButton?.addEventListener('click', closeMenuAndRestoreFocus);

    sidebar.addEventListener('click', (event) => {
        if (window.innerWidth <= 1024 && event.target.closest('a')) {
            setMenuState(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar.classList.contains('active')) {
            setMenuState(false);
            menuButton.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024 && sidebar.classList.contains('active')) {
            setMenuState(false);
        }
    });

    setMenuState(false);
});
