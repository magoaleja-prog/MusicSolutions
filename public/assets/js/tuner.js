/* ==========================================================================
   MusicSolutions · Afinador en tiempo real (Web Audio API)
   Usa el modulo puro Pitch (pitch-detection.js) para detectar la nota.
   Ademas puede sonar la nota de referencia de cada cuerda para afinar de oido.
   ========================================================================== */
(function () {
    'use strict';

    const root = document.querySelector('[data-tuner]');
    if (!root) {
        return;
    }

    const instruments = JSON.parse(root.getAttribute('data-notes') || '{}');

    const noteEl = root.querySelector('[data-tuner-note]');
    const frequencyEl = root.querySelector('[data-tuner-frequency]');
    const centsEl = root.querySelector('[data-tuner-cents]');
    const needleEl = root.querySelector('[data-tuner-needle]');
    const statusEl = root.querySelector('[data-tuner-status]');
    const toggleButton = root.querySelector('[data-tuner-toggle]');
    const instrumentSelect = root.querySelector('[data-tuner-instrument]');
    const stringsEl = root.querySelector('[data-tuner-strings]');
    const calibration = root.querySelector('[data-tuner-a4]');

    let audioContext = null;
    let analyser = null;
    let source = null;
    let stream = null;
    let buffer = null;
    let running = false;
    let manualTarget = null;
    let frame = 0;

    // Contexto de audio aparte para sonar las notas de referencia
    let toneContext = null;
    let activeTone = null;

    function referenceA4() {
        return calibration && calibration.checked ? 442 : 440;
    }

    function currentInstrument() {
        return instruments[instrumentSelect.value] || { label: '', notes: [] };
    }

    function nearestTarget(frequency, notes) {
        let best = null;
        let bestDistance = Infinity;

        notes.forEach((name) => {
            const target = Pitch.noteToFrequency(name, referenceA4());
            if (target === null) {
                return;
            }
            const distance = Math.abs(1200 * Math.log2(frequency / target));
            if (distance < bestDistance) {
                bestDistance = distance;
                best = { name: name, frequency: target };
            }
        });

        return best;
    }

    function updateDisplay(frequency) {
        if (!frequency || frequency < 0) {
            noteEl.textContent = '--';
            centsEl.textContent = '0 cents';
            frequencyEl.textContent = '0.0 Hz';
            needleEl.style.left = '50%';
            needleEl.style.background = '#fff';
            return;
        }

        const notes = currentInstrument().notes || [];
        let target = manualTarget
            ? { name: manualTarget, frequency: Pitch.noteToFrequency(manualTarget, referenceA4()) }
            : nearestTarget(frequency, notes);

        if (!target || !target.frequency) {
            const midi = Pitch.frequencyToMidi(frequency, referenceA4());
            target = {
                name: Pitch.midiToName(midi),
                frequency: frequency / Math.pow(2, (midi - Math.round(midi)) / 12),
            };
        }

        const cents = 1200 * Math.log2(frequency / target.frequency);
        const clamped = Math.max(-50, Math.min(50, cents));
        const detected = Pitch.midiToName(Pitch.frequencyToMidi(frequency, referenceA4()));

        noteEl.textContent = target.name;
        frequencyEl.textContent = frequency.toFixed(1) + ' Hz';
        centsEl.textContent = (cents >= 0 ? '+' : '') + cents.toFixed(0) + ' cents · ' + detected;

        needleEl.style.left = Math.max(0, Math.min(100, 50 + clamped)) + '%';

        const absolute = Math.abs(cents);
        if (absolute < 5) {
            needleEl.style.background = '#34d399';
            statusEl.textContent = 'Afinado 🎯';
        } else if (absolute < 20) {
            needleEl.style.background = '#fbbf24';
            statusEl.textContent = cents > 0 ? 'Un poco alto' : 'Un poco bajo';
        } else {
            needleEl.style.background = '#f43f5e';
            statusEl.textContent = cents > 0 ? 'Demasiado alto' : 'Demasiado bajo';
        }
    }

    function loop() {
        if (!running) {
            return;
        }

        analyser.getFloatTimeDomainData(buffer);
        updateDisplay(Pitch.autoCorrelate(buffer, audioContext.sampleRate));
        frame = window.requestAnimationFrame(loop);
    }

    async function start() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            statusEl.textContent = 'Tu navegador no soporta acceso al microfono. Prueba con Chrome, Edge o Firefox.';
            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: false,
                    noiseSuppression: false,
                    autoGainControl: false,
                },
            });

            const Context = window.AudioContext || window.webkitAudioContext;
            audioContext = new Context();
            if (audioContext.state === 'suspended') {
                await audioContext.resume();
            }

            source = audioContext.createMediaStreamSource(stream);
            analyser = audioContext.createAnalyser();
            analyser.fftSize = 2048;
            buffer = new Float32Array(analyser.fftSize);
            source.connect(analyser);

            running = true;
            toggleButton.textContent = '⏹ Detener';
            toggleButton.classList.remove('btn-primary');
            toggleButton.classList.add('btn-danger');
            statusEl.textContent = 'Escuchando... toca una nota.';
            loop();
        } catch (error) {
            statusEl.textContent = 'No se pudo acceder al microfono: es necesario para detectar la nota. Revisa los permisos del navegador (icono del candado).';
        }
    }

    function stop() {
        running = false;
        window.cancelAnimationFrame(frame);

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
        }

        if (audioContext) {
            audioContext.close();
        }

        audioContext = null;
        analyser = null;
        source = null;
        stream = null;

        toggleButton.textContent = '🎤 Escuchar';
        toggleButton.classList.add('btn-primary');
        toggleButton.classList.remove('btn-danger');
        statusEl.textContent = 'Pulsa "Escuchar" para empezar.';
        updateDisplay(0);
    }

    /* --- Sonar nota de referencia (afinar de oido) ---------------------- */
    async function playTone(frequency, seconds) {
        try {
            const Context = window.AudioContext || window.webkitAudioContext;
            if (!toneContext) {
                toneContext = new Context();
            }
            if (toneContext.state === 'suspended') {
                await toneContext.resume();
            }

            if (activeTone) {
                activeTone.stop();
                activeTone.disconnect();
                activeTone = null;
            }

            const now = toneContext.currentTime;
            const osc = toneContext.createOscillator();
            const gain = toneContext.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(frequency, now);

            gain.gain.setValueAtTime(0.0001, now);
            gain.gain.exponentialRampToValueAtTime(0.28, now + 0.02);
            gain.gain.setValueAtTime(0.28, now + seconds - 0.1);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + seconds);

            osc.connect(gain);
            gain.connect(toneContext.destination);
            osc.start(now);
            osc.stop(now + seconds + 0.05);

            activeTone = osc;
        } catch (error) {
            statusEl.textContent = 'Tu navegador bloqueo la reproduccion del tono.';
        }
    }

    function renderStrings() {
        const notes = currentInstrument().notes || [];
        stringsEl.innerHTML = '';

        notes.forEach((name) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = name;
            button.title = 'Tocar ' + name + ' para escuchar y afinar';
            button.setAttribute('aria-label', 'Sonar la nota ' + name + ' de referencia');

            button.addEventListener('click', () => {
                manualTarget = Pitch.noteToFrequency(name, referenceA4()) === null ? null : name;
                stringsEl.querySelectorAll('button').forEach((b) => b.classList.remove('is-active'));
                button.classList.add('is-active');

                const frequency = Pitch.noteToFrequency(name, referenceA4());
                if (frequency) {
                    playTone(frequency, 1.6);
                    statusEl.textContent = 'Sonando ' + name + ' a ' + frequency.toFixed(1) + ' Hz. Afina tu cuerda hasta igualarla.';
                }
            });

            stringsEl.appendChild(button);
        });
    }

    toggleButton.addEventListener('click', () => {
        if (running) {
            stop();
        } else {
            start();
        }
    });

    instrumentSelect.addEventListener('change', () => {
        manualTarget = null;
        renderStrings();
    });

    if (calibration) {
        calibration.addEventListener('change', () => {
            manualTarget = null;
        });
    }

    renderStrings();
})();