<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('status')->index();
            $table->string('payment_reference')->nullable()->index();
            $table->uuid('correlation_id')->index();
            $table->timestamps();
            $table->index(['campaign_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('donations'); }
};


