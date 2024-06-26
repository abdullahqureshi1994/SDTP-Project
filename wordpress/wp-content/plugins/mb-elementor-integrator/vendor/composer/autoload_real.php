<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitaa8b42e4d6872704c5e2082c5b7d73c5
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

        spl_autoload_register(array('ComposerAutoloaderInitaa8b42e4d6872704c5e2082c5b7d73c5', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitaa8b42e4d6872704c5e2082c5b7d73c5', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitaa8b42e4d6872704c5e2082c5b7d73c5::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
