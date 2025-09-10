<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('donations', function (Blueprint $table) {
            $table->timestamp('processed_at')->nullable()->after('correlation_id');
            $table->index('processed_at');
        });
    }
    public function down(): void {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['processed_at']);
            $table->dropColumn('processed_at');
        });
    }
};


