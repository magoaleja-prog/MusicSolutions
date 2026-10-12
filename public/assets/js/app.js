/* ==========================================================================
   MusicSolutions · UI global: navegacion, reproductor de musica,
   accesibilidad, avisos y utilidades.
   ========================================================================== */
(function () {
    'use strict';

    /* --- Navegacion movil ------------------------------------------------ */
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    /* --- Cerrar avisos ---------------------------------------------------- */
    document.querySelectorAll('[data-flash-close]').forEach((button) => {
        button.addEventListener('click', () => {
            const flash = button.closest('[data-flash]');
            if (flash) {
                flash.remove();
            }
        });
    });

    document.querySelectorAll('.flash--success[data-flash]').forEach((flash) => {
        window.setTimeout(() => flash.remove(), 6000);
    });

    /* --- Copiar al portapapeles -------------------------------------------- */
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

    /* ======================================================================
       Reproductor de musica global (barras inferior).
       Cualquier elemento con [data-play='{"url","title","artist","art"}']
       dispara la reproduccion.
       ====================================================================== */
    const playerEl = document.querySelector('[data-player]');
    let nowPlaying = null;

    function formatTime(seconds) {
        if (!Number.isFinite(seconds)) {
            return '0:00';
        }
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return m + ':' + String(s).padStart(2, '0');
    }

    if (playerEl) {
        const audio = new Audio();
        audio.preload = 'metadata';

        const artEl = playerEl.querySelector('[data-player-art]');
        const titleEl = playerEl.querySelector('[data-player-title]');
        const artistEl = playerEl.querySelector('[data-player-artist]');
        const timeEl = playerEl.querySelector('[data-player-time]');
        const durationEl = playerEl.querySelector('[data-player-duration]');
        const fillEl = playerEl.querySelector('[data-player-fill]');
        const barEl = playerEl.querySelector('[data-player-bar]');
        const toggleBtn = playerEl.querySelector('[data-player-toggle]');
        const closeBtn = playerEl.querySelector('[data-player-close]');
        const volumeInput = playerEl.querySelector('[data-player-volume]');

        // El arte puede ser la portada (URL) o un icono de texto (emoji).
        function setArt(value) {
            artEl.replaceChildren();

            if (!value) {
                artEl.textContent = '🎵';
                return;
            }

            if (typeof value === 'string' && /^https?:\/\//i.test(value)) {
                const img = document.createElement('img');
                img.src = value;
                img.alt = '';
                img.width = 46;
                img.height = 46;
                img.loading = 'lazy';
                artEl.appendChild(img);
                return;
            }

            artEl.textContent = value;
        }

        const playerState = {
            play(track, button) {
                if (nowPlaying && nowPlaying.button === button && !audio.paused) {
                    playerState.toggle();
                    return;
                }

                audio.src = track.url;
                nowPlaying = { track: track, button: button };

                titleEl.textContent = track.title || 'Sin titulo';
                artistEl.textContent = track.artist || '';
                setArt(track.art);
                barEl.setAttribute('aria-valuenow', '0');

                document.body.classList.add('has-player');
                playerEl.classList.add('is-visible');
                playerEl.setAttribute('aria-hidden', 'false');

                document.querySelectorAll('[data-play]').forEach((el) => el.classList.remove('is-playing'));
                if (button) {
                    button.classList.add('is-playing');
                }

                audio.play().catch(() => {
                    artistEl.textContent = (track.artist || '') + ' · prueba otra vez';
                });
            },

            toggle() {
                if (!nowPlaying) {
                    return;
                }
                if (audio.paused) {
                    audio.play();
                } else {
                    audio.pause();
                }
            },

            close() {
                audio.pause();
                audio.removeAttribute('src');
                audio.load();
                nowPlaying = null;
                document.body.classList.remove('has-player');
                playerEl.classList.remove('is-visible');
                playerEl.setAttribute('aria-hidden', 'true');
                document.querySelectorAll('[data-play]').forEach((el) => el.classList.remove('is-playing'));
                toggleBtn.textContent = '▶';
            },
        };

        audio.addEventListener('play', () => { toggleBtn.textContent = '⏸'; });
        audio.addEventListener('pause', () => { toggleBtn.textContent = '▶'; });
        audio.addEventListener('ended', () => {
            toggleBtn.textContent = '▶';
            if (nowPlaying) {
                nowPlaying.button.classList.remove('is-playing');
            }
        });

        audio.addEventListener('timeupdate', () => {
            if (!audio.duration) {
                return;
            }
            const percent = (audio.currentTime / audio.duration) * 100;
            fillEl.style.width = percent + '%';
            timeEl.textContent = formatTime(audio.currentTime);
            durationEl.textContent = formatTime(audio.duration);
            barEl.setAttribute('aria-valuenow', String(Math.round(percent)));
        });

        audio.addEventListener('loadedmetadata', () => {
            durationEl.textContent = formatTime(audio.duration);
        });

        barEl.addEventListener('click', (event) => {
            if (!audio.duration) {
                return;
            }
            const rect = barEl.getBoundingClientRect();
            const ratio = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
            audio.currentTime = ratio * audio.duration;
        });

        barEl.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
                return;
            }
            event.preventDefault();
            const step = event.key === 'ArrowRight' ? 5 : -5;
            audio.currentTime = Math.max(0, Math.min(audio.duration || 0, audio.currentTime + step));
        });

        volumeInput.addEventListener('input', () => {
            audio.volume = parseFloat(volumeInput.value);
        });

        toggleBtn.addEventListener('click', () => playerState.toggle());
        closeBtn.addEventListener('click', () => playerState.close());

        // Delegacion: [data-play]
        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-play]');
            if (!button) {
                return;
            }

            let track = null;
            try {
                track = JSON.parse(button.getAttribute('data-play'));
            } catch (error) {
                track = null;
            }

            if (track && track.url) {
                playerState.play(track, button);
            }
        });
    }

    /* ======================================================================
       Accesibilidad: panel ajustable con preferencias persistentes.
       ====================================================================== */
    const fab = document.querySelector('[data-a11y-toggle]');
    const a11yPanel = document.querySelector('#a11y-panel');

    const A11Y_KEYS = ['contrast', 'large', 'nomotion', 'lineguide'];

    function applyA11y(key, enabled) {
        document.body.classList.toggle('a11y-' + key, enabled);
        try {
            localStorage.setItem('a11y:' + key, enabled ? '1' : '0');
        } catch (error) {
            // almacenamiento no disponible: la preferencia dura solo la sesion
        }
    }

    if (fab && a11yPanel) {
        fab.addEventListener('click', () => {
            const open = !a11yPanel.hidden;
            a11yPanel.hidden = open;
            fab.setAttribute('aria-expanded', String(!open));
        });

        const closeBtn = a11yPanel.querySelector('[data-a11y-close]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                a11yPanel.hidden = true;
                fab.setAttribute('aria-expanded', 'false');
            });
        }

        A11Y_KEYS.forEach((key) => {
            const input = a11yPanel.querySelector('[data-a11y="' + key + '"]');
            if (!input) {
                return;
            }

            let saved = '0';
            try {
                saved = localStorage.getItem('a11y:' + key) || '0';
            } catch (error) {
                saved = '0';
            }

            input.checked = saved === '1';
            applyA11y(key, input.checked);

            input.addEventListener('change', () => applyA11y(key, input.checked));
        });

        // Cerrar con Escape
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !a11yPanel.hidden) {
                a11yPanel.hidden = true;
                fab.setAttribute('aria-expanded', 'false');
                fab.focus();
            }
        });
    }

    // Guia de lectura: linea fija que sigue el puntero
    const guide = document.querySelector('.reading-guide');
    if (guide) {
        document.addEventListener('mousemove', (event) => {
            guide.style.top = Math.max(0, event.clientY - 22) + 'px';
        });
    }
})();