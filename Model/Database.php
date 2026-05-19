<?php

class Database
{
    private const DB_HOST = '127.0.0.1';
    private const DB_NAME = 'travel_guide';
    private const DB_USER = 'root';
    private const DB_PASS = '';

    private static $connection = null;

    public static function getConnection()
    {
        if (self::$connection === null) {
            $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

            self::$connection = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}
