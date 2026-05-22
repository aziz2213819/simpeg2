<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Comment; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar semua pengaduan (Sisi Dashboard Admin)
     * Digunakan untuk memanajemen laporan oleh petugas.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $type = $request->input('tipe_sampah');

        $reports = Report::query()
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('nama_pelapor', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhere('lokasi_manual', 'like', "%{$search}%")
                      ->orWhere('tracking_id', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($type, function ($query, $type) {
                return $query->where('tipe_sampah', $type);
            })
            ->withCount('comments') // Menghitung jumlah komentar otomatis (kolom virtual: comments_count)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengaduan.index', compact('reports', 'search', 'status', 'type'));
    }

    /**
     * Menampilkan daftar pengaduan (Sisi Portal Warga / Landing Page)
     * Method ini digunakan untuk menampilkan transparansi ke masyarakat.
     */
    public function portal()
    {
        // Mengambil semua data pengaduan untuk ditampilkan di landing page.
        // withCount('comments') wajib ada agar variabel $item->comments_count tersedia di Blade.
        $allReports = Report::withCount('comments')
            ->latest()
            ->get();
        
        // Pastikan 'welcome' adalah nama file blade portal/landing page Anda.
        return view('welcome', compact('allReports')); 
    }

    /**
     * Menampilkan detail pengaduan dan diskusi komentar.
     * Menggunakan Route Model Binding (Report $pengaduan).
     */
    public function show(Report $pengaduan)
    {
        // Eager load relasi agar performa lebih cepat dan data muncul.
        // comments.user diasumsikan agar Anda bisa menampilkan siapa yang memberi tanggapan.
        $pengaduan->load(['user', 'comments.user']);
        
        // Dikirim sebagai 'item' agar sesuai dengan kodingan Blade Anda sebelumnya.
        return view('pengaduan.show', ['item' => $pengaduan]);
    }

    /**
     * Menyimpan komentar/tanggapan baru dari Admin.
     */
    public function storeComment(Request $request, Report $pengaduan)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        // Simpan komentar ke database.
        // Pastikan foreign key di database Anda adalah 'report_id'.
        Comment::create([
            'report_id' => $pengaduan->id,
            'user_id'   => Auth::id(), // ID Admin/Petugas yang sedang login
            'body'      => $request->body,
        ]);

        return back()->with('success', 'Tanggapan berhasil dikirim ke publik!');
    }

    /**
     * Memperbarui status laporan (Pending/Proses/Selesai).
     */
    public function updateStatus(Request $request, Report $pengaduan)
    {
        $request->validate([
            'status' => 'required|in:pending,proses,selesai'
        ]);
        
        $pengaduan->update([
            'status' => $request->status
        ]);
        
        return back()->with('success', 'Status laporan #' . $pengaduan->tracking_id . ' diperbarui menjadi ' . $request->status);
    }

    /**
     * Menghapus data laporan pengaduan beserta file fotonya.
     */
    public function destroy(Report $pengaduan)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($pengaduan) {
                // 1. Hapus file foto dari storage jika ada untuk menghemat kapasitas server.
                if ($pengaduan->foto_bukti && Storage::disk('public')->exists($pengaduan->foto_bukti)) {
                    Storage::disk('public')->delete($pengaduan->foto_bukti);
                }

                // 2. Explicitly delete comments before deleting the report
                $pengaduan->comments()->delete();

                // 3. Hapus data laporan.
                $pengaduan->delete();
            });

            return redirect()->route('admin.pengaduan.index')->with('success', 'Laporan berhasil dihapus secara permanen.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }
}