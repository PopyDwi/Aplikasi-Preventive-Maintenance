<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DataTeknisiController extends Controller
{
    // Return page view
    public function index()
    {
        return view('datateknisiadm');
    }

    // API: list teknisi
    public function list(Request $request)
    {
        $q = $request->query('q');
        $query = User::where('role', 'Teknisi');
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('username', 'like', "%$q%")
                    ->orWhere('nomor_whatsapp', 'like', "%$q%");
            });
        }
        $users = $query->orderBy('username')->get(['id', 'username', 'role', 'nomor_whatsapp']);
        return response()->json(['data' => $users]);
    }

    // Create
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:6',
            'nomor_whatsapp' => 'nullable|string|max:25',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'email' => $request->username . '@example.com',
            'password' => Hash::make($request->password),
            'role' => 'Teknisi',
            'nomor_whatsapp' => $request->nomor_whatsapp,
        ]);

        if ($user) return response()->json(['message' => 'Akun teknisi berhasil disimpan.', 'data' => $user]);
        return response()->json(['message' => 'Gagal menyimpan akun teknisi.'], 500);
    }

    // Update
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $v = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:users,username,'.$user->id,
            'password' => 'nullable|string|min:6',
            'nomor_whatsapp' => 'nullable|string|max:25',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user->username = $request->username;
        $user->name = $request->username;
        $user->nomor_whatsapp = $request->nomor_whatsapp;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json(['message' => 'Akun teknisi berhasil diperbarui.', 'data' => $user]);
    }

    // Delete permanent
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Akun teknisi berhasil dihapus.']);
    }
}
