<?php

use function Livewire\Volt\{state, form, rules, usesFileUploads};
usesFileUploads();
state([
    'name' => '',
    'foto' => '',
    'keterangan' => '',
]);

rules([
    'name' => 'required|min:6',
    'foto' => 'required|image|max:10240',
    'keterangan' => 'required',
]);

$simpan = function () {
    $data = $this->validate();
    $data['user_id'] = auth()->user()->id;
    $data['tanggal'] = Carbon\Carbon::createFromFormat('d-m-Y', $this->tanggal);
    $data['foto'] = $this->foto->store('logbook', 'public',);
    


    App\Models\Logbook::create($data);
    session()->flash('success', 'logbook user berhasil disimpan');
    return $this->redirect('/logbook');
};
state([
    'tanggal' => ''
])->url();
?>


<x-layouts.app>
    @volt
        <x-slot name="header">
            <div class="flex justify-between p-3 shadow-xl">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Isi Logbook') }}

                </h2>
                <a href="/logbook" wire:navigate class="p-2 text-xs text-white rounded bg-slate-500">Kembali </a>
            </div>
        </x-slot>
        <div>
            <div class="flex justify-center p-3 mt-2">
                
                <form wire:submit="simpan" class="w-full max-w-lg ">
                    <div class="w-full p-2 my-5">
                        <div class="flex w-full overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800">
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


                    <div class="relative flex items-center">
                        <span class="absolute">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>

                        <input type="text" wire:model="name"
                            class="block w-full py-3 text-gray-700 bg-white border rounded-lg px-11 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                            placeholder="Nama Kegiatan">
                    </div>
                    @error('name')
                        <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                    @enderror

                    <label for="dropzone-file"
                        class="flex items-center px-3 py-3 mx-auto mt-6 text-center bg-white border-2 border-dashed rounded-lg cursor-pointer dark:border-gray-600 dark:bg-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-300 dark:text-gray-500"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>

                        <h2 class="mx-3 text-gray-400">Foto Kegiatan</h2>

                        <input id="dropzone-file" wire:model="foto" type="file" class="hidden" />
                    </label>
                    @if ($foto)
                        <img src="{{ str($foto->temporaryUrl())->replace('/livewire/preview-file/','/storage/tmp/') }}" class="object-cover border-2 w-52 h-52"
                            alt="">
                    @endif
                    @error('foto')
                        <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                    @enderror
                    <div wire:loading class="spinner">
                        <div class="w-full spinner-border text-primary" role="status">
                            <span class="w-full">Loading...</span>
                        </div>
                    </div>




                    <div class="relative flex items-center mt-6">
                        <span class="absolute top-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>

                        <textarea wire:model="keterangan"
                            class="block w-full py-3 text-gray-700 bg-white border rounded-lg min-h-32 px-11 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                            placeholder="Keterangan"></textarea>
                    </div>
                    @error('keterangan')
                        <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                    @enderror

                    <div class="mt-6">
                        <button type="submit"
                            class="w-full px-6 py-3 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-blue-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                            Simpan
                        </button>

                    </div>
                </form>
            </div>

        </div>
    @endvolt
</x-layouts.app>
