<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveTrackingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_tracking_flow_and_api_across_all_roles_and_statuses()
    {
        // 1. Seed Users
        $admin = User::factory()->create(['role' => 'admin']);
        $courier = User::factory()->create([
            'role' => 'courier',
            'name' => 'Kurir Ahmad',
            'phone' => '081234567890',
            'latitude' => -6.2150,
            'longitude' => 106.8400,
        ]);
        $patient = User::factory()->create([
            'role' => 'patient',
            'name' => 'Pasien Siti',
            'address' => 'Jl. Gatot Subroto No. 100',
        ]);
        $otherPatient = User::factory()->create(['role' => 'patient']);

        // 2. Create Delivery for Patient
        $delivery = Delivery::create([
            'patient_id' => $patient->id,
            'status' => 'pending',
            'tracking_number' => 'ORD-20261127-TEST',
            'delivery_address' => 'Jl. Gatot Subroto No. 100, Jakarta Selatan',
            'latitude' => -6.2300,
            'longitude' => 106.8300,
            'total_price' => 50000,
        ]);

        // 3. Patient accesses their tracking page
        $resPatient = $this->actingAs($patient)->get(route('tracking.show', $delivery->id));
        $resPatient->assertStatus(200);
        $resPatient->assertSee('Live Tracking');
        $resPatient->assertSee($delivery->tracking_number);
        $resPatient->assertSee('Status Pengiriman');

        // 4. Other patient cannot access this delivery (404/not found)
        $resOther = $this->actingAs($otherPatient)->get(route('tracking.show', $delivery->id));
        $resOther->assertStatus(404);

        // 5. Admin can access any tracking page
        $resAdmin = $this->actingAs($admin)->get(route('tracking.show', $delivery->id));
        $resAdmin->assertStatus(200);

        // 6. Test trackingApi for pending status
        $resApiPending = $this->actingAs($patient)->get(route('tracking.api', $delivery->id));
        $resApiPending->assertStatus(200);
        $resApiPending->assertJsonStructure([
            'status',
            'eta',
            'distance',
            'courier',
            'delivery' => ['lat', 'lng', 'address'],
        ]);
        $this->assertEquals('pending', $resApiPending->json('status'));
        $this->assertNull($resApiPending->json('courier'));

        // 7. Verify Patient Home includes active delivery when awaiting_courier
        $delivery->update(['status' => 'awaiting_courier']);
        $resHome = $this->actingAs($patient)->get(route('home'));
        $resHome->assertStatus(200);
        $resHome->assertSee($delivery->tracking_number);

        // 8. Courier can view tracking when status is awaiting_courier
        $resCourierReady = $this->actingAs($courier)->get(route('tracking.show', $delivery->id));
        $resCourierReady->assertStatus(200);

        // 9. Courier accepts job
        $this->actingAs($courier)->post(route('courier.job.accept', $delivery->id));
        $delivery->refresh();
        $this->assertEquals('delivering', $delivery->status);
        $this->assertEquals($courier->id, $delivery->courier_id);

        // 10. Courier updates location via API
        $locRes = $this->actingAs($courier)->postJson(route('courier.location.update'), [
            'latitude' => -6.2200,
            'longitude' => 106.8350,
        ]);
        $locRes->assertStatus(200);
        $locRes->assertJson(['status' => 'success']);

        $courier->refresh();
        $this->assertEquals(-6.2200, $courier->latitude);
        $this->assertEquals(106.8350, $courier->longitude);

        // 11. Test trackingApi for delivering status
        $resApiDelivering = $this->actingAs($patient)->get(route('tracking.api', $delivery->id));
        $resApiDelivering->assertStatus(200);
        $this->assertEquals('delivering', $resApiDelivering->json('status'));
        $this->assertNotNull($resApiDelivering->json('eta'));
        $this->assertNotNull($resApiDelivering->json('distance'));
        $this->assertEquals('Kurir Ahmad', $resApiDelivering->json('courier.name'));
        $this->assertEquals(-6.2200, $resApiDelivering->json('courier.lat'));
        $this->assertEquals(106.8350, $resApiDelivering->json('courier.lng'));

        // 12. Courier completes job
        $completeRes = $this->actingAs($courier)->post(route('courier.job.complete', $delivery->id));
        $completeRes->assertRedirect();

        $delivery->refresh();
        $this->assertEquals('completed', $delivery->status);

        // 13. Test trackingApi for completed status
        $resApiCompleted = $this->actingAs($patient)->get(route('tracking.api', $delivery->id));
        $resApiCompleted->assertStatus(200);
        $this->assertEquals('completed', $resApiCompleted->json('status'));
    }
}
