/**
 * MusicSolutions · Prueba del algoritmo de deteccion de tono.
 *
 * Genera ondas seno con Node y comprueba que el afinador las detecta bien,
 * mas las conversiones nota <-> frecuencia.
 *
 *   node tests/unit/pitch.test.mjs
 */
import { createRequire } from 'module';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const require = createRequire(import.meta.url);
const __dirname = dirname(fileURLToPath(import.meta.url));
const modulePath = join(__dirname, '..', '..', 'public', 'assets', 'js', 'pitch-detection.js');
const Pitch = require(modulePath);

const SR = 44100;

function sine(freq, ms) {
    const n = Math.floor((SR * ms) / 1000);
    const buf = new Float32Array(n);
    for (let i = 0; i < n; i++) {
        buf[i] = Math.sin((2 * Math.PI * freq * i) / SR);
    }
    return buf;
}

const cases = [
    [82.41, 'E2'],
    [110, 'A2'],
    [146.83, 'D3'],
    [196, 'G3'],
    [246.94, 'B3'],
    [329.63, 'E4'],
    [440, 'A4'],
    [523.25, 'C5'],
];

let pass = 0;
for (const [freq, name] of cases) {
    const detected = Pitch.autoCorrelate(sine(freq, 800), SR);
    const ok = detected > 0 && Math.abs(detected - freq) < 2.5;
    console.log(`${ok ? 'PASS' : 'FAIL'} ${name}: ${freq} Hz -> detectado ${detected.toFixed(2)} Hz`);
    if (ok) {
        pass++;
    }
}
console.log(`\n${pass}/${cases.length} pruebas de deteccion OK`);

// Conversiones
const e2 = Pitch.noteToFrequency('E2', 440);
const nameA4 = Pitch.midiToName(Pitch.frequencyToMidi(440, 440));
console.log(`E2 -> ${e2.toFixed(2)} (esperado 82.41)`);
console.log(`Nombre para 440 Hz -> ${nameA4} (esperado A4)`);

const conversionsOk = e2 !== null && Math.abs(e2 - 82.41) < 0.01 && nameA4 === 'A4';
process.exit(pass === cases.length && conversionsOk ? 0 : 1);