<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('places');
echo "Columns in 'places' table:\n";
print_r($columns);

if (!in_array('lat', $columns)) {
    echo "ERROR: 'lat' column missing!\n";
}
if (!in_array('lng', $columns)) {
    echo "ERROR: 'lng' column missing!\n";
}
if (!in_array('is_chat_enabled', $columns)) {
    echo "ERROR: 'is_chat_enabled' column missing!\n";
}

$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table';");
echo "\nTables in database:\n";
print_r($tables);
