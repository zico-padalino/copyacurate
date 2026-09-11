<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;

trait ResolvesDemoUser
{
    protected function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /** @deprecated Use currentUser() */
    protected function demoUser(): User
    {
        return $this->currentUser();
    }

    protected function sanitizeMoney(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?: '0';
    }
}
