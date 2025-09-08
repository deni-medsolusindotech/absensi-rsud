{{-- <x-app-layout> --}}
<?php

use function Laravel\Folio\{middleware};
use function Livewire\Volt\{with,mount, usesPagination, on, layout, state, updated};

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
        'logbook' => App\Models\Logbook::get(),
    ];
});

state([
    'today' => Carbon\Carbon::now()->day,
    'month' => now()->month ,
    'year' => now()->year,
    'types' => App\Models\UserType::get(),
    'search' => '',
    'filter' => '',
]);

mount(function(){

    if(session()->has('month')){
        $this->month = session('month');
    }
    if(session()->has('year')){
        $this->year = session('year');
    }
}
);

$ganti = function ($userid, $day, $status) {
    $masuk = Carbon\Carbon::createFromFormat('d', $day)->format('Y-m-d');
    $pulang = $status === 'Malam' ? Carbon\Carbon::parse($masuk)->addDay()->format('Y-m-d') : $masuk;

    App\Models\Schedule::updateOrCreate(
        [
            'user_id' => $userid,
            'tanggal_masuk' => $masuk,
        ],
        [
            'tanggal_pulang' => $pulang,
            'status' => $status,
            'updated_at' => now(),
        ],
    );
};

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
    <script src="/assets/js/sweatalert.min.js"></script>
</head>
<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

        <main>
            <div>

                @volt
                    
                    @if (auth()->user()->jabatan != 'manajemen')
                        <div class="p-5 bg-white">
                            <div class="w-full p-6 mx-auto border">
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

                                <h2 class="mt-6 text-lg font-bold text-center">Laporan Kegiatan</h2>

                                <table class="w-full mt-4 text-sm border">
                                    <tr>
                                        <td class="p-2 font-semibold border">Nama</td>
                                        <td class="p-2 border">{{ auth()->user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold border">Jabatan / Unit</td>
                                        <td class="p-2 border">{{ auth()->user()->jabatan }}</td>
                                    </tr>
                                </table>

                                <table class="w-full mt-4 text-sm border">
                                    <thead>
                                        <tr class="bg-gray-200">
                                            <th class="p-2 border">No.</th>
                                            <th class="p-2 border">Hari / Tanggal</th>
                                            <th class="p-2 border">Kegiatan</th>
                                            <th class="p-2 border">Penjelasan Pelaksanaan Kegiatan</th>
                                            <th class="p-2 border">Foto Kegiatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $tanggal = Carbon\Carbon::create($year, $month, 1);
                                            $awalBulan = $tanggal->startOfMonth()->toDateString();
                                            $akhirBulan = $tanggal->endOfMonth()->toDateString();

                                            $logbooks = App\Models\Logbook::where('user_id', auth()->user()->id)
                                                        ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
                                                        ->orderBy('tanggal', 'asc')
                                                        ->get();

$no = 1;
                                        @endphp
                                        @foreach ($logbooks as $logbook)
                                            <tr>
                                                <td class="p-2 text-center border">{{ $no++ }}</td>
                                                <td class="p-2 border">
                                                    {{ Carbon\Carbon::parse($logbook->tanggal)->translatedFormat('l, d F Y') }}
                                                </td>
                                                <td class="p-2 border">{{ $logbook->name }}</td>
                                                <td class="p-2 border">{{ $logbook->keterangan }}</td>
                                                <td class="p-2 border"> <img src="/storage/{{ $logbook->foto }}" class="h-20"
                                                        alt="">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="flex justify-end">

                                    <div class="w-[400px] mt-6 text-center">
                                        <input value="@php Carbon\Carbon::setLocale('id'); echo "Garut, " . Carbon\Carbon::now()->translatedFormat('d F Y');@endphp" type="text" id="status" class="w-full font-bold text-center border-none outline-none focus:outline-none ring-none">                                        <p class="font-semibold">Mengetahui,</p>
                                        <input value="Kepala Seksi Penunjang Medis dan Farmasi" type="text" id="status" class="mt-2 mb-[100px] border-none w-full outline-none focus:outline-none ring-none text-center">
                                        <input value="Rahmat Budiana, S.Kep., Ners, MSi" type="text" id="status" class="w-full mt-6 font-bold text-center border-none outline-none focus:outline-none ring-none">
                                        <input value="NIP: 198204092005011004" type="text" id="status" class="w-full -mt-4 text-sm text-center border-none outline-none focus:outline-none ring-none">
                                       

                                    </div>
                                </div>
                               <div>
                                        <button
                                        class="w-full px-6 py-3 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-blue-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                                        id="printButton" onclick="hideAndPrint()">Print</button>


                                    <script>
                                        function hideAndPrint() {
                                            document.getElementById("printButton").style.display = "none";
                                            window.print();
                                            setTimeout(() => {
                                                document.getElementById("printButton").style.display = "block";
                                            }, 1000);
                                        }
                                    </script>
                               </div>
                            </div>
                        </div>
                    @endif

                @endvolt

            </div>
        </main>
    </div>

</body>
</html>
