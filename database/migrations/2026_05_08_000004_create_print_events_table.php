<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_order_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('target')->default('kitchen');
            $table->json('payload');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['device_order_id', 'status']);
            $table->index(['status', 'target']);
            $table->index('acknowledged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_events');
    }
};
