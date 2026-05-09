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
        // Users
        $patient = User::create([
            'name' => 'Budi Pasien',
            'email' => 'pasien@sifantar.com',
            'password' => bcrypt('password'),
            'role' => 'patient',
        ]);

        $courier = User::create([
            'name' => 'Ahmad Kurir',
            'email' => 'kurir@sifantar.com',
            'password' => bcrypt('password'),
            'role' => 'courier',
        ]);

        $admin = User::create([
            'name' => 'Admin Farmasi',
            'email' => 'admin@sifantar.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Medicines
        $m1 = Medicine::create([
            'name' => 'Paracetamol 500mg',
            'description' => 'Obat penurun demam dan pereda nyeri.',
            'price' => 5000,
            'stock' => 100,
        ]);

        $m2 = Medicine::create([
            'name' => 'Amoxicillin',
            'description' => 'Antibiotik untuk mengobati infeksi bakteri.',
            'price' => 15000,
            'stock' => 50,
        ]);

        $m3 = Medicine::create([
            'name' => 'Vitamin C',
            'description' => 'Suplemen untuk menjaga daya tahan tubuh.',
            'price' => 25000,
            'stock' => 200,
        ]);

        // Deliveries
        // $d1 = Delivery::create([
        //     'patient_id' => $patient->id,
        //     'status' => 'pending',
        //     'tracking_number' => 'ORD-' . date('Ymd') . '-001',
        //     'total_price' => 25000,
        //     'delivery_address' => 'Jl. Merdeka No. 123, Jakarta',
        // ]);

        // DeliveryItem::create([
        //     'delivery_id' => $d1->id,
        //     'medicine_id' => $m1->id,
        //     'quantity' => 2,
        //     'price_at_time' => 5000,
        // ]);

        // DeliveryItem::create([
        //     'delivery_id' => $d1->id,
        //     'medicine_id' => $m2->id,
        //     'quantity' => 1,
        //     'price_at_time' => 15000,
        // ]);

        // $d2 = Delivery::create([
        //     'patient_id' => $patient->id,
        //     'status' => 'ready',
        //     'tracking_number' => 'ORD-' . date('Ymd') . '-002',
        //     'total_price' => 25000,
        //     'delivery_address' => 'Jl. Merdeka No. 123, Jakarta',
        // ]);

        // DeliveryItem::create([
        //     'delivery_id' => $d2->id,
        //     'medicine_id' => $m3->id,
        //     'quantity' => 1,
        //     'price_at_time' => 25000,
        // ]);

        // Articles
        Article::create([
            'title' => 'Cara Menjaga Daya Tahan Tubuh Saat Musim Hujan',
            'slug' => Str::slug('Cara Menjaga Daya Tahan Tubuh Saat Musim Hujan'),
            'content' => 'Lorem ipsum dolor sit amet...',
            'category' => 'Kesehatan',
            'author_id' => $admin->id,
            'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=300&h=300&fit=crop'
        ]);

        Article::create([
            'title' => 'Pentingnya Membaca Label Obat Sebelum Konsumsi',
            'slug' => Str::slug('Pentingnya Membaca Label Obat Sebelum Konsumsi'),
            'content' => 'Lorem ipsum dolor sit amet...',
            'category' => 'Obat',
            'author_id' => $admin->id,
            'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=300&h=300&fit=crop'
        ]);

    }
}
