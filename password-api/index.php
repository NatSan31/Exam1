<?php

header("Content-Type: application/json");

require_once "controllers/PasswordController.php";
require_once "controllers/QRController.php";

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ===== PASSWORD CONTROLLER =====
$passwordController = new PasswordController();

if ($method === 'GET' && strpos($uri, '/api/password') !== false) {
    $passwordController->generatePassword();
    exit;
}

if ($method === 'POST' && strpos($uri, '/api/passwords') !== false) {
    $passwordController->generateMultiplePasswords();
    exit;
}

if ($method === 'POST' && strpos($uri, '/api/password/validate') !== false) {
    $passwordController->validatePassword();
    exit;
}

// ===== QR CONTROLLER =====
if ($method === 'POST' && $uri === '/api/v1/qrs') {
    $qrController = new QRController();
    $qrController->generate();
    exit;
}

// ===== 404 =====
http_response_code(404);
echo json_encode(["error" => "Ruta no encontrada"]);