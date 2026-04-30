<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Drop existing unique index to allow normalization/merging
        // SQLite doesn't support dropping indices by name easily in Blueprint sometimes, 
        // but we can try. If it fails, we catch it.
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        } catch (\Exception $e) {
            // Index might not exist or driver doesn't support dropping by name
        }

        // 2. DATA CLEANUP: Normalize existing emails and merge duplicates
        $users = DB::table('users')->get();
        $grouped = $users->groupBy(fn($u) => strtolower($u->email));

        foreach ($grouped as $email => $group) {
            if ($group->count() > 1) {
                // Find the best record to keep (prefer verified)
                $keep = $group->whereNotNull('email_verified_at')->first() ?: $group->first();
                
                foreach ($group as $u) {
                    if ($u->id !== $keep->id) {
                        DB::table('users')->where('id', $u->id)->delete();
                    }
                }
                DB::table('users')->where('id', $keep->id)->update(['email' => strtolower($email)]);
            } else {
                $u = $group->first();
                if ($u) {
                    DB::table('users')->where('id', $u->id)->update(['email' => strtolower($email)]);
                }
            }
        }

        // 3. ENFORCE UNIQUE CONSTRAINT & COLLATION
        Schema::table('users', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) COLLATE utf8mb4_unicode_ci');
            }
            
            // Re-add the unique index
            $table->unique('email');
        });

        // 4. CREATE ADMINS TABLE
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE admins MODIFY email VARCHAR(255) COLLATE utf8mb4_unicode_ci');
            }
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 5. MIGRATE EXISTING ADMINS TO NEW TABLE
        $admins = DB::table('users')->where('role', 'admin')->get();
        foreach ($admins as $admin) {
            DB::table('admins')->insert([
                'name' => $admin->name,
                'email' => $admin->email,
                'email_verified_at' => $admin->email_verified_at,
                'password' => $admin->password,
                'created_at' => $admin->created_at,
                'updated_at' => $admin->updated_at,
            ]);
            DB::table('users')->where('id', $admin->id)->delete();
        }
        
        // 6. REMOVE ROLE COLUMN FROM USERS
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }
        });

        Schema::dropIfExists('admins');
    }
};
