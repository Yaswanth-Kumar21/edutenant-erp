<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$views = [
    'admissions/create' => 'resources/views/admin/admissions/create.blade.php',
    'fees/dashboard'    => 'resources/views/admin/fees/dashboard.blade.php',
];

foreach ($views as $name => $path) {
    echo "=== Compiling: {$name} ===\n";
    try {
        $compiled = Illuminate\Support\Facades\Blade::compileString(
            file_get_contents($path)
        );
        // Write to temp and check PHP syntax
        $tmp = sys_get_temp_dir() . '/blade_check_' . md5($name) . '.php';
        file_put_contents($tmp, $compiled);
        exec("php -l \"{$tmp}\" 2>&1", $out, $code);
        if ($code === 0) {
            echo "  OK — no syntax errors\n";
        } else {
            echo "  ERROR:\n";
            foreach ($out as $line) echo "    {$line}\n";
            // Show the problematic compiled lines
            $lines = explode("\n", $compiled);
            preg_match('/on line (\d+)/', implode("\n", $out), $m);
            if (isset($m[1])) {
                $errLine = (int)$m[1];
                echo "  Context (compiled PHP lines " . max(1,$errLine-3) . "-" . ($errLine+3) . "):\n";
                for ($i = max(0,$errLine-4); $i <= min(count($lines)-1, $errLine+2); $i++) {
                    echo "    " . ($i+1) . ": " . $lines[$i] . "\n";
                }
            }
        }
        unlink($tmp);
    } catch (\Throwable $e) {
        echo "  EXCEPTION: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
echo "Done.\n";
