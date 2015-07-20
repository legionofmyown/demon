<?php

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

spl_autoload_register(function($class) {
    $file = join('/', explode('\\', $class)) . '.php';
    if(file_exists($file)) {
        require $file;
    }
});

\Demon\Core\DB::$config['connection'] = 'mysql:dbname=demon_test;host=127.0.0.1';
\Demon\Core\DB::$config['username'] = 'root';
\Demon\Core\DB::$config['password'] = '';

$dbh = \Demon\Core\DB::getInstance();
$dbh->beginTransaction();
$dbh->exec(file_get_contents(__DIR__ . '/../../structure.sql'));
//$dbh->exec(file_get_contents(__DIR__ . '/testdata.sql'));
$dbh->commit();
