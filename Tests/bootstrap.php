<?php

if (file_exists($file = __DIR__ . '/../vendor/.composer/autoload.php')) {
    $autoload = require_once $file;
} else {
    require_once $_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';

    use Symfony\Component\ClassLoader\UniversalClassLoader;

    $loader = new UniversalClassLoader();
    $loader->registerNamespace('Symfony', $_SERVER['SYMFONY']);
    $loader->register();
}



/*
if (file_exists($file = __DIR__ . '/../vendor/.composer/autoload.php')) {
    $autoload = require_once $file;
} else {
    throw new RuntimeException('Install dependencies to run test suite.');
}
*/

spl_autoload_register(function($class) {
    $class = ltrim($class, '\\');
    if (0 === strpos($class, 'RobMasters\Bundle\TaskBundle\\')) {
        $file = __DIR__.'/../'.str_replace('\\', '/', substr($class, strlen('RobMasters\Bundle\TaskBundle\\'))).'.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});