<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;

final class TunerController extends Controller
{
    public function index(): Response
    {
        return $this->view('tuner/index', [
            'instruments' => $this->instruments(),
        ]);
    }

    /**
     * Instrument presets: target notes in scientific pitch notation.
     *
     * @return array<string, array{label:string, notes:array<int,string>}>
     */
    private function instruments(): array
    {
        return [
            'guitar' => [
                'label' => 'Guitarra',
                'notes' => ['E2', 'A2', 'D3', 'G3', 'B3', 'E4'],
            ],
            'bass' => [
                'label' => 'Bajo',
                'notes' => ['E1', 'A1', 'D2', 'G2'],
            ],
            'ukulele' => [
                'label' => 'Ukelele',
                'notes' => ['G4', 'C4', 'E4', 'A4'],
            ],
            'violin' => [
                'label' => 'Violin',
                'notes' => ['G3', 'D4', 'A4', 'E5'],
            ],
            'cello' => [
                'label' => 'Violonchelo',
                'notes' => ['C2', 'G2', 'D3', 'A3'],
            ],
            'chromatic' => [
                'label' => 'Cromatico',
                'notes' => ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'],
            ],
        ];
    }
}
