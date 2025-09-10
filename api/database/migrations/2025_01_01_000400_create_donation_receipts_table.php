<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('donation_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->timestamp('issued_at')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('donation_receipts'); }
};


