<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private static $secretKey = "HlSrmyTulVjj2L3xdLTfiKSlH326gqAq1Q8FuADPSyT";

    public static function getSecretKey()
    {
        return self::$secretKey;
    }

    public static function verifyToken()
    {
        // getallheaders() lit l'extérieur de l'enveloppe HTTP 
        // (les en-têtes reçus par le serveur lors de la requête).
        $headers = getallheaders();

        // La syntaxe ?? sert de filet de secours : "cherche dans $headers, sinon dans $_SERVER, sinon mets null".
        $authHeader = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        /* preg_match(...) : analyse le texte de l'en-tête.
        Bearer\s : cherche le mot "Bearer" suivi d'un espace (\s).
        (\S+) : capture tout ce qui suit sans espace (c'est notre JWT). */
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["error" => "Accès refusé. Jeton manquant ou format invalide."]);
            exit();
        }

        // contient uniquement la clé JWT isolée.
        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => "Jeton invalide ou expiré."]);
            exit();
        }
    }

    public static function requireRole($user, $roleAttendu)
    {
        if (!isset($user['role']) || $user['role'] !== $roleAttendu) {
            http_response_code(403);
            echo json_encode(["error" => "Accès interdit. Droits insuffisants."]);
            exit();
        }
    }
}