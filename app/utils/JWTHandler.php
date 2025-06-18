<?php

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {
    private $secret_key;

    public function __construct() {
        $this->secret_key = 'your-256-bit-secret-key';
    }

    // Create JWT
    public function encode($data) {
        $issueAt = time();
        $expireAt = $issueAt + 3600; // 1 Hour
        $payload = [
            'iat' => $issueAt,
            'exp' => $expireAt,
            'data' => $data
        ];
        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    // Decode JWT
    public function decode($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $e) {
            return null;
        }
    }

    // Validate JWT
    public function validate($token) {
        return $this->decode($token) !== null;
    }
    
}