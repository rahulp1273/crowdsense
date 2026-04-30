<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Running migrations...\n";
Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();

if (Schema::hasColumn('admins', 'is_super_admin')) {
    echo "SUCCESS: is_super_admin column exists.\n";
} else {
    echo "FAILURE: is_super_admin column NOT found.\n";
}
