<?php

$f = fopen('api.log', 'a');
fwrite($f, '[' . date('Y-m-d H:i:s') . '] ' . $_SERVER['QUERY_STRING'] . "\n");
fclose($f);

spl_autoload_register(function($class) {
    $file = join('/', explode('\\', $class)) . '.php';
    if(file_exists($file)) {
        require $file;
    }
});

\Demon\Core\DB::$config['connection'] = 'mysql:dbname=demon;host=127.0.0.1';
\Demon\Core\DB::$config['username'] = 'root';
\Demon\Core\DB::$config['password'] = '';

$service = $_GET['service'];
$method = $_GET['method'];
$data = json_decode($_GET['data'], true);

$ret = "";
try {
	$ret = \Demon\Service\Api::request($service, $method, $data);
} catch(\Demon\Exception\ClientException $e) {
	$ret = [
        'error' => $e->getClientMessage(),
        'success' => false,
    ];
} catch(\Exception $e) {
    $ret = [
        'error' => 'There was an error in your request',
        'success' => false,
    ];
}

$ret['service'] = $service;
$ret['method'] = $method;

echo(json_encode($ret));