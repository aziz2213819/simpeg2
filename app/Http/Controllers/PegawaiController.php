<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;

class PegawaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // 1. Ambil 5 notifikasi terbaru
        $recentNotifs = Notification::where('employee_id', $employee->id)
            ->latest()
            ->take(5)
            ->get();

        // 2. Hitung Masa Kerja (Contoh format: 3 Tahun 2 Bulan)
        $masaKerja = '-';
        if ($employee->tmt_start) {
            $tmt = Carbon::parse($employee->tmt_start);
            $now = Carbon::now();
            $diff = $tmt->diff($now);
            $masaKerja = $diff->y . ' Tahun ' . $diff->m . ' Bulan';
        }

        return view('pegawai.dashboard', compact('user', 'employee', 'recentNotifs', 'masaKerja'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('pegawai.settings.profile', compact('user'));
    }

    public function updateEmail(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
        ]);

        $user->email = $validated['email'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('pegawai.profil')
            ->with('status', 'Email berhasil diperbarui.');
    }

    public function password()
    {
        $user = Auth::user();
        return view('pegawai.settings.password', compact('user'));
    }

    public function updatePassword(Request $request) {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()
            ],
        ], [
            'current_password.current_password' => 'Password lama yang Anda masukkan salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.mixed' => 'Password harus berupa huruf besar, huruf kecil, dan angka.',
            // 197002052003121004
        ]);
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        return back()->with('status', 'password-updated');
    }

    public function duafaktor() {
        $user = Auth::user();
        return view('pegawai.settings.two-factor', compact('user'));
    }

    public function enable2fa(Request $request, EnableTwoFactorAuthentication $enable)
    {
        $enable($request->user());
        return back()->with('status', '2fa-enabling');
    }

    public function disable2fa(Request $request, DisableTwoFactorAuthentication $disable)
    {
        $disable($request->user());
        return back()->with('status', '2fa-disabled');
    }

    public function confirm2fa(Request $request, ConfirmTwoFactorAuthentication $confirmAction)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            $confirmAction($request->user(), $request->code);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'code' => 'Kode yang Anda masukkan tidak valid.',
            ]);
        }

        return back()->with('status', '2fa-confirmed');
    }

    public function tampilan()
    {
        $user = Auth::user();
        return view('pegawai.settings.tampilan', compact('user'));
    }

    

    public function notification()
    {
        $user = Auth::user();
        $employee = $user->employee;

        $notifications = Notification::where('employee_id', Auth::user()->employee_id)
            ->where(function ($q) {
                $q->where('type', '!=', 'pangkat')
                ->orWhere(function ($q2) {
                    $q2->where('type', 'pangkat')
                        ->whereNotNull('status');
                });
            })
            ->latest()
            ->paginate(10);

        return view('pegawai.notifikasi.index', compact('user', 'notifications', 'employee'));
    }

    public function notificationShow(Notification $notification)
    {
        // Pastikan hanya pegawai yang bersangkutan yang bisa lihat
        if ($notification->employee_id !== auth()->user()->employee->id) {
            abort(403, 'Akses ditolak.');
        }
        if (!$notification->is_read) {
            $notification->is_read = true;
            $notification->save();
        }

        if ($notification->type != 'pangkat') {
            return redirect()->route('pegawai.notifikasi')->with('error', 'Notifikasi ini tidak dapat dibuka.');
        }

        return view('pegawai.notifikasi.show', compact('notification'));
    }

    public function notificationUpdate(Request $request, Notification $notification)
    {
        // Pastikan hanya pegawai yang bersangkutan yang bisa upload
        if ($notification->employee_id !== auth()->user()->employee->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($notification->type != 'pangkat') {
            return redirect()->route('pegawai.notifikasi')->with('error', 'Notifikasi ini tidak dapat dibuka.');
        }

        $request->validate([
            // Hanya izinkan PDF atau Gambar, maksimal 5MB
            'sk_file' => 'required|mimes:pdf|max:5120',
        ], [
            'sk_file.required' => 'Anda harus memilih file terlebih dahulu.',
            'sk_file.mimes' => 'Format file harus PDF.',
        ]);

        // Hapus file lama jika pegawai mengunggah ulang (opsional, agar storage tidak penuh)
        if ($notification->sk_file_path) {
            Storage::disk('public')->delete($notification->sk_file_path);
        }

        // Simpan ke folder storage/app/public/sk_dokumen
        $path = $request->file('sk_file')->store('sk_dokumen', 'public');

        $notification->update([
            'sk_file_path' => $path,
            'submitted_at' => now(),
            'status' => 'pending',
            'is_read' => true,
        ]);
        return back()->with('success', 'Berkas SK berhasil diunggah dan dikirim ke sistem dan notifikasi ditandai sudah dibaca.');
    }

    public function read($id)
    {
        // Pastikan hanya pegawai yang bersangkutan yang bisa lihat
        if (Notification::findOrFail($id)->employee_id !== auth()->user()->employee->id) {
            abort(403, 'Akses ditolak.');
        }
        $notif = Notification::findOrFail($id);
        $notif->update([
            'is_read' => true
        ]);
        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }
}
