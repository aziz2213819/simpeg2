<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="home" :href="route('petugas.dashboard')"
                :current="request()->routeIs('petugas.dashboard')" wire:navigate>Dashboard</flux:navbar.item>
            <flux:navbar.item icon="document-text" :href="route('petugas.laporan')"
                :current="request()->routeIs('petugas.laporan')" wire:navigate>Laporan TPS</flux:navbar.item>
        </flux:navbar>
        <flux:spacer />
        <flux:navbar class="me-4">
            {{-- Setting profile not strictly required for petugas in this iteration, but left as placeholder --}}
            <flux:navbar.item class="max-lg:hidden" icon="cog-6-tooth" href="#" label="Settings" />
        </flux:navbar>
        <flux:dropdown position="top" align="start">
            <flux:profile initials="{{ Str::substr(auth()->user()->worker->nama_petugas ?? 'P', 0, 1) }}" name="{{ auth()->user()->name }}"
                icon-trailing="chevron-down" />
            <flux:menu>
                <flux:menu.item class="w-full cursor-pointer" href="#" icon="user">
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
            <flux:sidebar.item icon="home" :href="route('petugas.dashboard')"
                :current="request()->routeIs('petugas.dashboard')" wire:navigate>Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="document-text" :href="route('petugas.laporan')"
                :current="request()->routeIs('petugas.laporan')" wire:navigate>Laporan TPS</flux:sidebar.item>
        </flux:sidebar.nav>
        <flux:sidebar.spacer />
        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="#">Settings
            </flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>

    {{ $slot }}

    @fluxScripts
</body>

</html>