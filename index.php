<?php

require_once("vendor/autoload.php");

require_once 'MyWorker.php';
require_once 'MyWork.php';
require_once 'MyDataProvider.php';

if (extension_loaded("pthreads")) {
    echo "Using pthreads" . PHP_EOL;
} else {
    echo "Using polyfill" . PHP_EOL;
}

$threads = 8;

// Create data provider.
$provider = new MyDataProvider();

// Create pool of workers.
$pool = new Pool($threads, 'MyWorker', [$provider]);

$start = microtime(true);

$workers = $threads;
for ($i = 0; $i < $workers; $i++) {
    $pool->submit(new MyWork($i));
}

$pool->shutdown();

