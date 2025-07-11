<?php
// weather_proxy.php

require_once __DIR__ . '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$city = $_GET['city'] ?? '';
$apiKey = $_ENV['OPENWEATHER_API_KEY'];
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?units=metric&q=" . urlencode($city) . "&appid=" . $apiKey;

$weather = file_get_contents($apiUrl);
header('Content-Type: application/json');
echo $weather;
?>