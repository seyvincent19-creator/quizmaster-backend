<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['class_name', 'generation']);
            $table->foreignId('class_id')->nullable()->after('name')
                ->constrained('classes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_id');
            $table->string('class_name', 50)->nullable()->after('name');
            $table->string('generation', 50)->nullable()->after('class_name');
        });
    }
};
