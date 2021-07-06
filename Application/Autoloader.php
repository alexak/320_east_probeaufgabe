<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */


namespace Application;


class Autoloader
{
    /**
     * Enregistre notre autoloader
     */
    static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * @param $class string
     */
    static function autoload($class){
        if (strpos($class, __NAMESPACE__ . '\\') === 0){
            $class = str_replace(__NAMESPACE__ . '\\', '', $class);
            $class = str_replace('\\', '/', $class);
            require $class . '.php';
        }
    }
}