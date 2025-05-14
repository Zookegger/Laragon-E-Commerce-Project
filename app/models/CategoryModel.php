<?php
class CategoryModel {
    private $conn;
    private $table_name = "Category";

    public function __construct($db){
        $this->conn = $db;
    }

    // Get all categories
    public function get_categories() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get category by ID
    public function get_category_by_id($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Add a new category
    public function addCategory($name, $description) {
        $query = "INSERT INTO " . $this->table_name . " (name, description)
                  VALUES (:name, :description)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        return $stmt->execute();
    }

    // Update an existing category
    public function updateCategory($id, $name, $description) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Delete a category
    public function deleteCategory($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
