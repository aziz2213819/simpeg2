<nav class="px-4 md:px-8 py-4 max-w-7xl mx-auto">
    <div class="flex items-center justify-between">

        {{-- LOGO --}}
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" class="h-12 md:h-14" />
                <span class="text-lg md:text-2xl font-bold dark:text-white">
                    DLH <span class="text-emerald-600">Care</span>
                </span>
            </div>
            {{-- <a href="{{ route('profil.dlh') }}"
               class="text-xs md:text-sm text-emerald-600 hover:text-emerald-500">
                Profil DLH
            </a> --}}
        </div>

        {{-- HAMBURGER (MOBILE) --}}
        <button onclick="toggleMenu()" class="md:hidden text-zinc-800 dark:text-white">
            ☰
        </button>

        {{-- MENU DESKTOP --}}
        <div class="hidden md:flex items-center gap-6 text-sm font-medium">

            <a href="{{ route('home') }}" class="hover:text-emerald-600 dark:text-white">Beranda</a>
            <a href="{{ route('pengaduan.create') }}" class="hover:text-emerald-600 dark:text-white">Buat laporan</a>
            <a href="{{ route('cek.status') }}" class="hover:text-emerald-600 dark:text-white">Cek laporan</a>
            <a href="{{ route('profil.dlh') }}" class="hover:text-emerald-600 dark:text-white">Tentang DLH</a>

            @auth
                <a href="{{ auth()->user()->employee_id ? '/homepage' : '/dashboard' }}"
                   class="px-4 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                    Dashboard
                </a>
            @else
                <flux:button href="{{ route('login') }}">
                    Login
                </flux:button>

                <flux:button href="{{ route('pengaduan.create') }}" variant="primary" class="bg-emerald-600 px-8 dark:text-white">
                    Lapor
                </flux:button>
            @endauth
        </div>
    </div>

    {{-- MENU MOBILE --}}
    <div id="mobileMenu" class="hidden flex-col mt-4 gap-3 md:hidden text-sm bg-white dark:bg-zinc-900 p-4 rounded-xl shadow border border-zinc-100 dark:border-zinc-800">

        <a href="{{ route('home') }}" class="block py-2 border-b dark:border-zinc-800 dark:text-white">Beranda</a>
        <a href="{{ route('pengaduan.create') }}" class="block py-2 border-b dark:border-zinc-800 dark:text-white">Buat laporan</a>
        <a href="{{ route('cek.status') }}#alur-lapor" class="block py-2 border-b dark:border-zinc-800 dark:text-white">Cek laporan</a>
        <a href="{{ route('profil.dlh') }}" class="block py-2 border-b dark:border-zinc-800 dark:text-white">Tentang DLH</a>

        @auth
            <a href="{{ auth()->user()->employee_id ? '/homepage' : '/dashboard' }}"
               class="block py-2 text-white bg-emerald-600 text-center rounded">
                Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="block py-2 border dark:border-zinc-700 text-center rounded dark:text-white">
                Login
            </a>

            <a href="{{ route('pengaduan.create') }}"
               class="block py-2 bg-emerald-600 text-white text-center rounded border border-transparent">
                Lapor
            </a>
        @endauth
    </div>
</nav>

<script>
function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}
</script>
