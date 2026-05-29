<?php
/**
 * db.php — подключение к базе данных MySQL
 * Измените параметры под ваш сервер.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ваш пользователь MySQL
define('DB_PASS', '');           // ваш пароль MySQL
define('DB_NAME', 'phonebook');  // имя базы данных

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
    DB_USER,
    DB_PASS,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

/**
 * SQL для создания таблицы (выполните один раз в phpMyAdmin или раскомментируйте):
 *
 * CREATE DATABASE IF NOT EXISTS phonebook CHARACTER SET utf8 COLLATE utf8_general_ci;
 * USE phonebook;
 * CREATE TABLE IF NOT EXISTS contacts (
 *   id       INT AUTO_INCREMENT PRIMARY KEY,
 *   surname  VARCHAR(100) NOT NULL,
 *   name     VARCHAR(100) NOT NULL,
 *   lastname VARCHAR(100),
 *   gender   VARCHAR(10),
 *   date     DATE,
 *   phone    VARCHAR(30),
 *   location VARCHAR(255),
 *   email    VARCHAR(150),
 *   comment  TEXT,
 *   created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 */
