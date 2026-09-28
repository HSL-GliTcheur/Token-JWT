<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../AuthMiddleware.php';

// Vérification du token
$currentUser = AuthMiddleware::verifyToken();

// Retourne les infos décodées de l'utilisateur
http_response_code(200);
echo json_encode([
    "id" => $currentUser['id'],
    "email" => $currentUser['email'],
    "role" => $currentUser['role']
]);