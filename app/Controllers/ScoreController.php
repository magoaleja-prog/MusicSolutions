<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Score;
use App\Support\Uploader;
use RuntimeException;

final class ScoreController extends Controller
{
    private const ALLOWED_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'mus', 'midi', 'mid', 'xml', 'mscz', 'mxl', 'zip'];

    public function index(): Response
    {
        $mineOnly = request()->input('mine') === '1';
        $scores = (new Score())->withAuthor(100, $mineOnly ? (int) Auth::id() : null);

        return $this->view('scores/index', [
            'scores' => $scores,
            'mineOnly' => $mineOnly,
        ]);
    }

    public function create(): Response
    {
        return $this->view('scores/create');
    }

    public function store(): Response
    {
        Csrf::verify();

        $data = [
            'title' => (string) request()->input('title', ''),
            'composer' => (string) request()->input('composer', ''),
            'instrument' => (string) request()->input('instrument', ''),
            'description' => (string) request()->input('description', ''),
            'visibility' => (string) request()->input('visibility', 'public'),
        ];

        $validator = Validator::make($data, [
            'title' => 'required|min:2|max:150',
            'composer' => 'max:120',
            'instrument' => 'max:80',
            'description' => 'max:2000',
            'visibility' => 'required|in:public,private',
        ], [
            'title' => 'titulo',
            'composer' => 'compositor',
            'instrument' => 'instrumento',
            'description' => 'descripcion',
        ]);

        $file = request()->file('file');

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('_old', $data);

            return $this->redirect(url('partituras/nueva'));
        }

        if ($file === null) {
            Session::flash('error', 'Adjunta el archivo de la partitura.');

            return $this->redirect(url('partituras/nueva'));
        }

        try {
            $stored = Uploader::store($file, 'scores', self::ALLOWED_EXTENSIONS);
        } catch (RuntimeException $exception) {
            Session::flash('error', $exception->getMessage());
            Session::flash('_old', $data);

            return $this->redirect(url('partituras/nueva'));
        }

        $id = (new Score())->create([
            'user_id' => (int) Auth::id(),
            'title' => $data['title'],
            'composer' => $data['composer'],
            'instrument' => $data['instrument'],
            'description' => $data['description'],
            'visibility' => $data['visibility'],
            'file_path' => $stored['path'],
            'original_name' => $stored['original_name'],
            'mime' => $stored['mime'],
            'size' => $stored['size'],
        ]);

        Session::flash('success', 'Partitura guardada correctamente.');

        return $this->redirect(url('partituras/' . $id));
    }

    public function show(string $id): Response
    {
        $score = (new Score())->findWithAuthor((int) $id);

        if ($score === null) {
            return Response::abort(404);
        }

        $isOwner = (int) ($score['user_id'] ?? 0) === (int) Auth::id();

        if ($score['visibility'] === 'private' && !$isOwner) {
            return Response::abort(404);
        }

        return $this->view('scores/show', [
            'score' => $score,
            'isOwner' => $isOwner,
        ]);
    }

    public function download(string $id): Response
    {
        $score = (new Score())->find((int) $id);

        if ($score === null) {
            return Response::abort(404);
        }

        $isOwner = (int) $score['user_id'] === (int) Auth::id();

        if ($score['visibility'] === 'private' && !$isOwner) {
            return Response::abort(404);
        }

        $path = base_path('public/uploads/' . $score['file_path']);

        if (!is_file($path)) {
            return Response::abort(404);
        }

        return Response::download($path, $score['original_name'] ?: ('partitura.' . pathinfo($path, PATHINFO_EXTENSION)), $score['mime'] ?: 'application/octet-stream');
    }

    public function destroy(string $id): Response
    {
        Csrf::verify();

        $score = (new Score())->find((int) $id);

        if ($score === null) {
            return Response::abort(404);
        }

        if ((int) $score['user_id'] !== (int) Auth::id() && (user()['role'] ?? 'member') !== 'admin') {
            Session::flash('error', 'No puedes eliminar esta partitura.');

            return $this->redirect(url('partituras'));
        }

        $path = base_path('public/uploads/' . $score['file_path']);
        if (is_file($path)) {
            @unlink($path);
        }

        (new Score())->delete((int) $id);

        Session::flash('success', 'Partitura eliminada.');

        return $this->redirect(url('scores'));
    }
}
