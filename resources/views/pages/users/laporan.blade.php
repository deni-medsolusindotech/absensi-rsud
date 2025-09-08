{{-- <x-app-layout> --}}
<?php

use function Laravel\Folio\{middleware};
use function Livewire\Volt\{with, usesPagination, on, layout, state};
on([
    'hapus' => function ($id) {
        $user = App\Models\User::findOrFail($id);
        $user->delete();
        return $this->redirect('/users', true);
    },
]);
state([
    'search' => '',
    'filter' => '',
])->url();

with(function(){
    $users = App\Models\User::latest();
    if ($this->search) {
        $users->where('name', 'like', '%' . $this->search . '%')->orWhere('email', 'like', '%' . $this->search . '%')->orWhere('jabatan', 'like', '%' . $this->search . '%');
    }
    if ($this->filter) {
        $users->where('jabatan', $this->filter); 
    }
    return [
        'users' => $users->paginate(10)
    ];
});

?>

@volt
<x-layouts.app>
<x-slot name="header">
    <div class="flex justify-between w-full">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Daftar Karyawan
        </h2>
        @if (auth()->user()->jabatan == 'manajemen')
            <a href="/users/tambah" wire:navigate class="px-3 py-2 text-xs text-white bg-blue-500 rounded-md">Tambah
                Karyawan</a>
    </div>
    @endif
</x-slot>
<div>
    @if (auth()->user()->jabatan == 'manajemen')
        <script>
            function hapus(id, name) {
                Swal.fire({
                    title: "Anda yakin?",
                    html: `<b>${name}</b> akan dihapus!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal",
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            Livewire.dispatch('hapus', [id]);
                            resolve();
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Terhapus!",
                            html: `<b>${name}</b> berhasil dihapus.`,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        </script>


        <section class="container mx-auto">
            <div class="px-10 mt-6">
                <div class="flex justify-between w-full p-2">
                    <input type="text" name="search" wire:model.live="search" class="w-1/3 px-4 py-2 rounded-xl" placeholder="cari..." id="search">

                    <select 
                    name="filter" 
                    class="w-1/3 px-4 py-2 rounded-xl" 
                    id="filter"
                    onchange="redirectFilter(this)">
                    <option value="">Semua</option>
                    <option value="ranap">Ranap</option>
                    <option value="rawat_jalan">Rawat jalan</option>
                    <option value="farmasi">Farmasi</option>
                    <option value="administrasi">Administrasi</option>
                    <option value="igd">IGD</option>
                    <option value="lab">Lab</option>
                    <option value="radiologi">Radiologi</option>
                    <option value="cssd">CSSD</option>
                    <option value="ambulance">Supir Ambulance</option>
                    <option value="laundry">Laundry</option>
                    <option value="satpam">Satpam</option>
                    <option value="cs">Cleaning Service</option>
                    <option value="manajemen">Manajemen</option>
                </select>

                <script>
                function redirectFilter(selectElement) {
                    const value = selectElement.value;
                    const baseUrl = "https://192.168.22.251/users/laporan";
                    window.location.href = value ? `${baseUrl}?filter=${value}` : baseUrl;
                }
                </script>
                </div>
                <div class="overflow-hidden border rounded-lg dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3.5 text-sm font-normal text-left text-gray-500 dark:text-gray-400">
                                    Nama</th>
                                <th class="px-4 py-3.5 text-sm font-normal text-left text-gray-500 dark:text-gray-400">
                                    Email</th>
                                <th class="relative py-3.5 px-4"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900">
                            @forelse ($users as $user)
                                <tr wire:loading.remove>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-700">
                                        <div class="flex items-center gap-x-3">
                                            <img class="object-cover w-10 h-10 rounded-full"
                                                src="/storage/{{ $user->foto }}" alt="">
                                            <div>
                                                <h2 class="text-gray-800 dark:text-white">{{ $user->name }}</h2>
                                                <p class="text-sm text-yellow-600 dark:text-gray-400">
                                                    {{ $user->jabatan }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}
                                    </td>
                                    <td class="flex justify-start px-4 py-4 text-sm whitespace-nowrap">
                                        <a wire:navigate href="/users/{{ $user->id }}"
                                            class="text-gray-500 transition-colors duration-200 me-3 dark:hover:text-yellow-500 dark:text-gray-300 hover:text-yellow-500 focus:outline-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>

                                        <button type="button"
                                            onclick="hapus({{ $user->id }}, '{{ $user->name }}')"
                                            class="text-red-500 hover:text-red-600 dark:text-gray-300 dark:hover:text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td wire:loading.remove colspan="8" class="p-10 text-center">
                                    Tidak Ada Data
                                </td>
                            </tr>
                            @endforelse
                            <tr wire:loading>
                                <td colspan="8" class="p-10 text-center animate-pulse">
                                    Loading ...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-5">
                    {{ $users->links() }}
                </div>
            </div>
        </section>
    @else
        <section class="container mx-auto">
            <div class="w-full text-white bg-red-500">
                <div class="container flex items-center justify-between px-6 py-4 mx-auto">
                    <div class="flex">
                        <svg viewBox="0 0 40 40" class="w-6 h-6 fill-current">
                            <path
                                d="M20 3.36667C10.8167 3.36667 3.3667 10.8167 3.3667 20C3.3667 29.1833 10.8167 36.6333 20 36.6333C29.1834 36.6333 36.6334 29.1833 36.6334 20C36.6334 10.8167 29.1834 3.36667 20 3.36667ZM19.1334 33.3333V22.9H13.3334L21.6667 6.66667V17.1H27.25L19.1334 33.3333Z">
                            </path>
                        </svg>

                        <p class="mx-3">Anda Tidak Punya Akses.</p>
                    </div>

                    <button
                        class="p-1 transition-colors duration-300 transform rounded-md hover:bg-opacity-25 hover:bg-gray-600 focus:outline-none">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    @endif
</div>
</x-layouts.app>
@endvolt
