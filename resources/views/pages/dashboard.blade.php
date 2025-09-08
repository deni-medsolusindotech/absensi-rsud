<?php

use function Livewire\Volt\{with, usesFileUploads, on, layout, state, mount};
use Carbon\Carbon;
layout('components.layouts.app');
usesFileUploads();
Carbon::setLocale('id');
$data = App\Models\Absen::whereUserId(auth()->user()->id)
    ->latest()
    ->first();

if ($data && $data->foto_masuk && $data->foto_pulang && $data->updated_at->toDateString() !== now()->toDateString()) {
    $data = null;
}
state([
    'masuk' => $data ? $data->jam_masuk : false,
    'pulang' => $data ? $data->jam_pulang : false,
    'data' => $data,
    'shiftIcon' => [
        'nonshift-puasa' => '🌅',
        'pagi' => '🌅',
        'siang' => '🌞',
        'malam' => '🌙',
        'malam-cs' => '🌙',
        'pagi-cs' => '🌅',
        'malam-satpam' => '🌙',
        'pagi-satpam' => '🌅',
	'sore-secwan' => '🌙',
        'pagi-secwan' => '🌅',
        'nonshift' => '❌',
        'libur' => '❌',
    ],
    'pesan' => '',
    'shiftOptions' => [
        // 'nonshift-puasa' => 'Non shift Puasa',
        'nonshift' => 'Non shift',
        'pagi' => 'Pagi',
        'siang' => 'Siang',
        'malam' => 'Malam',
        'pagi-cs' => 'Pagi-cs',
        'malam-cs' => 'Malam-cs',
        'pagi-satpam' => 'Pagi-satpam',
        'malam-satpam' => 'Malam-satpam',
	'pagi-secwan' => 'Pagi-Secwan',
	'sore-secwan' => 'Sore-Secwan',
    ],
    'shiftTime' => [
        // 'nonshift-puasa' => ['06:30', '14:00'],
        'nonshift' => ['07:30', '16:00'],
        'pagi' => ['07:30', '14:00'],
        'siang' => ['14:00', '20:00'],
        'malam' => ['20:00', '07:30'],
        'pagi-cs' => ['06:00', '18:00'],
        'malam-cs' => ['18:00', '06:00'],
        'pagi-satpam' => ['07:00', '19:00'],
        'malam-satpam' => ['19:00', '07:00'],
	'pagi-secwan' => ['07:00','15:00'],
	'sore-secwan' => ['14:00','22:00'],
        'ambulance' => ['08:00', '08:00'],
    ],
    'selectedShift' => $data ? $data->status_shift : '',
    'status' => $data ? 'pulang' : 'masuk',
]);
$capture = function ($data) {
    $image = str_replace('data:image/png;base64,', '', $data);
    $image = str_replace(' ', '+', $image);
    $imageName = 'absensi_'.auth()->user()->name . time() . '.png';

    Storage::disk('public')->put('absensi/' . $imageName, base64_decode($image));

    if ($this->status == 'pulang') {
        $now = Carbon::now(); // Waktu sekarang
        $jamPulangShift = Carbon::createFromFormat('H:i', $this->shiftTime[$this->selectedShift][1]); // Jam pulang shift
        $pulangLebihAwal = null; // Default tidak pulang lebih awal

        if ($now->lt($jamPulangShift)) {
            $diff = $jamPulangShift->diff($now);

            $jam = $diff->h; // Ambil jumlah jam
            $menit = $diff->i; // Ambil jumlah menit

            if ($jam > 0 && $menit > 0) {
                $pulangLebihAwal = "{$jam} jam {$menit} menit";
            } elseif ($jam > 0) {
                $pulangLebihAwal = "{$jam} jam";
            } else {
                $pulangLebihAwal = "{$menit} menit";
            }
        }

        $data = App\Models\Absen::whereUserId(auth()->user()->id)
            ->latest()
            ->first();
        if ($data->foto_pulang) {
            $fotoLama = public_path($data->foto_pulang);
            if (file_exists($fotoLama)) {
                unlink($fotoLama); // Hapus file lama
            }
        }
        $data->update([
            'foto_pulang' => 'absensi/' . $imageName,
            'jam_pulang' => Carbon::now(),
            'pulang_awal' => $pulangLebihAwal,
        ]);
    } else {
        $now = Carbon::now(); // Waktu sekarang
        $jamMasukShift = Carbon::createFromFormat('H:i', $this->shiftTime[$this->selectedShift][0]); // Jam masuk shift

        // Hitung keterlambatan (hanya jika sekarang lebih dari jam pulang shift)
        if ($now->gt($jamMasukShift)) {
            $diff = $jamMasukShift->diff($now);

            $jam = $diff->h; // Ambil jumlah jam
            $menit = $diff->i; // Ambil jumlah menit

            if ($jam > 0 && $menit > 0) {
                $telat = "{$jam} jam {$menit} menit";
            } elseif ($jam > 0) {
                $telat = "{$jam} jam";
            } else {
                $telat = "{$menit} menit";
            }
        } else {
            $telat = null; // Tidak telat
        }

        App\Models\Absen::create([
            'user_id' => auth()->user()->id,
            'status_shift' => $this->selectedShift,
            'jam_masuk_shift' => Carbon::now(),
            'jam_pulang_shift' => Carbon::now(),
            'foto_masuk' => 'absensi/' . $imageName,
            'jam_masuk' => Carbon::now(),
            'foto_pulang' => null,
            'jam_pulang' => null,
            'telat' => $telat,
            'pulang_awal' => null,
        ]);
    }

    session()->flash('message', 'Absensi berhasil!');
    return $this->redirect('/dashboard');
};

