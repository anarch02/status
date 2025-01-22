<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitffc7536de37c3995c4e2cf0141f02d5b
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitffc7536de37c3995c4e2cf0141f02d5b', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitffc7536de37c3995c4e2cf0141f02d5b', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitffc7536de37c3995c4e2cf0141f02d5b::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
