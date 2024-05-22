<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6657264f14b49ff94f69ec64a551fa59
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6657264f14b49ff94f69ec64a551fa59::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6657264f14b49ff94f69ec64a551fa59::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6657264f14b49ff94f69ec64a551fa59::$classMap;

        }, null, ClassLoader::class);
    }
}