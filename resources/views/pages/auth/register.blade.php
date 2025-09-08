<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.guest')] class extends Component {
    use WithFileUploads;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $jabatan = '';
    public  $foto ;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'jabatan' => ['required', 'string'],
            'foto' => ['required', 'image'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['foto'] = $this->foto->store('foto', 'public');

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input wire:model="name" id="name" class="block w-full mt-1" type="text" name="name" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block w-full mt-1" type="email" name="email"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="jabatan" :value="__('Jabatan / Unit')" />

            <select
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                wire:model="jabatan" id="jabatan" class="block w-full mt-1" type="jabatan" name="jabatan" required
                autocomplete="username">
                <option value="">Pilih </option>
                <option value="dokter_umum">Dokter Umum</option>
                <option value="dokter_spesialis">Dokter Spesialis</option>
                <option value="it">IT</option>
                <option value="farmasi">Farmasi</option>
                <option value="rawat_jalan">Rawat Jalan</option>
                <option value="rekam_medis">Rekam Medis</option>
                <option value="administrasi">Administrasi</option>
                <option value="gizi">Gizi</option>
                <option value="kesling">Kesling</option>
                <option value="ranap">Ranap</option>
                <option value="igd">Igd</option>
                <option value="lab">Lab</option>
                <option value="radiologi">Radiologi</option>
                <option value="cssd">Cssd</option>
                <option value="ambulance">Supir Ambulance</option>
                <option value="laundry">Laundry</option>
                <option value="satpam">Satpam</option>
                <option value="cs">Cleaning Service</option>
            </select>
            <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="jabatan" :value="__('Foto')" />
            <label for="dropzone-file"
                class="flex items-center px-3 py-3 text-center bg-white border-2 border-dashed rounded-lg cursor-pointer dark:border-gray-600 dark:bg-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-300 dark:text-gray-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>

                <h2 class="mx-3 text-gray-400">Foto</h2>

                <input id="dropzone-file" wire:model="foto" type="file" class="hidden" />
            </label>
            @if ($foto)
            <div class="flex justify-center w-full">
                <img src="{{ str($foto->temporaryUrl())->replace('/livewire/preview-file/','/storage/tmp/') }}" class="object-cover w-24 h-24 mt-2 border-2 rounded-full"
                alt="">
            </div>
            @endif
            @error('foto')
                <span class="mt-2 text-xs text-red-500 error">{{ $message }}</span>
            @enderror

        </div>
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block w-full mt-1" type="password" name="password"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block w-full mt-1"
                type="password" name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}" wire:navigate>
                {{ __('Sudah Punya Akun?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</div>