$masuklagi = function () {
    $this->data = null;
    $this->masuk = false;
    $this->pulang = false;
    $this->status = 'masuk';
};
?>

<div >
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Absen Masuk') }}
        </h2>
    </x-slot>
    <div class="relative  w-full min-h-[calc(100vh-78px)] overflow-hidden bg-slate-200">
        <div class="p-2 my-7">
            <div class="flex w-full max-w-sm overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800">
                <div class="w-2 bg-gray-800 dark:bg-gray-900"></div>

                <div class="flex items-center px-2 py-3">
                    <img class="object-cover w-20 h-20 border-2 border-gray-600 rounded-full" alt="User avatar"
                        src="/storage/{{ auth()->user()->foto }}">

                    <div class="mx-3">
                        <p class="text-xl text-gray-900 dark:text-gray-200">{{ auth()->user()->name }} .</p>
                        <p class="text-gray-600 dark:text-gray-200">{{ auth()->user()->email }} .</p>
                        <p class="text-gray-600 dark:text-gray-200">{{ auth()->user()->jabatan }} .</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="@if (!$data) flex justify-center @endif py-2 my-2">
            @if ($data && $data->foto_pulang && $data->foto_masuk)

                <div wire:click="masuklagi()" class="w-full -mt-10 text-white cursor-pointer hover:animate-bounce mb-11 bg-emerald-500">
                    <div class="container flex items-center justify-between px-6 py-4 mx-auto">
                        <div class="flex">
                            <svg viewBox="0 0 40 40" class="w-6 h-6 fill-current">
                                <path
                                    d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM16.6667 28.3333L8.33337 20L10.6834 17.65L16.6667 23.6166L29.3167 10.9666L31.6667 13.3333L16.6667 28.3333Z">
                                </path>
                            </svg>

                            <p class="mx-3">Anda Sudah Pulang .. Masuk Lagi ? klik ini</p>
                        </div>


                    </div>
                </div>
            @endif
            @if ($selectedShift)
                
            <div @if ($data) @if (!$data->foto_pulang)  @endif
                @endif
                class="flex  overflow-hidden  @if ($data) min-h-20 -mt-10 rounded-r-full @endif relative bg-white @if ($status == 'masuk') border-2 shadow-xl border-slate-600 @endif rounded-lg  me-2 dark:bg-gray-800">
                <div
                    class="flex items-center justify-center w-12 @if ($masuk) @if ($data->telat) bg-yellow-500 @else bg-blue-500 @endif
@else
bg-red-500 @endif">
                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z" />
                    </svg>
                </div>

                <div class="z-10 px-4 py-2 -mx-3">
                    <div class="mx-3">
                        <span
                            class="font-semibold   @if ($masuk) @if ($data->telat) text-yellow-500 @else text-blue-500 @endif
@else
text-red-500 @endif">Masuk
                            {{ $shiftTime[$selectedShift][0] }}</span>
                        <p class="text-sm text-gray-600 dark:text-gray-200">
                            {{ $masuk ? $masuk->translatedFormat('D d F Y H:i') : 'Belum Tap' }}
                        </p>
                        @if ($data)
                            <span class="text-xs italic text-yellow-500">
                                {{ $data->telat ? 'Telat ' . $data->telat : '' }}
                        @endif
                    </div>
                </div>
                @if ($data)
                    <img class="absolute top-0 right-0 z-0 object-cover w-20 h-20 border-gray-600 rounded-full"
                        alt="User avatar" src="/storage/{{ $data->foto_masuk }}">
                @endif
            </div>
            <div 
                class="flex  overflow-hidden relative @if ($data) @if ($data->foto_pulang) min-h-20  rounded-r-full @endif @endif bg-white rounded-lg me-2 dark:bg-gray-800 @if ($status == 'pulang') border-2 shadow-xl border-slate-600 @endif">
                <div
                    class="flex items-center justify-center w-12 @if ($pulang) @if ($data->pulang_awal) bg-yellow-500 @else bg-blue-500 @endif
@else
bg-red-500 @endif">
                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z" />
                    </svg>
                </div>

                <div class="px-4 py-2 -mx-3">
                    <div class="mx-3">
                        <span
                            class="font-semibold   @if ($pulang) @if ($data->pulang_awal) text-yellow-500 @else text-blue-500 @endif
