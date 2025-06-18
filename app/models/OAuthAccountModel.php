<?php

class OAuthAccountModel {
    private $conn;
    private $table_name = "oauth_accounts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function findByProviderAndProviderUserId($provider, $provider_user_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE provider = :provider AND provider_user_id = :provider_user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':provider', $provider, PDO::PARAM_STR);
            $stmt->bindParam(':provider_user_id', $provider_user_id, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }
    public function findByProviderAndAccountId($provider, $account_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE provider = :provider AND account_id = :account_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':provider', $provider, PDO::PARAM_STR);
            $stmt->bindParam(':account_id', $account_id, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }

    public function create($account_id, $provider, $id, $email, $picture = null) {
        try {

            if (empty($account_id) || empty($provider) || empty($id) || empty($email)) {
                return false; // Ensure all parameters are provided
            }
            
            $query = "INSERT INTO " . $this->table_name . " (account_id, provider, provider_user_id, email, picture) VALUES (:account_id, :provider, :provider_user_id, :email, :picture)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':account_id', $account_id);
            $stmt->bindParam(':provider', $provider);
            $stmt->bindParam(':provider_user_id', $id);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':picture', $picture);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }

    public function findByAccountId($account_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_id', $account_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}