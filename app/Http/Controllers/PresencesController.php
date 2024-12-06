<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presences; // Model Presences
use Illuminate\Support\Facades\Auth;


class PresencesController extends Controller
{
public function store(Request $request)
    {

        if (!request()->bearerToken()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is required.'
            ], 401);
        }

        
        $request->validate([
            'id_user' => 'required|exists:users,id', 
            'date'    => 'required|date', 
            'time'    => 'required|date_format:H:i:s', 
            'status'  => 'required|in:hadir,izin,sakit', 
        ]);

        
        $presences = Presences::create([
            'id_user' => $request->id_user,
            'date'    => $request->date,
            'time'    => $request->time,
            'status'  => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi berhasil dicatat',
            'data'    => $presences
],201);
}


    /**
     * Fungsi untuk melihat riwayat presensi user.
     */
    public function riwayat(Request $request, $user_id = null)
    {
        $user = auth()->user();

        // Admin dapat melihat riwayat presensi siapa saja
        if ($user->role === 'admin') {
            // Jika admin tidak menyertakan user_id, tampilkan semua presensi
            $presences = $user_id 
                ? Presences::where('user_id', $user_id)->get()
                : Presences::all();
        } 
        // Siswa hanya bisa melihat riwayatnya sendiri
        else if ($user->role === 'siswa') {
            // Jika siswa mencoba mengakses presensi siswa lain
            if ($user_id && $user_id != $user->id) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk melihat riwayat presensi siswa lain.'
                ], 403);
            }

            // Menampilkan riwayat presensi siswa yang sedang login
            $presences = Presences::where('user_id', $user->id)->get();
        } else {
            return response()->json([
                'message' => 'Role tidak dikenali.'
            ], 403);
        }

        // Jika tidak ada presensi yang ditemukan
        if ($presences->isEmpty()) {
            return response()->json([
                'message' => 'Riwayat presensi tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Riwayat presensi ditemukan.',
            'data' => $presences,
        ]);
    }
}
