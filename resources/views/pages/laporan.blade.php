<?php

use function Laravel\Folio\{middleware};
use function Livewire\Volt\{with, usesPagination, on, layout, state, updated};

layout('components.layouts.app');
 
with(function () {
    $users = App\Models\User::latest();
    if ($this->search) {
        $users
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('jabatan', 'like', '%' . $this->search . '%');
    }
    if ($this->filter) {
        $users->where('jabatan', $this->filter);
    }

    if (auth()->user()->jabatan != 'manajemen') {
        $users->where('id', auth()->user()->id);
    }

    return [
        'users' => $users->paginate(10),
        'days' => range(1, Carbon\Carbon::create(null, 1)->daysInMonth),
        'absen' => App\Models\Absen::get(),
    ];
});

state([
    'search' => '',
    'filter' => '',
    'today' => Carbon\Carbon::now()->day,
    'month' => now()->month,
    'year' => now()->year,
    'types' => App\Models\UserType::get(),
]);

?>
<x-slot name="header">
    <div class="flex justify-between w-full">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Laporan Absensi Karyawan
        </h2>

    </div>
</x-slot>

<div>
    <div class="w-full text-white bg-blue-500">
        <div class="container flex items-center justify-between px-6 py-4 mx-auto">
            <div class="flex">
                <svg viewBox="0 0 40 40" class="w-6 h-6 fill-current">
                    <path
                        d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z">
                    </path>
                </svg>

                <p class="mx-3">Sedang Tahap Pengembangan .</p>
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

    <script>
        function left() {
            document.querySelector('.scroll-container').scrollBy({
                left: -500,
                behavior: 'smooth'
            });
        }

        function right() {
            document.querySelector('.scroll-container').scrollBy({
                left: 500,
                behavior: 'smooth'
            });
        }
    </script>

    <div class="p-6">
        @if (auth()->user()->jabatan == 'manajemen')
            <div class="flex justify-between w-full py-5">
                <div class="w-1/3 pe-3 ">

                    <input type="text" name="search" wire:model.live="search" class="w-full text-xs me-2 rounded-xl"
                        placeholder="cari..." id="search">
                </div>
                <div class="w-1/3 pe-3">

                    <select type="text" name="filter" wire:model.live="filter"
                        class="w-full text-xs me-2 rounded-xl" placeholder="cari..." id="filter">
                        <option value="">Semua </option>
                        <option value="ranap">Ranap</option>
                        <option value="igd">Igd</option>
                        <option value="lab">Lab</option>
                        <option value="radiologi">Radiologi</option>
                        <option value="cssd">Cssd</option>
                        <option value="ambulan">Supir Ambulan</option>
                        <option value="laundry">Laundry</option>
                        <option value="satpam">Satpam</option>
                        <option value="cs">Cleaning Service</option>
                        <option value="manajemen">managemen</option>
                    </select>
                </div>
                <div class="w-1/3 pe-3">
                    <select class="w-full text-xs me-2 rounded-xl" wire:model.live="year" id="year">
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                    </select>
                </div>
                <div class="w-1/3 pe-3">
                    <select class="w-full text-xs me-2 rounded-xl" wire:model.live="month" id="month">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
            </div>
        @endif
        <div class="relative">
            <button onclick="left()" class="absolute left-0 z-10 p-2 bg-gray-200 rounded-full shadow-md -top-2">
                ◀
            </button>


            <!-- Tombol Geser Kanan -->
            <button onclick="right()" class="absolute right-0 z-10 p-2 bg-gray-200 rounded-full shadow-md -top-2">
                ▶
            </button>
            <div class="p-10 overflow-x-auto scroll-container">
                <div class="flex gap-2 whitespace-nowrap">
                    <div class="p-3 min-w-[300px] max-w-[300px] text-center font-semibold rounded-md"></div>
                    @foreach ($days as $day)
                        @php
                            Carbon\Carbon::setLocale('id');
                            $tanggal = \Carbon\Carbon::create($year, $month, $day);
                        @endphp
                        <div
                            class="p-3 min-w-[110px] max-w-[110px] text-center font-semibold border rounded-md 
                                {{ $day == $today ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                            {{ str_pad($day, 2, '0', STR_PAD_LEFT) }} <br>
                            <span class="text-[8px]"> {{ $tanggal->translatedFormat('l, d M Y') }}</span>
                        </div>
                    @endforeach
                </div>

                @foreach ($users as $user)
                    <div class="flex gap-2 mt-5 whitespace-nowrap">
                        <div class="p-2 min-w-[300px] max-w-[300px] text-start text-xs font-semibold border rounded-md">
                            <div class="flex items-center gap-x-3">
                                <img class="object-cover w-10 h-10 rounded-full" src="/storage/{{ $user->foto }}"
                                    alt="">
                                <div>
                                    <h2 class="text-gray-800 dark:text-white">{{ $user->name }}</h2>
                                    <p class="text-sm text-yellow-600 dark:text-gray-400">
                                        {{ $user->jabatan }}</p>
                                </div>
                            </div>
                        </div>
                        @foreach ($days as $day)
                            @php
                                $tanggal = \Carbon\Carbon::create($year, $month, $day);
                                $hari = $tanggal->dayOfWeek;
                                $formattedTanggal = $tanggal->format('Y-m-d');

                                $shift = $user->allabsen->filter(function ($absen) use ($formattedTanggal) {
                                    return \Carbon\Carbon::parse($absen->jam_masuk)->format('Y-m-d') ===
                                        $formattedTanggal;
                                });

                                // Menentukan ikon shift
                                $shiftIcons = [
                                    'pagi' => '🌅',
                                    'siang' => '🌞',
                                    'malam' => '🌙',
                                    'malam-cs' => '🌙',
                                    'pagi-cs' => '🌅',
                                    'malam-satpam' => '🌙',
                                    'pagi-satpam' => '🌅',
                                    'nonshift' => '🌅',
                                    'nonshift-puasa' => '🌅',
                                    'libur' => '❌',
                                ];
                            @endphp

                            <div class="relative">
                                <div
                                    class="px-1 pt-3  min-w-[110px] max-w-[110px] text-center font-semibold border rounded-md 
                                        {{ $tanggal->isToday() ? 'bg-blue-300 ' : 'bg-gray-200 text-gray-800' }} cursor-pointer">
                                    <small class="absolute top-0 left-0 text-[8px] text-start px-1">
                                        {{ $user->name }}
                                    </small>

                                    @if ($shift->count())
                                        @foreach ($shift as $s)
                                            <div 
                                                class="relative w-full p-2 mb-1 text-xs bg-white border border-gray-200 rounded-xl text-start">
                                               
                                                   <p class="absolute -right-4 -top-4">
                                                        <img id="openModal{{ $s->id }}" class="object-cover w-8 h-8 border-2 border-blue-300 rounded-full"
                                                            src="/storage/{{ ($s->foto_pulang) ? $s->foto_pulang : $s->foto_masuk }}" alt="">
                                                    </p>
                                                
                                                    <!-- Modal -->
                                                    <div id="modal{{ $s->id }}" class="fixed inset-0 z-10 hidden overflow-y-auto bg-black bg-opacity-50">
                                                        <div class="flex items-center justify-center min-h-screen">
                                                            <div class="p-6 bg-white rounded-lg shadow-lg">
                                                                <h2 class="text-lg font-semibold">Foto Masuk dan Pulang</h2>
                                                                <div class="justify-between w-full">
                                                                    <img src="/storage/{{ $s->foto_masuk }}" class="w-full" alt="">
                                                                    <img src="/storage/{{ $s->foto_pulang }}" class="w-full" alt="">
                                                                </div>
                                                                <button id="closeModal{{ $s->id }}" class="px-4 py-2 mt-4 text-white bg-red-500">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                
                                                <script>
                                                    document.addEventListener("DOMContentLoaded", function () {
                                                        let openModal = document.getElementById("openModal{{ $s->id }}");
                                                        let closeModal = document.getElementById("closeModal{{ $s->id }}");
                                                        let modal = document.getElementById("modal{{ $s->id }}");
                                                
                                                        openModal.addEventListener("click", function () {
                                                            modal.classList.remove("hidden");
                                                        });
                                                
                                                        closeModal.addEventListener("click", function () {
                                                            modal.classList.add("hidden");
                                                        });
                                                    });
                                                </script>
                                                
                                                <p class="w-full mb-1 text-center border-b">
                                                    {{ $s->status_shift . $shiftIcons[$s->status_shift] }}
                                                </p>
                                                <p class="mb-2">
                                                    <span class="p-[2px] bg-blue-300 rounded-full">
                                                        {{ $s->jam_masuk->format('H:i') }} </span>
                                                    -
                                                    <span
                                                        class="p-[2px] @if ($s->jam_pulang) bg-blue-300 @else bg-red-300 @endif rounded-full">
                                                        {{ $s->jam_pulang ? $s->jam_pulang->format('H:i') : 'Belum' }}
                                                    </span>


                                                </p>
                                                <p>
                                                    @php
                                                        $text = $s->telat;

                                                        // Ambil angka dari teks
                                                        preg_match('/(\d+)\s*jam/', $text, $jam);
                                                        preg_match('/(\d+)\s*menit/', $text, $menit);

                                                        // Konversi ke format TIME
                                                        $jam = isset($jam[1]) ? (int) $jam[1] : 0;
                                                        $menit = isset($menit[1]) ? (int) $menit[1] : 0;

                                                        $telat = sprintf('%02d:%02d', $jam, $menit);

                                                        $text = $s->pulang_awal;

                                                        // Ambil angka dari teks
                                                        preg_match('/(\d+)\s*jam/', $text, $jam);
                                                        preg_match('/(\d+)\s*menit/', $text, $menit);

                                                        // Konversi ke format TIME
                                                        $jam = isset($jam[1]) ? (int) $jam[1] : 0;
                                                        $menit = isset($menit[1]) ? (int) $menit[1] : 0;

                                                        $awal = sprintf('%02d:%02d', $jam, $menit);

                                                    @endphp
                                                    @if($telat != '00:00')
                                                    <span class="p-[2px] bg-yellow-300 rounded-full">
                                                        {{ $telat }} </span>
                                                    @endif
                                                    @if($awal != '00:00')
                                                    <span class="p-[2px] bg-yellow-300 rounded-full">
                                                        {{ $awal }} </span>
                                                    @endif
                                                </p>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="p-5 text-xs">
                                            Libur
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach


            </div>
            <div>
                {{ $users->links() }}
            </div>


        </div>
    </div>




</div>
