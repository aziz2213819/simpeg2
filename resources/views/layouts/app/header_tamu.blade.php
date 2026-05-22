<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>
        {{ filled($title ?? null) ? $title.' - '.'Dinas Lingkungan Hidup' }}
    </title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    {{-- <link rel="icon" href="/favicon.svg" type="image/svg+xml"> --}}
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="home" :href="route('pegawai.homepage')"
                :current="request()->routeIs('pegawai.homepage')" wire:navigate>Beranda</flux:navbar.item>
            <flux:navbar.item icon="bell" badge="12" :href="route('pegawai.notifikasi')"
                :current="request()->routeIs('pegawai.notifikasi')" wire:navigate>Notifikasi</flux:navbar.item>
        </flux:navbar>
        <flux:spacer />
        <flux:navbar class="me-4">
            <flux:navbar.item class="max-lg:hidden" icon="cog-6-tooth" :href="route('pegawai.profil')" label="Settings"
                wire:navigate />
        </flux:navbar>
        <flux:dropdown position="top" align="start">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />
            <flux:menu>
                <flux:menu.item class="w-full cursor-pointer" :href="route('pegawai.profil')" icon="user" wire:navigate>
                    Profil
                </flux:menu.item>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Keluar') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>
    <flux:sidebar sticky collapsible="mobile"
        class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand href="#" logo="https://fluxui.dev/img/demo/logo.png"
                logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png" name="SIMPEG" />
            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>
        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" :href="route('pegawai.homepage')"
                :current="request()->routeIs('pegawai.homepage')" wire:navigate>Beranda</flux:sidebar.item>
            <flux:sidebar.item icon="bell" badge="12" :href="route('pegawai.notifikasi')"
                :current="request()->routeIs('pegawai.notifikasi')" wire:navigate>Notifikasi</flux:sidebar.item>
        </flux:sidebar.nav>
        <flux:sidebar.spacer />
        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" :href="route('pegawai.profil')" wire:navigate>Settings
            </flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>

    {{ $slot }}

    @fluxScripts
</body>

</html>