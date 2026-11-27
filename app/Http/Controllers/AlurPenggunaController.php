<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlurPenggunaController extends Controller
{
    /**
     * Menampilkan halaman panduan alur penggunaan sistem SIPABIS berdasarkan role.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $defaultTab = $request->query('role', $user->role ?? 'pegawai');

        return view('alur-pengguna.index', compact('user', 'defaultTab'));
    }
}
