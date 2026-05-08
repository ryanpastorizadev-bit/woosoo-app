<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_order_id')->nullable()->constrained('device_orders')->nullOnDelete();
            $table->foreignId('table_id')->index();
            $table->string('table_name')->nullable();
            $table->string('session_key')->index();
            $table->string('type')->default('initial');
            $table->string('status')->default('draft');
            $table->string('pos_order_reference')->nullable()->index();
            $table->unsignedInteger('guest_count')->default(1);
            $table->unsignedInteger('subtotal_cents')->default(0);
            $table->unsignedInteger('tax_cents')->default(0);
            $table->unsignedInteger('total_cents')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'status']);
            $table->index(['table_id', 'session_key', 'status']);
            $table->index(['parent_order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_orders');
    }
};
