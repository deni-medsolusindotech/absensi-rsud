<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Masuk;

new class extends Component {
    use WithFileUploads;

    public $image;
    
    public function capture($data)
    {
        $image = str_replace('data:image/png;base64,', '', $data);
        $image = str_replace(' ', '+', $image);
        $imageName = 'absensi_'.time().'.png';

        Storage::disk('public')->put('absensi/'.$imageName, base64_decode($image));

        // Simpan ke database
        Masuk::create([
            'image' => 'absensi/'.$imageName,
            'user_id' => auth()->user()->id,
    ]);


        session()->flash('message', 'Absensi berhasil!');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Absen Masuk') }}
        </h2>
    </x-slot>

    <div class="flex flex-col items-center space-y-4">
        <div id="camera" class="w-screen border border-gray-300 rounded-sm"></div>
        <button onclick="takeSnapshot()" class="px-4 py-2 text-white bg-blue-500 rounded">Ambil Foto</button>
        <script src="/assets/js/webcam.min.js"></script>
        <script>
            Webcam.set({
                width: 400,
                height: 400,
                image_format: 'png',
                jpeg_quality: 90
            });
            Webcam.attach('#camera');

            function takeSnapshot() {
                Webcam.snap(function(data_uri) {
                    @this.capture(data_uri);
                });
            }
        </script>

        @if (session()->has('message'))
            <p class="text-green-500">{{ session('message') }}</p>
        @endif
    </div>

</div>

