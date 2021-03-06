<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit80d3105c737ae3da0dc631e9b3a92cb2
{
    public static $files = array (
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Predis\\' => 7,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $classMap = array (
        'Smarty' => __DIR__ . '/..' . '/smarty/smarty/libs/Smarty.class.php',
        'SmartyBC' => __DIR__ . '/..' . '/smarty/smarty/libs/SmartyBC.class.php',
        'SmartyCompilerException' => __DIR__ . '/..' . '/smarty/smarty/libs/Smarty.class.php',
        'SmartyException' => __DIR__ . '/..' . '/smarty/smarty/libs/Smarty.class.php',
        'Smarty_Security' => __DIR__ . '/..' . '/smarty/smarty/libs/sysplugins/smarty_security.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit80d3105c737ae3da0dc631e9b3a92cb2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit80d3105c737ae3da0dc631e9b3a92cb2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit80d3105c737ae3da0dc631e9b3a92cb2::$classMap;

        }, null, ClassLoader::class);
    }
}
