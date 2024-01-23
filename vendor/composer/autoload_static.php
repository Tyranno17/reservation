<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite60d49fca469e3cbbc66808dba7f04a6
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite60d49fca469e3cbbc66808dba7f04a6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite60d49fca469e3cbbc66808dba7f04a6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite60d49fca469e3cbbc66808dba7f04a6::$classMap;

        }, null, ClassLoader::class);
    }
}
