<?php
session_start();

const DB_HOST = 'localhost';
const DB_NAME = 'mon-in';
const DB_USER = 'root';
const DB_PASS = '';

try {
    $db = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
} catch (PDOException $e) {
    die('Error! Verbinding mislukt.' . $e->getMessage());
}