@volt

<?php

use function Livewire\Volt\{with, usesPagination, on,  state, updated};


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
        'users' => $users->get(),
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
])->url();

?>


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Presensi</title>
        <link rel="icon" type="image/png" href="/assets/img/favicon.ico">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media print {
                .only-screen {
                    display: none !important;
                }

                .only-print {
                    display: block !important;
                }
            }

            @media screen {
                .only-print {
                    display: none;
                }
            }
        </style>
        <style>
        @media print {
            .print-color {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        </style>

        <script src="/assets/js/sweatalert.min.js"></script>
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
@livewireScripts
        <div>
        

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
                    <div id="printNone" class="flex justify-between w-full py-5">
                        {{-- <div class="w-1/3 pe-3 ">

                            <input type="text" name="search" wire:model.live="search" class="w-full text-xs me-2 rounded-xl"
                                placeholder="cari..." id="search">
                        </div> --}}
                        <script>
                        function applyParam(key, value) {
                            const url = new URL(window.location.href);
                            const params = url.searchParams;

                            // Set atau update parameter
                            params.set(key, value);

                            // Redirect ke URL dengan parameter yang diperbarui
                            window.location.href = url.pathname + '?' + params.toString();
                        }
                        </script>

                        <div class="w-1/3 pe-3">

                          <select id="filter" class="w-full text-xs me-2 rounded-xl" onchange="applyParam('filter', this.value)">
                            <option value="">Semua</option>
                            <option value="ranap" {{ request('filter') == 'ranap' ? 'selected' : '' }}>Ranap</option>
                            <option value="igd" {{ request('filter') == 'igd' ? 'selected' : '' }}>Igd</option>
                            <option value="lab" {{ request('filter') == 'lab' ? 'selected' : '' }}>Lab</option>
                            <option value="radiologi" {{ request('filter') == 'radiologi' ? 'selected' : '' }}>Radiologi</option>
                            <option value="cssd" {{ request('filter') == 'cssd' ? 'selected' : '' }}>Cssd</option>
                            <option value="ambulan" {{ request('filter') == 'ambulan' ? 'selected' : '' }}>Supir Ambulan</option>
                            <option value="laundry" {{ request('filter') == 'laundry' ? 'selected' : '' }}>Laundry</option>
                            <option value="satpam" {{ request('filter') == 'satpam' ? 'selected' : '' }}>Satpam</option>
                            <option value="cs" {{ request('filter') == 'cs' ? 'selected' : '' }}>Cleaning Service</option>
                            <option value="manajemen" {{ request('filter') == 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                        </select>
                        </div>
                        {{-- <div class="w-1/3 pe-3">
                            <select class="w-full text-xs me-2 rounded-xl"  onchange="applyParam('year', this.value)" id="year">
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                            </select>
                        </div> --}}
                        <div class="w-1/3 pe-3">
                            <select class="w-full text-xs me-2 rounded-xl"  onchange="applyParam('month', this.value)" id="month">
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
                    <div class="text-center">
                                    <div class="mb-6 text-center">
                                        <div class="flex items-center justify-center space-x-4">
                                            <!-- Logo -->
                                            <img src="/assets/img/garut.jpg"class="w-auto h-20">

                                            <!-- Informasi Kop Surat -->
                                            <div class="text-center">
                                                <h1 class="text-lg font-bold uppercase">PEMERINTAH KABUPATEN GARUT</h1>
                                                <h2 class="font-semibold text-md">DINAS KESEHATAN</h2>
                                                <h3 class="font-semibold text-md">UOBK RSUD MALANGBONG</h3>
                                                <p class="text-sm">Jl. Raya Malangbong – Ciawi, Sukamanah, Kec. Malangbong,
                                                    Kabupaten Garut, Jawa Barat - 44188</p>
                                            </div>
                                        </div>

                                        <!-- Garis Pembatas -->
                                        <div class="mt-2 border-t-4 border-black"></div>
                                    </div>

                                </div>

                                <h2 class="mt-6 text-lg font-bold text-center">Laporan Absensi Bulan Ke- {{ $month }} Tahun 2025</h2>
                    <div class="p-10 overflow-x-auto scroll-container">
                    

                        @foreach ($users as $user)
                            <div class="flex gap-2 mt-5 whitespace-nowrap">
                                <div class="p-2 min-w-[300px] max-w-[300px] text-start text-xs font-semibold border rounded-md">
                                    <div class="flex items-center gap-x-3">
                                        <img class="object-cover w-10 h-10 rounded-full" src="/storage/{{ $user->foto }}"
                                            alt="">
                                        <div>
                                            <h2 class="text-gray-800 ">{{ $user->name }}</h2>
                                            <p class="text-sm text-yellow-600 dark:text-gray-400">
                                                {{ $user->jabatan }}</p>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $no = 0;
                                @endphp
                                <div class="grid grid-cols-7 gap-2">
                                    @foreach ($days as $day)
                                        @php

                                            $no++;
                                            $tanggal = \Carbon\Carbon::create($year, $month, $day);
                                            $hari = $tanggal->dayOfWeek;
                                            $formattedTanggal = $tanggal->format('Y-m-d');
                                            $formattedTanggal2 = $tanggal->format('d F Y');

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
                                            $s = $shift->last();
                                        @endphp

                                        <div class="relative">
                                            <div
                                                class="px-1 pt-3  min-w-[110px] max-w-[110px] text-center font-semibold border rounded-md 
                                                    {{ $tanggal->isToday() ? 'bg-blue-300 ' : 'bg-gray-200 text-gray-800' }} cursor-pointer">
                                                {{-- <small class="absolute top-0 left-0 text-[8px] ms-5 font-bold text-center px-1">
                                                    {{ $user->name }} <br>
                                                    {{ $formattedTanggal2 }}
                                                </small> --}}

                                                @if ($s)
                                                        <div 
                                                            class="relative w-full p-2 mb-1 text-[8px] bg-white border border-gray-200 rounded-xl text-start">
                                                        
                                                            
                                                            <p class="w-full mb-1 text-[8px] text-center border-b">
                                                                {{ $s->status_shift  }} <br/> 
                                                                {{ $formattedTanggal2 }}
                                                            
                                                            </p>
                                                            <p class="mb-2 text-[8px]">
                                                                <span class="p-[2px] print-color bg-blue-300 rounded-full">
                                                                    {{ $s->jam_masuk->format('H:i') }} </span>
                                                                -
                                                                <span
                                                                    class="p-[2px] print-color @if ($s->jam_pulang) bg-blue-300 @else bg-red-300 @endif rounded-full">
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
                                                                 <span class="p-[2px] print-color bg-yellow-300 text-[8px] rounded-full">
                                                                    {{ $telat }} </span>
                                                                @endif
                                                                @if($awal != '00:00')
                                                                <span class="p-[2px] print-color bg-yellow-300 text-[8px] rounded-full">
                                                                    {{ $awal }} </span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                @else
                                                    <div class="p-5 text-[8px] bg-red-200 print-color rounded-xl">
                                                        {{ $formattedTanggal2 }} <br>
                                                        Libur
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach


                    </div>


                </div>
            </div>
             <div class="flex justify-end">

                                    <div class="w-[400px] mt-6 text-center">
                                        <input value="@php Carbon\Carbon::setLocale('id'); echo "Garut, " . Carbon\Carbon::now()->translatedFormat('d F Y');@endphp" type="text" id="status" class="w-full font-bold text-center border-none outline-none focus:outline-none ring-none">                                        <p class="font-semibold">Mengetahui,</p>
                                        <input value="Kepala Seksi Penunjang Medis dan Farmasi" type="text" id="status" class="mt-2 mb-[100px] border-none w-full outline-none focus:outline-none ring-none text-center">
                                        <input value="Rahmat Budiana, S.Kep., Ners, MSi" type="text" id="status" class="w-full mt-6 font-bold text-center border-none outline-none focus:outline-none ring-none">
                                        <input value="NIP: 198204092005011004" type="text" id="status" class="w-full -mt-4 text-sm text-center border-none outline-none focus:outline-none ring-none">
                                       

                                    </div>
                                </div>

 <button
                                        class="w-full px-6 py-3 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-blue-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                                        id="printButton" onclick="hideAndPrint()">Print</button>

                                    <script>
                                        function hideAndPrint() {
                                            document.getElementById("printButton").style.display = "none";
                                            document.getElementById("printNone").style.display = "none";
                                            window.print();
                                            setTimeout(() => {
                                                document.getElementById("printButton").style.display = "block";
                                            }, 1000);
                                        }
                                    </script>

        </div>
    </body>
    </html>
    @endvolt
