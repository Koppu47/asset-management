<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $role = $request->string('role')->trim()->value();

        $usersQuery = User::query()
            ->withCount('borrowRecords');

        if (!empty($search)) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($role) && in_array($role, ['admin', 'user'], true)) {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->count(),
        ];

        return view('admin.users.index', compact('users', 'search', 'role', 'stats'));
    }
}
