<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false);
            $table->json('permissions')->nullable();
        });

        // Set the first admin as Super Admin
        DB::table('admins')->where('id', 1)->update([
            'is_super_admin' => true,
            'permissions' => json_encode(['*']) // '*' means all permissions
        ]);
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['is_super_admin', 'permissions']);
        });
    }
};
