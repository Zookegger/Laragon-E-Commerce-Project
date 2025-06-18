<?php

class AccountModel
{
    private $conn;
    private $table_name = "account";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAccountById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAccountByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAccountByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save($username, $fullname, $email, $password, $image = null, $role = 'user')
    {
        // Validate required fields
        if (empty($username) || empty($email) || empty($fullname)) {
            throw new InvalidArgumentException("Required fields are missing");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }

        // Check for existing username or email
        if ($this->getAccountByUsername($username)) {
            throw new RuntimeException("Username already exists");
        }

        if ($this->getAccountByEmail($email)) {
            throw new RuntimeException("Email already exists");
        }

        try {
            // Prepare statement
            $query = "INSERT INTO " . $this->table_name . " 
                 (username, fullname, email, password, image, role, created_at) 
                 VALUES (:username, :fullname, :email, :password, :image, :role, NOW())";

            $stmt = $this->conn->prepare($query);

            // Sanitize and hash
            $username = htmlspecialchars(strip_tags(trim($username)));
            $fullname = htmlspecialchars(strip_tags(trim($fullname)));
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $role = htmlspecialchars(strip_tags(trim($role)));

            // Handle password - generate random one if not provided (for OAuth cases)
            $passwordHash = $password
                ? password_hash($password, PASSWORD_BCRYPT)
                : password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT);

            // Bind parameters
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':image', $image ?? null);
            $stmt->bindParam(':role', $role);

            // Execute and return ID
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }

            throw new RuntimeException("Failed to execute database query: " . $stmt->errorInfo()[2]);
        } catch (PDOException $e) {
            error_log("Database error saving account: " . $e->getMessage());
            throw new RuntimeException("Database operation failed: " . $e->getMessage());
        }
    }
}
