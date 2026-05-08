<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('menu_id')->index();
            $table->string('name');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price_cents')->default(0);
            $table->unsignedInteger('line_total_cents')->default(0);
            $table->json('modifiers')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['device_order_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_order_items');
    }
};
