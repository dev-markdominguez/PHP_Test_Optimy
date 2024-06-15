<?php

namespace Bootstrap;

use Dotenv\Dotenv;

class App
{
    private static $instance;

    private function __construct()
    {
        $this->loadEnvironmentVariables();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnvironmentVariables()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }
}

?>
