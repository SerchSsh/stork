<?php

/**
 * Базовый класс для работы с БД.
 * Не так что бы он сильно нужен, в рамках данного ТЗ (подключение можно было бы и в конкретной модели реализовать), но решил вот так реализовать.
 * Сюда вынес подключение к БД, и сообщение об ошибке.
 */
class Model{

    const E_DB = 'База данных не доступна. Проверьте настройки подключения и повторите попытку.';
    const E_DUPLICATE = 'Такой пользователь уже есть.';

    protected $db;
    protected $tableName;

    function __construct(array $dbConfig){
        $this->tableName = ucwords(static::class).'s';
        $this->db = new PDO($dbConfig['dbDns'].$dbConfig['dbName'], $dbConfig['dbLogin'], $dbConfig['dbPass']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}