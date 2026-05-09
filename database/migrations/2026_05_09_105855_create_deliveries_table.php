<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'preparing', 'ready', 'awaiting_courier', 'delivering', 'completed', 'cancelled'])->default('pending');
            $table->string('tracking_number')->unique();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->text('delivery_address')->nullable();
            $table->timestamp('estimation_arrival')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
