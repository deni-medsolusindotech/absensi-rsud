<?php

use function Livewire\Volt\{state, form, rules, usesFileUploads, mount, updated};
usesFileUploads();

state([
    'name' => '',
    'email' => '',
    'jabatan' => '',
    'foto' => '',
    'nomor_hp' => '',
    'password' => '',
    'confirm_password' => '',
    'id' => '',
    'edit' => false,
]);
mount(function ($id) {
    $data = App\Models\User::findOrFail($id);
    $this->name = $data->name;
    $this->email = $data->email;
    $this->jabatan = $data->jabatan;
    $this->foto = $data->foto;
    $this->nomor_hp = $data->nomor_hp;
    $this->id = $id;
});

rules([
    'name' => 'required|min:6',
    'email' => 'required|email',
    'jabatan' => 'required',
    'foto' => 'nullable|image|max:1024',
    'nomor_hp' => 'required',
    'password' => 'min:6',
    'confirm_password' => 'same:password',
]);

updated([
    'foto' => function () {
        $this->edit = true;
    },
]);

$simpan = function () {
    $data = $this->validate();
    $user = App\Models\User::findOrFail($this->id);
    if ($this->email !== $user->email) {
        $this->validate(['email' => 'unique:users,email']);
    }
    if ($data['foto'] != $user->foto) {
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }
        $data['foto'] = $this->foto->store('photos', 'public');
    } else {
        unset($data['foto']);
    }
    if ($data['password']) {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']);
    }
    $user->update($data);
    session()->flash('success', 'user berhasil di edit');
    return $this->redirect('/users');
};

?>


<x-layouts.app>
    @volt
        <x-slot name="header">
            <div class="flex justify-between p-3 shadow-xl">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Tambah User') }}

                </h2>
                <a href="/users" wire:navigate class="p-2 text-white rounded bg-slate-500">Kembali</a>
            </div>
        </x-slot>
        <div>
            <section class="bg-white dark:bg-gray-900">
                <div class="container flex items-center justify-center min-h-screen px-6 mx-auto">
                    <form wire:submit="simpan" class="w-full max-w-lg">


                        <div class="relative flex items-center mt-8">
                            <span class="absolute">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>

                            <input autocomplete="off" type="text" wire:model="name"
                                class="block w-full py-3 text-gray-700 bg-white border rounded-lg px-11 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                                placeholder="Nama">
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

                            <h2 class="mx-3 text-gray-400">Foto</h2>

                            <input autocomplete="off" id="dropzone-file" wire:model="foto" type="file" class="hidden" />
                        </label>
                        <div class="flex justify-center w-full p-5 bg-slate-100">
                            @if ($foto && $edit)
                                <img class="object-cover border-2 rounded-full w-52 h-52" src="{{ $foto->temporaryUrl() }}"
                                    alt="">
                            @else
                                <img class="object-cover border-2 rounded-full w-52 h-52" src="/storage/{{ $foto }}"
                                    alt="">
                            @endif
                        </div>
                        @error('foto')
                            <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                        @enderror

                        <div class="relative flex items-center mt-6">
                            <span class="absolute">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>

                            <input autocomplete="off" type="email" wire:model="email"
                                class="block w-full py-3 text-gray-700 bg-white border rounded-lg px-11 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                                placeholder="Email">

                        </div>
                        @error('email')
                            <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                        @enderror

                        <div class="relative flex items-center mt-6">
                            <span class="absolute">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>

                            <input autocomplete="off" type="Jabatan" wire:model="jabatan"
                                class="block w-full py-3 text-gray-700 bg-white border rounded-lg px-11 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                                placeholder="Jabatan">
                        </div>
                        @error('jabatan')
                            <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                        @enderror
                        <div class="relative flex items-center mt-6">
                            <span class="absolute">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>

                            <input autocomplete="off" type="number" wire:model="nomor_hp"
                                class="block w-full py-3 text-gray-700 bg-white border rounded-lg px-11 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                                placeholder="Nomor Handpone">
                        </div>
                        @error('nomor_hp')
                            <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                        @enderror
                        <div class="relative flex items-center mt-4">
                            <span class="absolute">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>

                            <input autocomplete="off" type="password" wire:model="password"
                                class="block w-full px-10 py-3 text-gray-700 bg-white border rounded-lg dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                                placeholder="Password">
                        </div>
                        @error('password')
                            <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
                        @enderror

                        <div class="relative flex items-center mt-4">
                            <span class="absolute">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 mx-3 text-gray-300 dark:text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>

                            <input autocomplete="off" type="password" wire:model="confirm_password"
                                class="block w-full px-10 py-3 text-gray-700 bg-white border rounded-lg dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 dark:focus:border-blue-300 focus:ring-blue-300 focus:outline-none focus:ring focus:ring-opacity-40"
                                placeholder="Konfirmasi Password">
                        </div>
                        @error('confirm_password')
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
            </section>
        </div>
    @endvolt
</x-layouts.app>
