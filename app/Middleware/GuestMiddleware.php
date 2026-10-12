<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

/**
 * Keeps authenticated users away from login and register screens.
 */
final class GuestMiddleware
{
    public function handle(Request $request): ?Response
    {
        if (!Auth::check()) {
            return null;
        }

        return Response::redirect(url('dashboard'));
    }
}
