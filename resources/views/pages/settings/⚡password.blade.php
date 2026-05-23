<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Password settings')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function messages(): array
    {
        return [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.mixed' => 'Password harus memiliki huruf besar dan kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
        ];
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Password Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Perbarui Password')" :subheading="__('Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <flux:input wire:model="current_password" :label="__('Password saat ini')" type="password" required
                autocomplete="current-password" />
            <flux:input wire:model="password" :label="__('Password baru')" type="password" required
                autocomplete="new-password" />
            <flux:input wire:model="password_confirmation" :label="__('Konfirmasi password baru')" type="password" required
                autocomplete="new-password" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" color="emerald" type="submit" class="w-full"
                        data-test="update-password-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>