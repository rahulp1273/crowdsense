<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_banned')) {
                $table->boolean('is_banned')->default(false);
            }
            if (!Schema::hasColumn('users', 'weight')) {
                $table->integer('weight')->default(1);
            }
        });

        Schema::table('places', function (Blueprint $table) {
            if (!Schema::hasColumn('places', 'lat')) {
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->integer('radius')->default(100);
                $table->integer('max_capacity')->nullable();
                $table->boolean('is_chat_enabled')->default(true);
            }
        });

        Schema::table('check_ins', function (Blueprint $table) {
            if (!Schema::hasColumn('check_ins', 'is_active')) {
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->integer('weight_applied')->default(1);
            }
        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_banned', 'weight']);
        });

        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'radius', 'max_capacity', 'is_chat_enabled']);
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'expires_at', 'weight_applied']);
        });
    }
};
