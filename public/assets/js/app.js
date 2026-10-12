/* ==========================================================================
   MusicSolutions · UI general (navegacion, avisos, utilidades)
   ========================================================================== */
(function () {
    'use strict';

    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Cerrar avisos
    document.querySelectorAll('[data-flash-close]').forEach((button) => {
        button.addEventListener('click', () => {
            const flash = button.closest('[data-flash]');
            if (flash) {
                flash.remove();
            }
        });
    });

    // Autocierre de avisos de exito
    document.querySelectorAll('.flash--success[data-flash]').forEach((flash) => {
        window.setTimeout(() => flash.remove(), 6000);
    });

    // Botones de copiar al portapapeles
    document.querySelectorAll('[data-copy-target]').forEach((button) => {
        button.addEventListener('click', async () => {
            const target = document.querySelector(button.getAttribute('data-copy-target'));
            if (!target) {
                return;
            }

            try {
                await navigator.clipboard.writeText(target.textContent);
                const original = button.textContent;
                button.textContent = 'Copiado';
                window.setTimeout(() => { button.textContent = original; }, 1600);
            } catch (error) {
                button.textContent = 'Error';
            }
        });
    });
})();
