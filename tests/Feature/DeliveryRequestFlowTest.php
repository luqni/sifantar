<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_request_delivery_without_medicine_list_and_admin_inputs_medicines()
    {
        // 1. Setup Patient and Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin_test@sifantar.com'],
            ['name' => 'Admin Apotek', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $patient = User::firstOrCreate(
            ['email' => 'pasien_test@sifantar.com'],
            ['name' => 'Pasien Budi', 'password' => bcrypt('password'), 'role' => 'patient', 'address' => 'Jl. Tebet Raya No. 12']
        );

        // 2. Patient accesses delivery-request page
        $response = $this->actingAs($patient)->get(route('delivery-request.create'));
        $response->assertStatus(200);
        $response->assertDontSee('name="medicine_list"', false);
        $response->assertSee('Verifikasi Obat oleh Apoteker');
        $response->assertSee('Alamat Pengantaran');

        // 3. Patient submits delivery request
        $postData = [
            'address' => 'Jl. Rasuna Said No. 45, Jakarta Selatan',
            'latitude' => -6.225,
            'longitude' => 106.831,
        ];

        $postResponse = $this->actingAs($patient)->post(route('delivery-request.store'), $postData);
        $postResponse->assertRedirect(route('page', 'home'));
        $postResponse->assertSessionHas('success');

        // Assert delivery created
        $delivery = Delivery::where('patient_id', $patient->id)->latest()->first();
        $this->assertNotNull($delivery);
        $this->assertEquals('pending', $delivery->status);
        $this->assertEquals('Jl. Rasuna Said No. 45, Jakarta Selatan', $delivery->delivery_address);
        $this->assertEquals(0, $delivery->total_price);
        $this->assertCount(0, $delivery->items);

        // 4. Admin opens delivery detail page
        $adminView = $this->actingAs($admin)->get(route('admin.delivery.show', $delivery->id));
        $adminView->assertStatus(200);
        $adminView->assertSee('Detail Pesanan');
        $adminView->assertSee('Input Obat / Resep');

        // 5. Admin adds medicine 1: Paracetamol (quantity 2, price 5000)
        $addMed1 = $this->actingAs($admin)->post(route('admin.delivery.items.store', $delivery->id), [
            'medicine_name' => 'Paracetamol 500mg',
            'quantity' => 2,
            'price_at_time' => 5000,
        ]);
        $addMed1->assertRedirect();
        $addMed1->assertSessionHas('success');

        $delivery->refresh();
        $this->assertCount(1, $delivery->items);
        $this->assertEquals(10000, $delivery->total_price);

        // 6. Admin adds medicine 2: Amoxicillin (quantity 1, price 15000)
        $addMed2 = $this->actingAs($admin)->post(route('admin.delivery.items.store', $delivery->id), [
            'medicine_name' => 'Amoxicillin 500mg',
            'quantity' => 1,
            'price_at_time' => 15000,
        ]);
        $addMed2->assertRedirect();

        $delivery->refresh();
        $this->assertCount(2, $delivery->items);
        $this->assertEquals(25000, $delivery->total_price);

        // 7. Admin deletes medicine 1
        $item1 = $delivery->items()->where('medicine_name', 'Paracetamol 500mg')->first();
        $delItem = $this->actingAs($admin)->delete(route('admin.delivery.items.destroy', [$delivery->id, $item1->id]));
        $delItem->assertRedirect();
        $delItem->assertSessionHas('success');

        $delivery->refresh();
        $this->assertCount(1, $delivery->items);
        $this->assertEquals(15000, $delivery->total_price);

        // 8. Admin updates status to preparing, then ready
        $this->actingAs($admin)->post(route('admin.delivery.status', $delivery->id), ['status' => 'preparing']);
        $delivery->refresh();
        $this->assertEquals('preparing', $delivery->status);

        $this->actingAs($admin)->post(route('admin.delivery.status', $delivery->id), ['status' => 'ready']);
        $delivery->refresh();
        $this->assertEquals('ready', $delivery->status);

        // 9. Clean up test records
        $delivery->items()->delete();
        $delivery->delete();
        $patient->delete();
        if ($admin->email === 'admin_test@sifantar.com') {
            $admin->delete();
        }
    }
}
