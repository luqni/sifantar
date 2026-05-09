<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Medicine;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Article;
use App\Models\Chat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users - Using updateOrCreate for deployment safety
        $patient = User::updateOrCreate(
            ['email' => 'pasien@sifantar.com'],
            [
                'name' => 'Budi Pasien',
                'password' => bcrypt('password'),
                'role' => 'patient',
            ]
        );

        $courier = User::updateOrCreate(
            ['email' => 'kurir@sifantar.com'],
            [
                'name' => 'Ahmad Kurir',
                'password' => bcrypt('password'),
                'role' => 'courier',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@sifantar.com'],
            [
                'name' => 'Admin Farmasi',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Medicines
        $medicines = [
            [
                'name' => 'Paracetamol 500mg',
                'description' => 'Obat penurun demam dan pereda nyeri.',
                'price' => 5000,
                'stock' => 100,
            ],
            [
                'name' => 'Amoxicillin',
                'description' => 'Antibiotik untuk mengobati infeksi bakteri.',
                'price' => 15000,
                'stock' => 50,
            ],
            [
                'name' => 'Vitamin C',
                'description' => 'Suplemen untuk menjaga daya tahan tubuh.',
                'price' => 25000,
                'stock' => 200,
            ],
            [
                'name' => 'OBH Combi',
                'description' => 'Obat batuk berdahak dan pilek.',
                'price' => 18500,
                'stock' => 80,
            ]
        ];

        foreach ($medicines as $med) {
            Medicine::updateOrCreate(['name' => $med['name']], $med);
        }

        // Articles
        $articles = [
            [
                'title' => 'Cara Menjaga Daya Tahan Tubuh Saat Musim Hujan',
                'category' => 'Kesehatan',
                'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=500&h=300&fit=crop'
            ],
            [
                'title' => 'Pentingnya Membaca Label Obat Sebelum Konsumsi',
                'category' => 'Obat',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=500&h=300&fit=crop'
            ],
            [
                'title' => 'Tips Memilih Vitamin C yang Tepat Untuk Anak',
                'category' => 'Keluarga',
                'image' => 'https://images.unsplash.com/photo-1550573105-05867a0da714?w=500&h=300&fit=crop'
            ]
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(
                ['title' => $art['title']],
                [
                    'slug' => Str::slug($art['title']),
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    'category' => $art['category'],
                    'author_id' => $admin->id,
                    'image' => $art['image']
                ]
            );
        }
    }
}
