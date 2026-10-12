<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\DevResource;
use App\Support\Uploader;
use RuntimeException;

final class DeveloperController extends Controller
{
    private const ALLOWED_TYPES = ['snippet', 'project', 'package', 'tool', 'link'];

    private const ALLOWED_EXTENSIONS = ['zip', 'tar', 'gz', 'pdf', 'png', 'jpg', 'jpeg', 'txt', 'md', 'json', 'js', 'ts', 'php', 'py', 'java', 'cs', 'cpp', 'c', 'rb', 'go', 'rs', 'sql', 'yml', 'yaml'];

    public function index(): Response
    {
        $type = (string) request()->input('type', '');
        $type = in_array($type, self::ALLOWED_TYPES, true) ? $type : '';

        return $this->view('developer/index', [
            'resources' => (new DevResource())->withAuthor(100, $type === '' ? null : $type),
            'activeType' => $type,
            'types' => self::ALLOWED_TYPES,
        ]);
    }

    public function create(): Response
    {
        return $this->view('developer/create', [
            'types' => self::ALLOWED_TYPES,
        ]);
    }

    public function store(): Response
    {
        Csrf::verify();

        $data = [
            'title' => (string) request()->input('title', ''),
            'description' => (string) request()->input('description', ''),
            'type' => (string) request()->input('type', 'snippet'),
            'language' => (string) request()->input('language', ''),
            'url' => (string) request()->input('url', ''),
            'content' => (string) request()->input('content', ''),
        ];

        $validator = Validator::make($data, [
            'title' => 'required|min:3|max:150',
            'description' => 'required|max:2000',
            'type' => 'required|in:' . implode(',', self::ALLOWED_TYPES),
            'language' => 'max:40',
            'url' => 'max:255',
        ], [
            'title' => 'titulo',
            'description' => 'descripcion',
            'type' => 'tipo',
            'language' => 'lenguaje',
            'url' => 'enlace',
        ]);

        $file = request()->file('file');

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('_old', $data);

            return $this->redirect(url('programadores/nuevo'));
        }

        if ($file === null && trim($data['content']) === '' && trim($data['url']) === '') {
            Session::flash('error', 'Anade al menos un fragmento de codigo, un enlace o un archivo.');
            Session::flash('_old', $data);

            return $this->redirect(url('programadores/nuevo'));
        }

        $stored = null;

        if ($file !== null) {
            try {
                $stored = Uploader::store($file, 'developer', self::ALLOWED_EXTENSIONS);
            } catch (RuntimeException $exception) {
                Session::flash('error', $exception->getMessage());
                Session::flash('_old', $data);

                return $this->redirect(url('programadores/nuevo'));
            }
        }

        $id = (new DevResource())->create([
            'user_id' => (int) Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'type' => $data['type'],
            'language' => $data['language'],
            'url' => $data['url'],
            'content' => $data['content'],
            'file_path' => $stored['path'] ?? null,
            'original_name' => $stored['original_name'] ?? null,
            'mime' => $stored['mime'] ?? null,
            'size' => $stored['size'] ?? null,
        ]);

        Session::flash('success', 'Recurso publicado en la zona de programadores.');

        return $this->redirect(url('programadores/' . $id));
    }

    public function show(string $id): Response
    {
        $resource = (new DevResource())->findWithAuthor((int) $id);

        if ($resource === null) {
            return Response::abort(404);
        }

        return $this->view('developer/show', [
            'resource' => $resource,
            'isOwner' => (int) ($resource['user_id'] ?? 0) === (int) Auth::id(),
        ]);
    }

    public function download(string $id): Response
    {
        $resource = (new DevResource())->find((int) $id);

        if ($resource === null || empty($resource['file_path'])) {
            return Response::abort(404);
        }

        $path = base_path('public/uploads/' . $resource['file_path']);

        if (!is_file($path)) {
            return Response::abort(404);
        }

        return Response::download($path, $resource['original_name'] ?: basename($path), $resource['mime'] ?: 'application/octet-stream');
    }

    public function destroy(string $id): Response
    {
        Csrf::verify();

        $resource = (new DevResource())->find((int) $id);

        if ($resource === null) {
            return Response::abort(404);
        }

        if ((int) $resource['user_id'] !== (int) Auth::id() && (user()['role'] ?? 'member') !== 'admin') {
            Session::flash('error', 'No puedes eliminar este recurso.');

            return $this->redirect(url('programadores'));
        }

        if (!empty($resource['file_path'])) {
            $path = base_path('public/uploads/' . $resource['file_path']);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        (new DevResource())->delete((int) $id);

        Session::flash('success', 'Recurso eliminado.');

        return $this->redirect(url('developer'));
    }
}
