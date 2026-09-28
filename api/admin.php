<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../AuthMiddleware.php';

// 1. Authentification
$currentUser = AuthMiddleware::verifyToken();

// 2. Contrôle d'accès (MANAGER uniquement)
AuthMiddleware::requireRole($currentUser, 'MANAGER');

// 3. Réponse en cas de succès
http_response_code(200);
echo json_encode([
    "status" => "Succès",
    "message" => "Accès autorisé au back-office pour " . $currentUser['email']
]);