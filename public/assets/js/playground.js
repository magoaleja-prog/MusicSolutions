/* ==========================================================================
   MusicSolutions · Playground de programadores
   - Detector de errores: POST /api/lint (php, node, python en el servidor)
   - Ejecucion de JavaScript en un iframe aislado (sandbox, sin acceso a la web)
   - Previsualizacion de HTML/CSS
   ========================================================================== */
(function () {
    'use strict';

    const root = document.querySelector('.playground');
    if (!root) {
        return;
    }

    const languageSelect = root.querySelector('[data-pg-language]');
    const editor = root.querySelector('[data-pg-editor]');
    const lintButton = root.querySelector('[data-pg-lint]');
    const runButton = root.querySelector('[data-pg-run]');
    const exampleButton = root.querySelector('[data-pg-example]');
    const clearButton = root.querySelector('[data-pg-clear]');
    const output = root.querySelector('[data-pg-output]');
    const statusEl = root.querySelector('[data-pg-status]');
    const consoleLabel = root.querySelector('[data-pg-console-label]');
    const preview = root.querySelector('[data-pg-preview]');

    const csrf = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrf ? csrf.getAttribute('content') : '';

    const EXAMPLES = {
        javascript:
            "// Hola desde la consola: pulsa \"Ejecutar\"\nconst notas = ['C', 'E', 'G', 'B'];\nconsole.log('Funciona! Acorde todavia no soportado:', notas.join('-'));\nfunction suma(a, b) { return a + b; }\nconsole.log('2 + 3 =', suma(2, 3));\nconsole.warn('Esto es una advertencia');\nconsole.error('Y esto un error controlado');",
        php:
            "<?php\n$frecuencias = ['E2' => 82.41, 'A2' => 110.00, 'D3' => 146.83];\nforeach ($frecuencias as $cuerda => $hz) {\n    echo \"$cuerda -> $hz Hz\\n\";\n}\n\nfunction semitono($frecuencia) {\n    return $frecuencia * pow(2, 1 / 12);\n}\n\n// Falta un ';' aqui: prueba a quitarlo para ver el detector\nvar_dump(semitono(440));",
        python:
            "# Solo revision de sintaxis (el servidor compila el fichero)\ndef semitono(frecuencia):\n    return frecuencia * (2 ** (1 / 12))\n\nfrecuencias = {\"E2\": 82.41, \"A2\": 110.0}\nfor cuerda, hz in frecuencias.items():\n    print(f\"{cuerda} -> {hz:.2f} Hz\")\n\nprint(semitono(440))",
        html:
            '<!DOCTYPE html>\n<html>\n<head>\n  <style>\n    body { font-family: system-ui; display: grid; place-items: center; height: 90vh; background: #08070d; color: #00ff9d; }\n  </style>\n</head>\n<body>\n  <h1>🎸 Preview HTML/CSS</h1>\n  <p>Esto se renderiza en una zona aislada del navegador.</p>\n</body>\n</html>',
    };

    const LABELS = {
        php: 'PHP — revision de sintaxis',
        javascript: 'JavaScript — consola aislada',
        python: 'Python — revision de sintaxis',
        html: 'HTML / CSS — previsualizacion',
    };

    function setStatus(text, className) {
        statusEl.textContent = text;
        statusEl.className = 'lint-' + (className || 'ok');
    }

    function writeLine(html, className) {
        const line = document.createElement('div');
        line.className = className || '';
        line.innerHTML = html;
        output.appendChild(line);
        output.scrollTop = output.scrollHeight;
    }

    function clearOutput(sayReady) {
        output.innerHTML = '';
        if (sayReady !== false) {
            writeLine('Lista. Escribe codigo y pulsa una accion.', 'muted');
        }
    }

    // 1) Detector de errores (sintaxis) --------------------------------
    lintButton.addEventListener('click', () => {
        const code = editor.value;

        if (code.trim() === '') {
            setStatus('No hay codigo que revisar', 'error');
            return;
        }

        setStatus('Revisando en el servidor...');
        clearOutput(false);

        fetch(root.getAttribute('data-lint-url') || '/api/lint', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ language: languageSelect.value, code: code }),
        })
            .then((response) => response.json())
            .then((result) => {
                if (result.error) {
                    setStatus('Error', 'error');
                    writeLine('<span class="lint-error">' + escapeHtml(result.error) + '</span>');
                    return;
                }

                if (!result.available) {
                    setStatus('No disponible', 'error');
                    writeLine('<span class="lint-error">El lenguaje <b>' + escapeHtml(result.language) + '</b> no esta instalado en este equipo, asi que solo se revisa en el navegador.</span>');
                    return;
                }

                if (result.ok) {
                    setStatus('Sin errores ✓');
                    writeLine('<span class="lint-ok">✔ Sin errores de sintaxis.</span>');
                    return;
                }

                setStatus(result.errors.length + ' error(es) encontrado(s)', 'error');
                writeLine('<span class="lint-error">✘ La sintaxis no es valida.</span>');

                (result.errors || []).forEach((error) => {
                    const line = error.line ? ' <b>linea ' + error.line + '</b>:' : ' <b>general</b>:';
                    writeLine('<span class="lint-error">' + line + ' ' + escapeHtml(error.message) + '</span>');
                });
            })
            .catch(() => {
                setStatus('Sin conexion con el servidor', 'error');
                writeLine('<span class="lint-error">No se pudo conectar con el detector de errores. Comprueba la red.</span>');
            });
    });

    // 2) Ejecucion JS en zona aislada / preview HTML ---------------------
    const RUNNER_SRCDOC = [
        '<!DOCTYPE html><html><body><script>',
        'var send = function (type, data) { parent.postMessage({ type: type, data: String(data) }, "*"); };',
        'window.onerror = function (m) { send("error", m); };',
        'function boot(code) {',
        '  console.log = function () { send("log", Array.prototype.slice.call(arguments).map(function (x) { return (typeof x === "object" && x !== null) ? JSON.stringify(x) : String(x); }).join(" ")); };',
        '  console.warn = function () { send("warn", Array.prototype.slice.call(arguments).map(String).join(" ")); };',
        '  console.error = function () { send("error", Array.prototype.slice.call(arguments).map(String).join(" ")); };',
        '  try { (0, eval)(code); } catch (err) { send("error", err && err.message ? err.message : String(err)); }',
        '}',
        'window.addEventListener("message", function (e) { if (e.data && e.data.type === "run") { boot(e.data.code); } });',
        'parent.postMessage({ type: "ready" }, "*");',
        '</script></body></html>',
    ].join('');

    runButton.addEventListener('click', () => {
        const code = editor.value;
        const language = languageSelect.value;

        if (code.trim() === '') {
            setStatus('No hay codigo que ejecutar', 'error');
            return;
        }

        if (language === 'html') {
            clearOutput(false);
            setStatus('Previsualizando...');
            preview.hidden = false;
            preview.setAttribute('sandbox', 'allow-scripts');
            preview.srcdoc = code;
            consoleLabel.textContent = 'Preview HTML/CSS';
            writeLine('<span class="lint-ok">✔ Vista previa renderizada en una zona aislada.</span>');
            return;
        }

        if (language !== 'javascript') {
            setStatus('Solo revision', 'error');
            clearOutput(false);
            writeLine('<span class="lint-error">En ' + escapeHtml(language.toUpperCase()) + ' este equipo solo revisa la sintaxis (no lo ejecuta). Usa <b>Detectar errores</b>.</span>');
            return;
        }

        clearOutput(false);
        setStatus('Ejecutando...');
        preview.removeAttribute('sandbox');
        preview.setAttribute('sandbox', 'allow-scripts');
        preview.srcdoc = RUNNER_SRCDOC;
        consoleLabel.textContent = 'Consola JavaScript';

        const deliver = (event) => {
            if (event.source !== preview.contentWindow) {
                return;
            }
            if (event.data && event.data.type === 'ready') {
                preview.contentWindow.postMessage({ type: 'run', code: code }, '*');
                setStatus('Ejecutando');
                return;
            }
            if (event.data && event.data.type === 'log') {
                writeLine('› ' + escapeHtml(event.data.data));
                return;
            }
            if (event.data && event.data.type === 'warn') {
                writeLine('⚠ ' + escapeHtml(event.data.data), 'lint-error');
                return;
            }
            if (event.data && event.data.type === 'error') {
                writeLine('<span class="lint-error">✘ ' + escapeHtml(event.data.data) + '</span>');
            }
        };

        window.addEventListener('message', deliver);
        // Los usuarios de la consola no necesitan el listener tras la primera prueba.
        // (se queda registrado; el iframe se sustituye en la siguiente ejecucion)
        setTimeout(() => window.removeEventListener('message', deliver), 20000);
    });

    // 3) Ejemplos rapidos --------------------------------------------------
    function loadExample() {
        const language = languageSelect.value;
        editor.value = EXAMPLES[language] || '';
        clearOutput();
        setStatus('Ejemplo cargado');
        preview.hidden = true;
        consoleLabel.textContent = LABELS[language] || 'Consola';
    }

    exampleButton.addEventListener('click', loadExample);

    languageSelect.addEventListener('change', () => {
        clearOutput();
        setStatus('Lenguaje cambiado');
        preview.hidden = true;
        consoleLabel.textContent = LABELS[languageSelect.value] || 'Consola';
        loadExample();
    });

    clearButton.addEventListener('click', () => {
        clearOutput();
        setStatus('Consola limpia');
    });

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Arranque: ejemplo inicial
    loadExample();
})();