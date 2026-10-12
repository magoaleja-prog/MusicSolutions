/* ==========================================================================
   MusicSolutions · Busqueda en vivo con resultados en streaming
   ========================================================================== */
(function () {
    'use strict';

    const form = document.querySelector('[data-search-form]');
    if (!form) {
        return;
    }

    const input = form.querySelector('[data-search-input]');
    const resultsUrl = form.getAttribute('action');
    const containers = {
        local: document.querySelector('[data-result-local]'),
        external: document.querySelector('[data-result-external]'),
        scores: document.querySelector('[data-result-scores]'),
        resources: document.querySelector('[data-result-resources]'),
    };
    const externalBlock = document.querySelector('[data-block-external]');

    let timer = null;
    let previewAudio = null;

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function empty(message) {
        return '<p class="empty">' + message + '</p>';
    }

    function renderLocal(items, term) {
        if (!items.length) {
            containers.local.innerHTML = empty(term === '' ? 'Sin canciones en el catalogo.' : 'Sin resultados en tu catalogo.');
            return;
        }

        containers.local.innerHTML = '<ul class="track-list">' + items.map((song) => (
            '<li class="track"><div><strong>' + escapeHtml(song.title) + '</strong>' +
            '<small>' + escapeHtml(song.artist) + (song.album ? ' · ' + escapeHtml(song.album) : '') + '</small></div>' +
            (song.genre ? '<span class="badge">' + escapeHtml(song.genre) + '</span>' : '') +
            '</li>'
        )).join('') + '</ul>';
    }

    function renderExternal(items) {
        if (externalBlock) {
            externalBlock.hidden = items.length === 0 && input.value.trim() === '';
        }

        if (!items.length) {
            containers.external.innerHTML = empty('Sin resultados externos.');
            return;
        }

        containers.external.innerHTML = '<ul class="track-list track-list--rich">' + items.map((track) => (
            '<li class="track">' +
            (track.artwork ? '<img src="' + escapeHtml(track.artwork) + '" alt="" width="48" height="48" loading="lazy">' : '') +
            '<div><strong>' + escapeHtml(track.title) + '</strong>' +
            '<small>' + escapeHtml(track.artist) + (track.album ? ' · ' + escapeHtml(track.album) : '') +
            (track.year ? ' (' + escapeHtml(track.year) + ')' : '') + '</small></div>' +
            (track.preview ? '<button type="button" class="btn btn-ghost btn-sm" data-preview="' + escapeHtml(track.preview) + '">▶︎</button>' : '') +
            '</li>'
        )).join('') + '</ul>';
    }

    function renderScores(items) {
        if (!items.length) {
            containers.scores.innerHTML = empty('Sin partituras para esta busqueda.');
            return;
        }

        containers.scores.innerHTML = '<ul class="stack-list">' + items.map((score) => (
            '<li><a href="' + resultsUrl.replace(/buscar.*$/, 'partituras/' + score.id) + '">' +
            '<strong>' + escapeHtml(score.title) + '</strong>' +
            '<small>' + escapeHtml(score.composer || 'Compositor desconocido') + '</small></a>' +
            '<span class="badge">' + escapeHtml(score.instrument || 'Partitura') + '</span></li>'
        )).join('') + '</ul>';
    }

    function renderResources(items) {
        if (!items.length) {
            containers.resources.innerHTML = empty('Sin recursos para esta busqueda.');
            return;
        }

        containers.resources.innerHTML = '<ul class="stack-list">' + items.map((resource) => (
            '<li><a href="' + resultsUrl.replace(/buscar.*$/, 'programadores/' + resource.id) + '">' +
            '<strong>' + escapeHtml(resource.title) + '</strong>' +
            '<small>' + escapeHtml(resource.type) + (resource.language ? ' · ' + escapeHtml(resource.language) : '') + '</small></a></li>'
        )).join('') + '</ul>';
    }

    function search(term) {
        fetch(resultsUrl + '?q=' + encodeURIComponent(term), {
            headers: { 'Accept': 'application/json' },
        })
            .then((response) => response.json())
            .then((data) => {
                renderLocal(data.local || [], term);
                renderExternal(data.external || []);
                renderScores(data.scores || []);
                renderResources(data.resources || []);
            })
            .catch(() => {
                containers.local.innerHTML = empty('No se pudo completar la busqueda.');
            });
    }

    input.addEventListener('input', () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => search(input.value.trim()), 350);
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(timer);
        search(input.value.trim());
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-preview]');
        if (!button) {
            return;
        }

        const url = button.getAttribute('data-preview');
        if (previewAudio) {
            previewAudio.pause();
        }

        previewAudio = new Audio(url);
        previewAudio.play();
        button.textContent = '⏸';
    });
})();