@else
text-red-500 @endif">Pulang
                            {{ $shiftTime[$selectedShift][1] }}</span>
                        <p class="text-sm text-gray-600 dark:text-gray-200">
                            {{ $pulang ? $pulang->translatedFormat('D d F Y H:i') : 'Belum Tap' }}
                        </p>
                        @if ($data)
                            <span class="text-xs italic text-yellow-500">
                                {{ $data->pulang_awal ? 'Pulang Awal ' . $data->pulang_awal : '' }}</span>
                        @endif
                    </div>
                </div>
                @if ($data)
                    @if ($data->foto_pulang)

                        <img class="absolute top-0 right-0 z-0 object-cover w-20 h-20 border-gray-600 rounded-full"
                            alt="User avatar" src="/storage/{{ $data->foto_pulang }}">
                    @endif
                @endif
            </div>

            @endif
        </div>

        <div id="camera"   style="transform: scaleX(-1);" wire:ignore class="relative w-full border border-gray-300 rounded-sm">

        </div>
        <div class="grid w-full grid-cols-3 mt-10 -bottom-100">


            <!-- Jam -->
            <div class="flex items-center w-full h-auto border border-black rounded-md ">
                <div id="liveClock" class="text-3xl font-bold text-black rounded-lg ps-2 bg-opacity-70">
                </div>
            </div>
            <div class="flex justify-center w-full">

                <!-- Tombol Kamera -->
                <button @if ($selectedShift) onclick="takeSnapshot()" @endif 
                    class="relative z-20 flex flex-col items-center justify-center w-24 text-white bg-blue-500 border-2 border-blue-900 rounded-full @if ($selectedShift) animate-pulse @endif   aspect-square">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3l2-3h8l2 3h3a2 2 0 0 1 2 2z" />
                        <circle cx="12" cy="13" r="4" />
                    </svg>
                </button>
            </div>

            <div
                class="z-20 @if (!$selectedShift) animate-pulse @endif flex flex-col items-center justify-center w-full p-1 text-5xl text-white bg-blue-400 border border-blue-500 rounded-xl">
                @if (!$masuk)
                    <select wire:model.live="selectedShift"
                        class="px-2 py-1 text-base text-black bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option hidden  value="">Pilih Shift</option>
                        @foreach ($shiftOptions as $key => $label)
                            @if (($key == 'pagi-cs' || $key == 'malam-cs') && auth()->user()->jabatan == 'cs'  )
                                <option value="{{ $key }}">{{ $label }} {{ $shiftIcon[$key] }}</option>                                
                            @elseif (($key == 'pagi-satpam' || $key == 'malam-satpam' || $key=='pagi-secwan' || $key =='sore-secwan') && auth()->user()->jabatan == 'satpam'  )
                                <option value="{{ $key }}">{{ $label }} {{ $shiftIcon[$key] }}</option>                                
                            @elseif(auth()->user()->jabatan != 'cs' && auth()->user()->jabatan != 'satpam'
                            && $key != 'pagi-cs' && $key != 'malam-cs' && $key != 'pagi-satpam' && $key != 'malam-satpam')
                                <option value="{{ $key }}">{{ $label }} {{ $shiftIcon[$key] }}</option>                                
                            @endif
                        @endforeach
                    </select>
                @else
                    <p class="text-xl">
                        {{ $selectedShift }}
                    </p>
                @endif
            </div>


        </div>

        <script>
            function updateClock() {
                let now = new Date();
                let hours = String(now.getHours()).padStart(2, '0');
                let minutes = String(now.getMinutes()).padStart(2, '0');
                let seconds = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds}`;
            }

            // Update jam setiap detik
            setInterval(updateClock, 1000);
            updateClock(); // Panggil langsung agar tidak kosong saat load pertama
        </script>


        @if (session()->has('message'))
            <script>
                Swal.fire({
                    title: "Berhasil!",
                    text: "{{ session('message') }}!",
                    icon: "success"
                });
            </script>
        @endif
    </div>
    <script>
        // setWebcamSize();
       </script>

</div>
@push('script-bottom')
    <script src="/assets/js/webcam.min.js"></script>
    <script>
        function setWebcamSize() {
            let cameraElement = document.querySelector('#camera');

            if (cameraElement) {
                let width = cameraElement.clientWidth;
                let height;

                // Cek apakah perangkat mobile atau komputer
                if (window.innerWidth <= 768) {
                //     // Mobile (Gunakan rasio 4:3)
                    height = width ;
                } else {
                    // Desktop (Gunakan rasio 16:9)
                    height = width ;
                }

                Webcam.set({
                    width: width,
                    height: height,
                    image_format: 'png',
                    jpeg_quality: 90
                });

                Webcam.attach('#camera');
            }
        }

        // Jalankan saat halaman selesai dimuat
        document.addEventListener("DOMContentLoaded", setWebcamSize);

        // Perbarui ukuran saat layar di-resize
        window.addEventListener('resize', setWebcamSize);
        // Function to take a snapshot
        function takeSnapshot() {
            Webcam.snap(function(data_uri) {
                @this.capture(data_uri);
            });
        }

        // Handle webcam errors
        Webcam.on('error', function(err) {
            console.error("Webcam.js Error:", err);
            Swal.fire({
                title: "Gagal!",
                text: "Gagal mengakses kamera: " + err.message,
                icon: "warning"
            });
        });
    </script>
  
@endpush
