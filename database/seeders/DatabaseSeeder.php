<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password')
            ]
        );

        $this->call([
            AccountSeeder::class,
        ]);

        $soals = [
            ['kategori' => 'umum', 'pertanyaan' => 'Hasil dari operasi 5 + 3 x 2 adalah...', 'jawaban_a' => '16', 'jawaban_b' => '11', 'jawaban_c' => '10', 'jawaban_d' => '15', 'jawaban_e' => '13', 'kunci_jawaban' => 'B'],
            ['kategori' => 'umum', 'pertanyaan' => 'Jika nilai persamaan x + 5 = 12, maka nilai x adalah...', 'jawaban_a' => '5', 'jawaban_b' => '6', 'jawaban_c' => '7', 'jawaban_d' => '8', 'jawaban_e' => '17', 'kunci_jawaban' => 'C'],
            ['kategori' => 'umum', 'pertanyaan' => 'Luas persegi panjang yang memiliki panjang 8 cm dan lebar 5 cm adalah...', 'jawaban_a' => '13 cm²', 'jawaban_b' => '26 cm²', 'jawaban_c' => '40 cm²', 'jawaban_d' => '45 cm²', 'jawaban_e' => '80 cm²', 'kunci_jawaban' => 'C'],
            ['kategori' => 'umum', 'pertanyaan' => 'Berapa nilai dari 10% dari 500?', 'jawaban_a' => '5', 'jawaban_b' => '10', 'jawaban_c' => '50', 'jawaban_d' => '100', 'jawaban_e' => '500', 'kunci_jawaban' => 'C'],
            ['kategori' => 'umum', 'pertanyaan' => 'Nilai dari akar kuadrat 144 adalah...', 'jawaban_a' => '10', 'jawaban_b' => '11', 'jawaban_c' => '12', 'jawaban_d' => '13', 'jawaban_e' => '14', 'kunci_jawaban' => 'C'],
            ['kategori' => 'umum', 'pertanyaan' => 'Bentuk paling sederhana dari pecahan 16/24 adalah...', 'jawaban_a' => '1/2', 'jawaban_b' => '2/3', 'jawaban_c' => '3/4', 'jawaban_d' => '4/6', 'jawaban_e' => '5/8', 'kunci_jawaban' => 'B'],
            ['kategori' => 'umum', 'pertanyaan' => 'Sebuah segitiga siku-siku memiliki sisi tegak 3 cm dan sisi alas 4 cm. Berapakah panjang sisi miringnya?', 'jawaban_a' => '5 cm', 'jawaban_b' => '6 cm', 'jawaban_c' => '7 cm', 'jawaban_d' => '8 cm', 'jawaban_e' => '10 cm', 'kunci_jawaban' => 'A'],
            ['kategori' => 'umum', 'pertanyaan' => 'Hasil perhitungan dari 2³ + 3² adalah...', 'jawaban_a' => '12', 'jawaban_b' => '15', 'jawaban_c' => '17', 'jawaban_d' => '25', 'jawaban_e' => '35', 'kunci_jawaban' => 'C'],
            ['kategori' => 'umum', 'pertanyaan' => 'Jika sebuah buku mendapat diskon 20% dan harganya menjadi Rp40.000, berapakah harga awal buku tersebut?', 'jawaban_a' => 'Rp45.000', 'jawaban_b' => 'Rp48.000', 'jawaban_c' => 'Rp50.000', 'jawaban_d' => 'Rp55.000', 'jawaban_e' => 'Rp60.000', 'kunci_jawaban' => 'C'],
            ['kategori' => 'umum', 'pertanyaan' => 'Manakah di antara bilangan berikut yang merupakan bilangan prima?', 'jawaban_a' => '9', 'jawaban_b' => '15', 'jawaban_c' => '21', 'jawaban_d' => '29', 'jawaban_e' => '33', 'kunci_jawaban' => 'D'],
            ['kategori' => 'rpl', 'pertanyaan' => 'Tipe data manakah yang digunakan untuk menyimpan nilai logika benar (true) atau salah (false)?', 'jawaban_a' => 'Integer', 'jawaban_b' => 'Float', 'jawaban_c' => 'Boolean', 'jawaban_d' => 'String', 'jawaban_e' => 'Array', 'kunci_jawaban' => 'C'],
            ['kategori' => 'rpl', 'pertanyaan' => 'Apakah fungsi utama dari atribut href pada tag <a> dalam HTML?', 'jawaban_a' => 'Mengatur warna teks', 'jawaban_b' => 'Menentukan tujuan tautan (link)', 'jawaban_c' => 'Membuat teks menjadi tebal', 'jawaban_d' => 'Menyisipkan gambar', 'jawaban_e' => 'Membuat garis bawah', 'kunci_jawaban' => 'B'],
            ['kategori' => 'rpl', 'pertanyaan' => 'Perintah SQL dasar yang digunakan untuk mengambil data dari sebuah tabel adalah...', 'jawaban_a' => 'GET', 'jawaban_b' => 'FETCH', 'jawaban_c' => 'SELECT', 'jawaban_d' => 'UPDATE', 'jawaban_e' => 'INSERT', 'kunci_jawaban' => 'C'],
            ['kategori' => 'rpl', 'pertanyaan' => 'Framework PHP manakah yang menggunakan konsep arsitektur Model-View-Controller (MVC)?', 'jawaban_a' => 'React', 'jawaban_b' => 'Vue', 'jawaban_c' => 'Laravel', 'jawaban_d' => 'Bootstrap', 'jawaban_e' => 'Tailwind', 'kunci_jawaban' => 'C'],
            ['kategori' => 'rpl', 'pertanyaan' => 'Dalam konsep OOP (Object Oriented Programming), kemampuan sebuah objek untuk memiliki banyak bentuk disebut...', 'jawaban_a' => 'Encapsulation', 'jawaban_b' => 'Inheritance', 'jawaban_c' => 'Polymorphism', 'jawaban_d' => 'Abstraction', 'jawaban_e' => 'Instantiation', 'kunci_jawaban' => 'C'],
        ];

        foreach ($soals as $s) {
            \App\Models\Soal::create($s);
        }
    }
}
