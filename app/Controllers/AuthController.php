<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

final class AuthController extends Controller
{
    public function loginForm(): Response
    {
        return $this->view('auth/login', [], 'layouts/auth');
    }

    public function login(): Response
    {
        Csrf::verify();

        $data = [
            'email' => (string) request()->input('email', ''),
            'password' => (string) request()->input('password', ''),
        ];

        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email' => 'correo',
            'password' => 'contrasena',
        ]);

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('_old', ['email' => $data['email']]);

            return $this->redirect(url('login'));
        }

        if (!Auth::attempt($data['email'], $data['password'])) {
            Session::flash('error', 'Credenciales incorrectas. Revisa tu correo y contrasena.');
            Session::flash('_old', ['email' => $data['email']]);

            return $this->redirect(url('login'));
        }

        Session::flash('success', 'Bienvenida de nuevo, ' . (user()['name'] ?? '') . '.');

        return $this->redirect(url('dashboard'));
    }

    public function registerForm(): Response
    {
        return $this->view('auth/register', [], 'layouts/auth');
    }

    public function register(): Response
    {
        Csrf::verify();

        $data = [
            'name' => (string) request()->input('name', ''),
            'username' => (string) request()->input('username', ''),
            'email' => (string) request()->input('email', ''),
            'password' => (string) request()->input('password', ''),
            'password_confirmation' => (string) request()->input('password_confirmation', ''),
        ];

        $validator = Validator::make($data, [
            'name' => 'required|min:3|max:80',
            'username' => 'required|min:3|max:40|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name' => 'nombre',
            'username' => 'usuario',
            'email' => 'correo',
            'password' => 'contrasena',
        ]);

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('_old', $data);

            return $this->redirect(url('register'));
        }

        $userId = (new User())->register($data);
        $user = (new User())->find($userId);

        if ($user !== null) {
            Auth::login($user);
        }

        Session::flash('success', 'Cuenta creada. Ya formas parte de MusicSolutions.');

        return $this->redirect(url('dashboard'));
    }

    public function logout(): Response
    {
        Csrf::verify();
        Auth::logout();

        return $this->redirect(url('login'));
    }
}
