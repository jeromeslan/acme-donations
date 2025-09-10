<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('goal_amount', 12, 2)->default(0);
            $table->decimal('donated_amount', 12, 2)->default(0);
            $table->enum('status', ['draft','pending','active','completed','archived'])->index();
            $table->boolean('featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['category_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('campaigns'); }
};


