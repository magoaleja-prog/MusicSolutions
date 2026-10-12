<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * Requires an authenticated session, otherwise sends the user to the login page.
 */
final class AuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        if (Auth::check()) {
            return null;
        }

        Session::flash('error', 'Inicia sesion para continuar.');

        return Response::redirect(url('login'));
    }
}
