/* ==========================================================================
   MusicSolutions · Afinador con deteccion de tono (Web Audio API)
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

    const NOTE_INDEX = { C: 0, 'C#': 1, Db: 1, D: 2, 'D#': 3, Eb: 3, E: 4, F: 5, 'F#': 6, Gb: 6, G: 7, 'G#': 8, Ab: 8, A: 9, 'A#': 10, Bb: 10, B: 11 };
    const NOTE_NAMES = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

    let audioContext = null;
    let analyser = null;
    let source = null;
    let stream = null;
    let buffer = null;
    let running = false;
    let manualTarget = null;
    let frame = 0;

    function referenceFrequency() {
        return calibration && calibration.checked ? 442 : 440;
    }

    function noteToFrequency(name) {
        const match = /^([A-G][#b]?)(-?\d+)$/.exec(name);
        if (!match) {
            return null;
        }

        const midi = (parseInt(match[2], 10) + 1) * 12 + NOTE_INDEX[match[1]];
        return referenceFrequency() * Math.pow(2, (midi - 69) / 12);
    }

    function frequencyToMidi(frequency) {
        return 69 + 12 * Math.log2(frequency / referenceFrequency());
    }

    function midiToName(midi) {
        const rounded = Math.round(midi);
        const name = NOTE_NAMES[((rounded % 12) + 12) % 12];
        const octave = Math.floor(rounded / 12) - 1;
        return name + octave;
    }

    function autoCorrelate(samples, sampleRate) {
        let size = samples.length;
        let rms = 0;

        for (let i = 0; i < size; i++) {
            rms += samples[i] * samples[i];
        }

        rms = Math.sqrt(rms / size);
        if (rms < 0.01) {
            return -1;
        }

        let start = 0;
        let end = size - 1;
        const threshold = 0.2;

        for (let i = 0; i < size / 2; i++) {
            if (Math.abs(samples[i]) < threshold) { start = i; break; }
        }
        for (let i = 1; i < size / 2; i++) {
            if (Math.abs(samples[size - i]) < threshold) { end = size - i; break; }
        }

        const trimmed = samples.slice(start, end);
        size = trimmed.length;

        const correlations = new Float32Array(size);
        for (let i = 0; i < size; i++) {
            for (let j = 0; j < size - i; j++) {
                correlations[i] += trimmed[j] * trimmed[j + i];
            }
        }

        let position = 0;
        while (position < size - 1 && correlations[position] > correlations[position + 1]) {
            position++;
        }

        let maxValue = -1;
        let maxPosition = -1;
        for (let i = position; i < size; i++) {
            if (correlations[i] > maxValue) {
                maxValue = correlations[i];
                maxPosition = i;
            }
        }

        let period = maxPosition;
        const x1 = correlations[period - 1];
        const x2 = correlations[period];
        const x3 = correlations[period + 1];
        const a = (x1 + x3 - 2 * x2) / 2;
        const b = (x3 - x1) / 2;

        if (a) {
            period -= b / (2 * a);
        }

        if (period <= 0) {
            return -1;
        }

        return sampleRate / period;
    }

    function currentInstrument() {
        return instruments[instrumentSelect.value] || { label: '', notes: [] };
    }

    function nearestTarget(frequency, notes) {
        let best = null;
        let bestDistance = Infinity;

        notes.forEach((name) => {
            const target = noteToFrequency(name);
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

    function renderStrings() {
        const notes = currentInstrument().notes || [];
        stringsEl.innerHTML = '';

        notes.forEach((name) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = name;
            button.addEventListener('click', () => {
                manualTarget = noteToFrequency(name) === null ? null : name;
                stringsEl.querySelectorAll('button').forEach((b) => b.classList.remove('is-active'));
                button.classList.add('is-active');
            });
            stringsEl.appendChild(button);
        });
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
        let target = manualTarget ? { name: manualTarget, frequency: noteToFrequency(manualTarget) } : nearestTarget(frequency, notes);

        if (!target) {
            const midi = frequencyToMidi(frequency);
            target = { name: midiToName(midi), frequency: frequency / Math.pow(2, (frequencyToMidi(frequency) - Math.round(frequencyToMidi(frequency))) / 12) };
        }

        const cents = 1200 * Math.log2(frequency / target.frequency);
        const clamped = Math.max(-50, Math.min(50, cents));
        const detected = midiToName(frequencyToMidi(frequency));

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
        const frequency = autoCorrelate(buffer, audioContext.sampleRate);
        updateDisplay(frequency);

        frame = window.requestAnimationFrame(loop);
    }

    async function start() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: false,
                    noiseSuppression: false,
                    autoGainControl: false,
                },
            });

            audioContext = new (window.AudioContext || window.webkitAudioContext)();
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
            statusEl.textContent = 'No se pudo acceder al microfono. Revisa los permisos del navegador.';
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
