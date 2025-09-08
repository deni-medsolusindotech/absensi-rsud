{{-- <x-app-layout> --}}
<?php

use function Laravel\Folio\{middleware};
use function Livewire\Volt\{with, usesPagination, on, layout, state, updated};
use Carbon\Carbon;
layout('components.layouts.app');
on([
    'hapus' => function ($id) {
        $user = App\Models\User::findOrFail($id);
        $user->delete();
        return $this->redirect('/users', true);
    },
]);
with(
    fn() => [
        'users' => App\Models\User::paginate(10),
        'days' => range(1, Carbon::create(null, 1)->daysInMonth),
        
    ],
);

state([
        'today' => Carbon::now()->day,
        'month' => now()->month,
        'year' => now()->year,
        'types' => App\Models\UserType::get()
]);

// updated(['year' => fn () => $this->refresh()]);

$ganti = function ($userid, $day, $status) {
    $masuk = Carbon::createFromFormat('d', $day)->format('Y-m-d');
    $pulang = $status === 'Malam' ? Carbon::parse($masuk)->addDay()->format('Y-m-d') : $masuk;

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
<x-slot name="header">
    <div class="flex justify-between w-full">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Daftar Jadwal Karyawan
        </h2>

    </div>
</x-slot>

<div>
    <div class="w-full text-white bg-blue-500">
        <div class="container flex items-center justify-between px-6 py-4 mx-auto">
            <div class="flex">
                <svg viewBox="0 0 40 40" class="w-6 h-6 fill-current">
                    <path d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z">
                    </path>
                </svg>
    
                <p class="mx-3">Sedang Tahap Pengembangan .</p>
            </div>
    
            <button class="p-1 transition-colors duration-300 transform rounded-md hover:bg-opacity-25 hover:bg-gray-600 focus:outline-none">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>
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
        <div class="relative">
            <!-- Tombol Geser Kiri -->
            <button onclick="left()" class="absolute left-0 z-10 p-2 bg-gray-200 rounded-full shadow-md -top-2">
                ◀
            </button>
           
            <div class="absolute flex w-1/3 -top-2 left-1/3">
                {{-- <input type="text" name="" value=" {{ $year }}" id=""> --}}
                <select class="w-1/2 text-xs rounded-xl" wire:model.live="year" id="year">
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                </select>
                <select class="w-1/2 text-xs rounded-xl" wire:model.live="month" id="month">
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
            <!-- Tombol Geser Kanan -->
            <button onclick="right()" class="absolute right-0 z-10 p-2 bg-gray-200 rounded-full shadow-md -top-2">
                ▶
            </button>
            <div class="p-10 overflow-x-auto scroll-container">
                <div class="flex gap-2 whitespace-nowrap">
                    <div class="p-3 min-w-[100px] max-w-[100px] text-center font-semibold rounded-md"></div>
                    @foreach ($days as $day)
                        @php
                            Carbon::setLocale('id');
                            $tanggal = \Carbon\Carbon::create($year, $month, $day);
                        @endphp
                        <div
                            class="p-3 min-w-[100px] max-w-[100px] text-center font-semibold border rounded-md 
                        {{ $day == $today ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                            {{ str_pad($day, 2, '0', STR_PAD_LEFT) }} <br>
                            <span class="text-[8px]"> {{ $tanggal->translatedFormat('l, d M Y') }}</span>
                        </div>
                    @endforeach
                </div>

                @foreach ($users as $user)
                    <div class="flex gap-2 mt-5 whitespace-nowrap">
                        <div
                            class="p-3 min-w-[100px] max-w-[100px] text-center text-xs font-semibold border rounded-md">
                            {{ $user->name }}
                        </div>
                        @foreach ($days as $day)
                            @php
                                $tanggal = \Carbon\Carbon::create($year, $month, $day);
                                $hari = $tanggal->dayOfWeek; 
                                $formattedTanggal = $tanggal->format('Y-m-d'); 

                                $shift = $user->jadwals->where('tanggal_masuk', $formattedTanggal)->first();

                                // Jika tidak ada shift, buat jadwal default berdasarkan hari kalender asli
                                if (!$shift) {
                                    $statusDefault = $hari >= 1 && $hari <= 5 ? 'pagi' : 'libur'; // Senin-Jumat Pagi, Sabtu-Minggu Libur

                                    $shift = App\Models\Schedule::create([
                                        'user_id' => $user->id,
                                        'tanggal_masuk' => $formattedTanggal,
                                        'tanggal_pulang' => $formattedTanggal,
                                        'status' => $statusDefault,
                                    ]);
                                }

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
                                $shiftIcon = $shiftIcons[$shift->status] ?? '';
                            @endphp

                            <div x-data="{ open: false, shift: { label: '{{ $shift->status }}', icon: '{{ $shiftIcon }}' } }" class="relative">

                                <!-- Kotak Shift -->
                                <div @click="open = !open"
                                    class="px-3 pt-5 min-w-[100px] max-w-[100px] text-center font-semibold border rounded-md 
                                {{ $tanggal->isToday() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} cursor-pointer">
                                    <small class="absolute top-0 left-0 text-[8px] text-start px-1">
                                        {{ $user->name }}
                                    </small>
                                    <span x-text="shift.icon + ' ' + shift.label"></span>
                                </div>

                                <!-- Dropdown Pilihan Shift -->
                                <div x-show="open" @click.away="open = false"
                                    class="absolute left-0 mt-2 w-[100px] z-50 bg-white border rounded shadow-lg">
                                    @foreach ($types as $type)                                        
                                        <button wire:click="ganti({{ $user->id }}, '{{ $formattedTanggal }}', '{{ $type->name }}')"
                                            @click="shift = { label: '{{ $type->name }}', icon: '@if($type->masuk == '07:30:00') 🌅 @elseif($type->masuk == '14:00:00') 🌞 @elseif($type->masuk == '00:00:00') ❌ @elseif($type->masuk == '20:00:00') 🌙 @endif'}, open = false"
                                            class="flex items-center w-full gap-2 px-3 py-2 text-sm hover:bg-gray-100">
                                            @if($type->masuk == '07:30:00') 🌅 @elseif($type->masuk == '14:00:00') 🌞 @elseif($type->masuk == '00:00:00') ❌ @elseif($type->masuk == '20:00:00') 🌙 @endif  {{ $type->name }}  
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
                

            </div>



        </div>
    </div>

   


</div>