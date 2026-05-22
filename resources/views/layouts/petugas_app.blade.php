<x-layouts::app.header_petugas :title="$title ?? null">
    <flux:main container>
        {{ $slot }}
    </flux:main>
</x-layouts::app.header_petugas>
