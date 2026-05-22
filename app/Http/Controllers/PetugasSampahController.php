<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasSampahController extends Controller
{
    public function dashboard()
    {
        $worker = Auth::user()->worker;
        // Check if we need to load tps relationship
        $worker->load('tps');
        return view('petugas.dashboard', compact('worker'));
    }

    public function laporan()
    {
        $worker = Auth::user()->worker;
        $worker->load('tps');
        return view('petugas.laporan', compact('worker'));
    }

    public function storeLaporan(Request $request)
    {
        $request->validate([
            'status_angkut' => 'required|in:sudah,belum',
            'keterangan' => 'nullable|string'
        ]);

        $worker = Auth::user()->worker;

        \App\Models\TpsReport::create([
            'worker_id' => $worker->id,
            'tps_id' => $worker->tps_id,
            'status_angkut' => $request->status_angkut,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('petugas.dashboard')->with('success', 'Laporan berhasil dikirim!');
    }
}
