<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../AuthMiddleware.php';
use Firebase\JWT\JWT;

// Utilisateurs de test
$users = [
    [
        "id" => 1,
        "email" => "user1@geotech.fr",
        "password" => "password123",
        "role" => "MANAGER"
    ],
    [
        "id" => 2,
        "email" => "tech1@geotech.fr",
        "password" => "secure456",
        "role" => "TECHNICIEN"
    ]
];

// Récupération du corps de la requête (JSON)
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

$authenticatedUser = null;
foreach ($users as $u) {
    if ($u['email'] === $email && $u['password'] === $password) {
        $authenticatedUser = $u;
        break;
    }
}

if (!$authenticatedUser) {
    http_response_code(401);
    echo json_encode(["error" => "Identifiants incorrects."]);
    exit();
}

// Payload JWT : expire dans 15 minutes
$payload = [
    "id" => $authenticatedUser['id'],
    "email" => $authenticatedUser['email'],
    "role" => $authenticatedUser['role'],
    "iat" => time(),
    "exp" => time() + (15 * 60)
];

$jwt = JWT::encode($payload, AuthMiddleware::getSecretKey(), 'HS256');

http_response_code(200);
echo json_encode(["token" => $jwt]);