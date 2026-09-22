<?php

namespace Database\Seeders;

use App\Models\MasterAnggotaDewan;
use Illuminate\Database\Seeder;

class MasterAnggotaDewanSeeder extends Seeder
{
    public function run(): void
    {
        $anggota = [
            // Sulbar 1 — Mamasa
            ['dapil' => 'Sulbar 1', 'kabupaten' => 'Mamasa', 'nama' => 'Suhadi Kandoa', 'partai' => 'PKB'],
            ['dapil' => 'Sulbar 1', 'kabupaten' => 'Mamasa', 'nama' => 'Sabar Budiman, S.H., M.H.', 'partai' => 'PDI-P'],
            ['dapil' => 'Sulbar 1', 'kabupaten' => 'Mamasa', 'nama' => 'Daud Tandi Arruan', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 1', 'kabupaten' => 'Mamasa', 'nama' => 'Fredy Boy', 'partai' => 'NasDem'],
            ['dapil' => 'Sulbar 1', 'kabupaten' => 'Mamasa', 'nama' => 'Harun Lullulangi', 'partai' => 'Hanura'],
            ['dapil' => 'Sulbar 1', 'kabupaten' => 'Mamasa', 'nama' => 'Elisabeth, S.E.', 'partai' => 'PAN'],

            // Sulbar 2 — Polewali Mandar
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Rahmat Ichwan Bahtiar', 'partai' => 'Gerindra'],
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Abdul Halim', 'partai' => 'PDI-P'],
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Irfan Pahri Putra, S.I.Kom.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Masdar Mahmuddin, S.Pd., M.Si.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Ary Iftikhar Shihab', 'partai' => 'NasDem'],
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Jalaluddin', 'partai' => 'PKS'],
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'H. Ahmad Junaedi, S.IP., M.IP.', 'partai' => 'PAN'],
            ['dapil' => 'Sulbar 2', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Gusrinaldy Sani Catur Putra Husain, S.H., M.H.', 'partai' => 'Demokrat'],

            // Sulbar 3 — Polewali Mandar
            ['dapil' => 'Sulbar 3', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Dra. Jumiaty A. Mahmud', 'partai' => 'PKB'],
            ['dapil' => 'Sulbar 3', 'kabupaten' => 'Polewali Mandar', 'nama' => 'M. Irbad Kaimuddin, S.Pi.', 'partai' => 'PDI-P'],
            ['dapil' => 'Sulbar 3', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Usman Suhuriah, S.Pd., M.Si.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 3', 'kabupaten' => 'Polewali Mandar', 'nama' => 'H. Abdul Rahim, S.Ag., M.H.', 'partai' => 'NasDem'],
            ['dapil' => 'Sulbar 3', 'kabupaten' => 'Polewali Mandar', 'nama' => 'H. Fadhiliy', 'partai' => 'PAN'],
            ['dapil' => 'Sulbar 3', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Syamsul Samad, S.IP., M.Si.', 'partai' => 'Demokrat'],
            ['dapil' => 'Sulbar 3', 'kabupaten' => 'Polewali Mandar', 'nama' => 'Syarifuddin, S.H.', 'partai' => 'Gerindra'],

            // Sulbar 4 — Majene
            ['dapil' => 'Sulbar 4', 'kabupaten' => 'Majene', 'nama' => 'H. Anthoni', 'partai' => 'PKB'],
            ['dapil' => 'Sulbar 4', 'kabupaten' => 'Majene', 'nama' => 'drg. Hj. Nurwan Katta, M.ARS.', 'partai' => 'Gerindra'],
            ['dapil' => 'Sulbar 4', 'kabupaten' => 'Majene', 'nama' => 'Dr. H. Mulyadi Bintaha, M.Pd.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 4', 'kabupaten' => 'Majene', 'nama' => 'H. Haluddin, S.Pd., M.M.Pd.', 'partai' => 'PAN'],
            ['dapil' => 'Sulbar 4', 'kabupaten' => 'Majene', 'nama' => 'Andi Nurul Fathiyah', 'partai' => 'Demokrat'],

            // Sulbar 5 — Mamuju
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'Sulfakri Sultan, S.H.', 'partai' => 'Gerindra'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'Drs. H. Habsi Wahid, M.M.', 'partai' => 'PDI-P'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'H. Irwan S. P. Pababari, S.H., M.TP.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'M. Khalil Qibran, S.H.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'H. Yudiaman Firusdi, S.H.', 'partai' => 'NasDem'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'Munandar Wijaya, S.IP., M.A.P.', 'partai' => 'PAN'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'Dr. Hj. Sitti Suraidah Suhardi, M.Si.', 'partai' => 'Demokrat'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'Sukri, S.P.', 'partai' => 'Demokrat'],
            ['dapil' => 'Sulbar 5', 'kabupaten' => 'Mamuju', 'nama' => 'Firman Argo Waskito', 'partai' => 'Demokrat'],

            // Sulbar 6 — Mamuju Tengah
            ['dapil' => 'Sulbar 6', 'kabupaten' => 'Mamuju Tengah', 'nama' => 'Dr. Hj. Amalia Fitri, S.E., M.M.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 6', 'kabupaten' => 'Mamuju Tengah', 'nama' => 'H. Haeruddin, S.H.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 6', 'kabupaten' => 'Mamuju Tengah', 'nama' => 'Resky Irmayani Mappigau, S.E.', 'partai' => 'Demokrat'],
            ['dapil' => 'Sulbar 6', 'kabupaten' => 'Mamuju Tengah', 'nama' => 'Murniati', 'partai' => 'PPP'],

            // Sulbar 7 — Pasangkayu
            ['dapil' => 'Sulbar 7', 'kabupaten' => 'Pasangkayu', 'nama' => 'Arwi', 'partai' => 'Gerindra'],
            ['dapil' => 'Sulbar 7', 'kabupaten' => 'Pasangkayu', 'nama' => 'Ir. I Putu Suardana', 'partai' => 'PDI-P'],
            ['dapil' => 'Sulbar 7', 'kabupaten' => 'Pasangkayu', 'nama' => 'Saddam, S.H., M.H.', 'partai' => 'Golkar'],
            ['dapil' => 'Sulbar 7', 'kabupaten' => 'Pasangkayu', 'nama' => 'Andi Muhammad Qusyairy, A.Md.Tra.', 'partai' => 'NasDem'],
            ['dapil' => 'Sulbar 7', 'kabupaten' => 'Pasangkayu', 'nama' => 'Abd. Azis Kulla', 'partai' => 'Hanura'],
            ['dapil' => 'Sulbar 7', 'kabupaten' => 'Pasangkayu', 'nama' => 'Andi Muhammar Qadafi Abidin, S.H., M.Kn.', 'partai' => 'Demokrat'],
        ];

        foreach ($anggota as $a) {
            MasterAnggotaDewan::firstOrCreate(
                ['nama' => $a['nama']],
                $a
            );
        }
    }
}
