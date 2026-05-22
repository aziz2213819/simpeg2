<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $workers = Worker::with('tps')
            ->when($search, function ($query, $search) {
                return $query->where('nama_petugas', 'like', "%{$search}%")
                    ->orWhere('plat_nomor', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.workers.index', compact('workers', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tpsList = Tps::all();

        return view('admin.workers.create', compact('tpsList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_petugas' => 'required|string|max:255',
            'jenis_kendaraan' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:255',
            'tps_id' => 'required|exists:tps,id',
        ]);

        DB::transaction(function () use ($validated) {
            $worker = Worker::create($validated);

            $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', $worker->nama_petugas));
            // $uniqueUsername = $cleanName . rand(100, 999);

            User::create([
                'name' => $worker->nama_petugas,
                'worker_id' => $worker->id,
                'email' => "{$cleanName}@gmail.com",
                'password' => bcrypt(env('DEFAULT_WORKER_PASSWORD')),
                'role' => 'petugas_sampah',
            ]);
        });

        return redirect()->route('workers.index')->with('success', 'Petugas dan Akun berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker)
    {
        return view('admin.workers.show', compact('worker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        $tpsList = Tps::all();

        return view('admin.workers.edit', compact('worker', 'tpsList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Worker $worker)
    {
        $validated = $request->validate([
            'nama_petugas' => 'required|string|max:255',
            'jenis_kendaraan' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:255',
            'tps_id' => 'required|exists:tps,id',
        ]);

        DB::transaction(function () use ($validated, $worker) {
            $worker->update($validated);

            $user = User::where('worker_id', $worker->id)->first();
            if ($user) {
                $user->update([
                    'name' => $validated['nama_petugas'],
                ]);
            }
        });

        return redirect()->route('workers.index')->with('success', 'Petugas dan Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        DB::transaction(function () use ($worker) {
            User::where('worker_id', $worker->id)->delete();
            $worker->delete();
        });

        return redirect()->route('workers.index')->with('success', 'Petugas dan Akun berhasil dihapus.');
    }
}
