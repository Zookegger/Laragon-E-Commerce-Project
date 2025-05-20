<?php
class ProductModel {
    private $connection;
    private $table_name = "Product";

    // CONSTRUCTOR
    public function __construct($db) {
        $this->connection = $db;
    }

    // Get product by ID
    public function get_product_by_id($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get all products
    public function get_products() {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN Category c ON p.category_id = c.id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Add product
    public function addProduct($name, $description, $price, $category_id, $image) {
        $errors = [];
        if (empty($name)) {
            $errors['name'] = "Product name cannot be empty";
        }
        if (empty($description)) {
            $errors['description'] = "Product description cannot be empty";
        }
        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = "Invalid price";
        }

        if (count($errors) > 0) {
            return $errors;
        }
        
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image)
                  VALUES (:name, :description, :price, :category_id, :image)";
        $stmt = $this->connection->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":category_id", $category_id);
        
        return $stmt->execute();
    }

    // Update product
    public function updateProduct($id, $name, $description, $price, $category_id, $image) {
        $query = "UPDATE " . $this->table_name . "
                  SET name = :name, description = :description, price = :price, category_id = :category_id, image = :image 
                  WHERE id = :id";
        
        $stmt = $this->connection->prepare($query);
        
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Delete product
    public function deleteProduct($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}