/* ==========================================================================
   MusicSolutions · Deteccion de tono (algoritmo puro, sin DOM)
   Usado por el afinador en el navegador y probado con Node en tests/.
   ========================================================================== */
(function (root, factory) {
    'use strict';
    if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.Pitch = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    const NOTE_INDEX = { C: 0, 'C#': 1, Db: 1, D: 2, 'D#': 3, Eb: 3, E: 4, F: 5, 'F#': 6, Gb: 6, G: 7, 'G#': 8, Ab: 8, A: 9, 'A#': 10, Bb: 10, B: 11 };
    const NOTE_NAMES = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

    /**
     * Frecuencia de referencia: La4 = 440 Hz (o 442 si se calibra).
     */
    function referenceFrequency(a4) {
        return a4 || 440;
    }

    /**
     * Convierte "E2", "A4" o "C#" a frecuencia. Sin octava devuelve null.
     */
    function noteToFrequency(name, a4) {
        const match = /^([A-G][#b]?)(-?\d+)?$/.exec(String(name).trim());
        if (!match || match[2] === undefined) {
            return null;
        }

        const midi = (parseInt(match[2], 10) + 1) * 12 + NOTE_INDEX[match[1]];
        return referenceFrequency(a4) * Math.pow(2, (midi - 69) / 12);
    }

    /**
     * Frecuencia -> numero MIDI (admite decimales).
     */
    function frequencyToMidi(frequency, a4) {
        return 69 + 12 * Math.log2(frequency / referenceFrequency(a4));
    }

    /**
     * Numero MIDI -> nombre "C4", "F#3"...
     */
    function midiToName(midi) {
        const rounded = Math.round(midi);
        const name = NOTE_NAMES[((rounded % 12) + 12) % 12];
        const octave = Math.floor(rounded / 12) - 1;
        return name + octave;
    }

    /**
     * Desviacion en cents entre una frecuencia y la nota mas cercana
     * (positivo = agudo, negativo = grave).
     */
    function centsFromFrequency(frequency, targetFrequency) {
        return 1200 * Math.log2(frequency / targetFrequency);
    }

    /**
     * Correlacion cruzada para hallar el periodo fundamental.
     * Devuelve la frecuencia en Hz o -1 si no hay senal clara.
     */
    function autoCorrelate(samples, sampleRate) {
        let size = samples.length;
        let rms = 0;

        for (let i = 0; i < size; i++) {
            rms += samples[i] * samples[i];
        }

        rms = Math.sqrt(rms / size);
        if (rms < 0.008) {
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

        let position = 1;
        while (position < size - 1 && correlations[position] > correlations[position + 1]) {
            position++;
        }

        let maxValue = -1;
        let maxPosition = -1;
        for (let i = position + 1; i < size; i++) {
            if (correlations[i] > maxValue) {
                maxValue = correlations[i];
                maxPosition = i;
            }
        }

        if (maxPosition <= 0) {
            return -1;
        }

        let period = maxPosition;
        const x1 = correlations[period - 1];
        const x2 = correlations[period];
        const x3 = correlations[period + 1];
        const a = (x1 + x3 - 2 * x2) / 2;
        const b = (x3 - x1) / 2;

        if (a !== 0) {
            period -= b / (2 * a);
        }

        if (period <= 1) {
            return -1;
        }

        return sampleRate / period;
    }

    return {
        noteToFrequency: noteToFrequency,
        frequencyToMidi: frequencyToMidi,
        midiToName: midiToName,
        centsFromFrequency: centsFromFrequency,
        autoCorrelate: autoCorrelate,
        NOTE_NAMES: NOTE_NAMES,
    };
}));