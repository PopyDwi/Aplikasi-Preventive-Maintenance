<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeknisiController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = User::where('role', 'Teknisi');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%$q%")
                    ->orWhere('username', 'like', "%$q%");
            });
        }

        $users = $query->orderBy('nama_lengkap')->get(['id', 'nama_lengkap', 'username', 'status']);

        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:191',
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:6',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'Teknisi',
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Akun teknisi berhasil dibuat.', 'data' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'nama_lengkap' => 'required|string|max:191',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:Aktif,Nonaktif',
        ];

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user->nama_lengkap = $request->nama_lengkap;
        $user->username = $request->username;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Akun teknisi berhasil diperbarui.', 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'Nonaktif';
        $user->save();

        return response()->json(['message' => 'Akun teknisi dinonaktifkan.']);
    }
}
