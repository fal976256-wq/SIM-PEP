<?php

namespace Database\Seeders;

use App\Models\MasterWilayah;
use Illuminate\Database\Seeder;

class MasterWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = [
            // Majene
            'Majene' => [
                'Banggae', 'Banggae Timur', 'Pamboang', 'Sendana',
                'Tammerodo Sendana', 'Tubo Sendana', 'Ulumanda', 'Malunda',
            ],
            // Polewali Mandar
            'Polewali Mandar' => [
                'Polewali', 'Binuang', 'Anreapi', 'Campalagian', 'Luyo',
                'Limboro', 'Mapilli', 'Matakali', 'Matangnga', 'Tapango',
                'Tinambung', 'Tubbi Taramanu', 'Wonomulyo', 'Balanipa',
                'Allu', 'Bulo',
            ],
            // Mamasa
            'Mamasa' => [
                'Mamasa', 'Aralle', 'Balla', 'Bambang', 'Buntu Malangka',
                'Mambi', 'Mehalaan', 'Messawa', 'Nosu', 'Pana',
                'Rantebulahan Timur', 'Sesenapadang', 'Sumarorong', 'Tabang',
                'Tabulahan', 'Tandukkalua', 'Tawalian',
            ],
            // Mamuju
            'Mamuju' => [
                'Mamuju', 'Kalumpang', 'Bonehau', 'Kalukku', 'Papalang',
                'Sampaga', 'Simboro', 'Tapalang', 'Tapalang Barat', 'Tommo',
                'Bala Balakang',
            ],
            // Pasangkayu
            'Pasangkayu' => [
                'Pasangkayu', 'Bambaira', 'Bambalamotu', 'Baras', 'Bulutaba',
                'Dapurang', 'Duripoku', 'Lariang', 'Pedongga', 'Sarjo',
                'Sarudu', 'Tikke Raya',
            ],
            // Mamuju Tengah
            'Mamuju Tengah' => [
                'Tobadak', 'Topoyo', 'Budong-Budong', 'Pangale', 'Karossa',
            ],
        ];

        foreach ($wilayah as $kabupaten => $kecamatans) {
            foreach ($kecamatans as $kecamatan) {
                MasterWilayah::firstOrCreate([
                    'kabupaten' => $kabupaten,
                    'kecamatan' => $kecamatan,
                ]);
            }
        }
    }
}
