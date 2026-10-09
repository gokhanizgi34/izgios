<?php
// Generate synthetic fixtures only; no application boot or database access.
error_reporting(E_ALL & ~E_DEPRECATED);
require __DIR__.'/../../vendor/autoload.php';
$fixtures = [];
foreach (['L', 'Q'] as $level) {
    $matrix = BaconQrCode\Encoder\Encoder::encode(
        'https://www.izgios.com/arac/12345678-1234-4234-8234-123456789abc',
        BaconQrCode\Common\ErrorCorrectionLevel::valueOf($level)
    )->getMatrix();
    $rows = [];
    for ($y = 0; $y < $matrix->getHeight(); $y++) {
        $row = '';
        for ($x = 0; $x < $matrix->getWidth(); $x++) $row .= $matrix->get($x, $y);
        $rows[] = $row;
    }
    $fixtures[$level] = $rows;
}
echo json_encode($fixtures);
