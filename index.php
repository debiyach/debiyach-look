<?php
/**
 * @author Ömer Faruk GÖL <omerfarukgol@hotmail.com>
 */

require 'vendor/autoload.php';

$d = new \Debiyach\Look\See(__DIR__ . '/resources/views', __DIR__ . '/resources/cache');
echo $d->render('omer', [
    "title" => 'Başlık',
    'omer' => "Ömer Faruk GÖL",
    "data" => [
        'Test1', 'Test2', 'Test3', 'Test4'
    ]
]);