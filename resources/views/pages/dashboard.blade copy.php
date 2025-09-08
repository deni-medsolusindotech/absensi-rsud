<?php

use function Livewire\Volt\{with, usesFileUploads, on, layout, state, mount};
layout('components.layouts.app');
usesFileUploads();
Carbon\Carbon::setLocale('id');
state([
    'jadwal' => auth()
        ->user()
        ->jadwals()
        ->where('tanggal_masuk', Carbon\Carbon::now()->format('Y-m-d'))
        ->first(),
    'shiftIcon' => [
        'pagi' => '🌅',
        'siang' => '🌞',
        'malam' => '🌙',
        'malam-cs' => '🌙',
        'pagi-cs' => '🌅',
        'malam-satpam' => '🌙',
        'pagi-satpam' => '🌅',
        'nonshift' => '❌',
        'libur' => '❌',
    ],
    'pesan' => '',
    'masuk' => auth()->user()->masuknow(),
    'pulang' => auth()->user()->pulangnow(),
    'shiftOptions' => [
        'nonshift' => 'Non shift',
        'pagi' => 'Pagi',
        'siang' => 'Siang',
        'malam' => 'Malam',
        'pagi-cs' => 'Pagi-cs',
        'malam-cs' => 'Malam-cs',
        'pagi-satpam' => 'Pagi-satpam',
        'malam-satpam' => 'Malam-satpam',
    ],
    'shiftTime' => [
        'nonshift' => ['07:30', '16:00'],
        'pagi' => ['07:30', '14:00'],
        'siang' => ['14:00', '20:00'],
        'malam' => ['20:00', '07:30'],
        'pagi-cs' => ['06:00', '18:00'],
        'malam-cs' => ['18:00', '06:00'],
        'pagi-satpam' => ['06:00', '18:00'],
        'malam-satpam' => ['18:00', '06:00'],
    ],
    'selectedShift' => 'nonshift',
    'status' => 'masuk'
]);
$capture = function ($data) {
    $image = str_replace('data:image/png;base64,', '', $data);
    $image = str_replace(' ', '+', $image);
    $imageName = 'absensi_' . time() . '.png';

    Storage::disk('public')->put('absensi/' . $imageName, base64_decode($image));

    if ($this->status == 'masuk') {
        App\Models\Pulang::create([
            'image' => 'absensi/' . $imageName,
            'user_id' => auth()->user()->id,
        ]);
    } else {
        App\Models\Masuk::create([
            'image' => 'absensi/' . $imageName,
            'user_id' => auth()->user()->id,
        ]);
    }

    session()->flash('message', 'Absensi berhasil!');
    return $this->redirect('/dashboard');
};
?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Absen Masuk') }}
        </h2>
    </x-slot>
    <div class="relative  w-full min-h-[calc(100vh-78px)] overflow-hidden bg-slate-200">
        <div class="p-2 my-10">
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
        <div class="flex justify-center py-2 my-2">
            <div class="flex w-1/2 overflow-hidden bg-white rounded-lg shadow-md me-2 dark:bg-gray-800">
                <div
                    class="flex items-center justify-center w-12 @if ($masuk) bg-blue-500 @else bg-yellow-500 @endif">
                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z" />
                    </svg>
                </div>

                <div class="px-4 py-2 -mx-3">
                    <div class="mx-3">
                        <span
                            class="font-semibold   @if ($masuk) text-blue-500 @else text-yellow-500 @endif">Masuk
                            {{ $shiftTime[$selectedShift][0] }}</span>
                        <p class="text-sm text-gray-600 dark:text-gray-200">
                            {{ $masuk ? $masuk->created_at->translatedFormat('D d F Y H:i') : 'Belum Tap' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex w-1/2 overflow-hidden bg-white rounded-lg shadow-md me-2 dark:bg-gray-800">
                <div
                    class="flex items-center justify-center w-12 @if ($pulang) bg-blue-500 @else bg-yellow-500 @endif">
                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z" />
                    </svg>
                </div>

                <div class="px-4 py-2 -mx-3">
                    <div class="mx-3">
                        <span
                            class="font-semibold   @if ($pulang) text-blue-500 @else text-yellow-500 @endif">Pulang {{ $shiftTime[$selectedShift][1] }}</span>
                        <p class="text-sm text-gray-600 dark:text-gray-200">
                            {{ $pulang ? $pulang->created_at->translatedFormat('D d F Y H:i') : 'Belum Tap' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
        <div id="camera" wire:ignore class="relative w-full border border-gray-300 rounded-sm">

        </div>
        <div class="grid w-full grid-cols-3 mt-10 -bottom-100">


            <!-- Jam -->
            <div class="flex items-center w-full h-auto border border-black rounded-md ">
                <div id="liveClock" class="text-3xl font-bold text-black rounded-lg ps-2 bg-opacity-70">
                </div>
            </div>
            <div class="flex justify-center w-full">

                <!-- Tombol Kamera -->
                <button onclick="takeSnapshot()"
                    class="relative z-20 flex flex-col items-center justify-center w-24 text-white bg-blue-500 border-2 border-blue-900 rounded-full animate-pulse aspect-square">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3l2-3h8l2 3h3a2 2 0 0 1 2 2z" />
                        <circle cx="12" cy="13" r="4" />
                    </svg>
                </button>
            </div>

            <div
                class="z-20 flex flex-col items-center justify-center w-full p-1 text-5xl text-white bg-blue-400 border border-blue-500 rounded-xl">
            @if (!$masuk)
                <select wire:model.live="selectedShift"
                    class="px-2 py-1 text-base text-black bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($shiftOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }} {{ $shiftIcon[$key] }}</option>
                    @endforeach
                </select>
            @else
            
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
                    // Mobile (Gunakan rasio 4:3)
                    height = width * (3 / 4);
                } else {
                    // Desktop (Gunakan rasio 16:9)
                    height = width * (9 / 16);
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
