<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index(): Response
    {
        // Fetch roles
        return Inertia::render('Roles/Roles', [
            'roles' => Role::all(),
        ]);
    }
}
