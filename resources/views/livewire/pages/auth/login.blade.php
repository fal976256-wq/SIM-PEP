<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public bool $acceptDisclaimer = false;

    public function login(): void
    {
        if (!$this->acceptDisclaimer) {
            $this->addError('disclaimer', 'Anda harus menyetujui disclaimer terlebih dahulu.');
            return;
        }

        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    Lupa kata sandi?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Masuk
            </x-primary-button>
        </div>
    </form>

    <!-- Disclaimer — INSIDE component, INSIDE wire:id boundary -->
    <div class="disclaimer-breakout mt-5 p-4 bg-gray-50 border border-gray-200 rounded-lg">
        <h4 class="text-sm font-bold text-gray-700 mb-2"><i class="fas fa-shield-halved me-1 text-blue-600"></i> DISKLAIMER DAN KEBIJAKAN PERLINDUNGAN DATA</h4>
        <div class="max-h-72 overflow-y-auto text-xs text-gray-600 leading-relaxed space-y-3 pr-2">
            <p>Dengan mengakses dan menggunakan sistem ini, pengguna menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan berikut:</p>

            <div>
                <p class="font-semibold text-gray-700">1. Kepatuhan terhadap Peraturan Perundang-undangan</p>
                <p>Sistem ini dikelola dengan mengacu pada peraturan perundang-undangan Republik Indonesia yang berlaku, termasuk namun tidak terbatas pada:</p>
                <ul class="list-disc ml-4 mt-1 space-y-0.5">
                    <li>Undang-Undang Nomor 27 Tahun 2022 tentang Perlindungan Data Pribadi.</li>
                    <li>Undang-Undang Nomor 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik sebagaimana telah diubah terakhir dengan Undang-Undang Nomor 1 Tahun 2024.</li>
                    <li>Peraturan Pemerintah Nomor 71 Tahun 2019 tentang Penyelenggaraan Sistem dan Transaksi Elektronik.</li>
                    <li>Peraturan perundang-undangan lain yang berkaitan dengan penyelenggaraan pemerintahan berbasis elektronik, keamanan informasi, dan pengelolaan data.</li>
                </ul>
                <p class="mt-1">Seluruh data yang dikumpulkan, diproses, disimpan, dan digunakan dalam sistem ini dilakukan sesuai dengan ketentuan hukum yang berlaku.</p>
            </div>

            <div>
                <p class="font-semibold text-gray-700">2. Kerahasiaan dan Perlindungan Data</p>
                <p>Data yang dimasukkan ke dalam sistem merupakan informasi resmi yang wajib dijaga kerahasiaan, keakuratan, dan keamanannya. Pengguna wajib memastikan bahwa setiap data yang diinput adalah benar, sah, dan diperoleh sesuai dengan kewenangannya.</p>
                <p class="mt-1">Setiap pengguna bertanggung jawab penuh atas seluruh aktivitas yang dilakukan menggunakan akun masing-masing, termasuk menjaga kerahasiaan kredensial akun dan mencegah akses oleh pihak yang tidak berwenang.</p>
            </div>

            <div>
                <p class="font-semibold text-gray-700">3. Penggunaan Layanan Google</p>
                <p>Sistem ini dapat menggunakan layanan dari Google, seperti Google Drive, Google Maps, Google Identity, atau layanan Google lainnya, untuk mendukung operasional sistem. Penggunaan layanan tersebut tunduk pada Kebijakan Privasi dan Ketentuan Layanan Google yang berlaku.</p>
                <p class="mt-1">Data yang diproses melalui layanan Google akan mengikuti mekanisme keamanan, kebijakan privasi, dan pengelolaan data yang ditetapkan oleh Google sesuai layanan yang digunakan.</p>
            </div>

            <div>
                <p class="font-semibold text-gray-700">4. Tanggung Jawab Pengguna</p>
                <p>Pengguna dilarang:</p>
                <ul class="list-disc ml-4 mt-1 space-y-0.5">
                    <li>Memasukkan data palsu, menyesatkan, atau tidak sah.</li>
                    <li>Mengakses data tanpa hak atau melampaui kewenangan yang diberikan.</li>
                    <li>Menyalin, mengubah, menyebarluaskan, atau menggunakan data untuk kepentingan pribadi maupun pihak lain tanpa izin.</li>
                    <li>Melakukan tindakan yang dapat mengganggu keamanan, integritas, maupun ketersediaan sistem.</li>
                </ul>
                <p class="mt-1">Pelanggaran terhadap ketentuan ini dapat dikenakan sanksi administratif, perdata, maupun pidana sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p>
            </div>

            <div>
                <p class="font-semibold text-gray-700">5. Pernyataan Resmi</p>
                <p class="font-medium">Sistem ini adalah milik Pemerintah Daerah Provinsi Sulawesi Barat. Penggunaan sistem ini tunduk pada peraturan perundang-undangan yang berlaku. Data yang dimasukkan bersifat rahasia dan setiap pengguna bertanggung jawab penuh atas data yang diinput. Penyalahgunaan sistem dapat dikenakan sanksi sesuai ketentuan yang berlaku.</p>
            </div>

            <div>
                <p class="font-semibold text-gray-700">6. Persetujuan Pengguna</p>
                <p>Dengan memilih tombol <strong>"Masuk"</strong> atau menggunakan sistem ini, pengguna dianggap telah menyetujui seluruh ketentuan dalam disclaimer ini serta bersedia mematuhi seluruh peraturan yang berlaku mengenai penggunaan sistem dan perlindungan data.</p>
            </div>
        </div>

        <label for="acceptDisclaimer" class="flex items-start gap-2 mt-3 cursor-pointer">
            <input wire:model="acceptDisclaimer" id="acceptDisclaimer" type="checkbox" class="mt-0.5 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="acceptDisclaimer">
            <span class="text-sm text-gray-700 font-medium">Saya telah membaca dan menyetujui seluruh disclaimer di atas</span>
        </label>
        <x-input-error :messages="$errors->get('disclaimer')" class="mt-1" />
    </div>
</div>
