<?php

namespace App\Http\Controllers;

use App\Models\attendance;
use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Presences;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class attendanceController extends Controller
{
    /**
     * Fungsi untuk mencatat presensi user.
     */
    public function store(Request $request)
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek apakah user yang login adalah admin
        if ($user->role === 'admin') {
            // Validasi input untuk admin (memerlukan user_id)
            $validated = $request->validate([
                'id_user' => 'required|exists:users,id',
                'status' => 'required|in:hadir,izin,sakit',
            ]);

            $userId = $validated['user_id'];
        } else {
            // Validasi input untuk siswa (tidak memerlukan user_id)
            $validated = $request->validate([
                'status' => 'required|in:hadir,izin,sakit',
            ]);

            $userId = $user->id; // Siswa hanya bisa melakukan presensi untuk dirinya sendiri
        }

        // Menyimpan presensi baru
        $presence = Presences::create([
            'user_id' => $userId,
            'date' => now()->toDateString(),  // Menggunakan waktu saat ini
            'time' => now()->toTimeString(),  // Menggunakan waktu saat ini
            'status' => $validated['status'], // Status dari request
        ]);

        // Mengembalikan response JSON
        return response()->json([
            'status' => 'sukses',
            'message' => 'Presensi berhasil dicatat',
            'data' => [
                'id' => $presence->id,
                'user_id' => $presence->user_id,
                'date' => $presence->date,
                'time' => $presence->time,
                'status' => $presence->status,
            ]
        ]);
    }
    public function riwayat($user_id)
    {
        // Mengecek apakah yang mengakses adalah admin atau bukan
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Hanya admin yang bisa melihat riwayat presensi.'
            ], 403); // Status 403 berarti "Forbidden"
        }

        // Jika admin, ambil riwayat presensi berdasarkan user_id
        $presences = presences::where('user_id', $user_id)->get();

        if ($presences->isEmpty()) {
            return response()->json([
                'message' => 'Riwayat presensi tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Riwayat presensi ditemukan.',
            'data' => $presences
        ]);
    }
}
