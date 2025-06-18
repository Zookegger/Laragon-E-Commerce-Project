<?php

require_once 'app/config/Database.php';
require_once 'app/models/CategoryModel.php';

class CategoryApiController {
    private $categoryModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->categoryModel = new CategoryModel($db);
    }
    
    public function test_connection() {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Connection successful', 'success' => true]);
        return;
    }

    public function list() {
        header('Content-Type: application/json');
        try {
            $categories = $this->categoryModel->get_categories();

            if (!$categories) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'No categories found']);
                return;
            }
            echo json_encode(['success' => true, 'categories' => $categories]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
            return;
        }
    }

    public function show($id) {
        header('Content-Type: application/json');
        try {
            $category = $this->categoryModel->get_category_by_id($id);
            if (!$category) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Category not found']);
                return;
            }
            echo json_encode(['success' => true, 'category' => $category]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
            return;
        }
    }

    public function store() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';

            if (!isset($name) || !isset($description)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid input']);
                return;
            }

            $category = $this->categoryModel->get_category_by_name($name);
            if ($category) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Category already exists']);
                return;
            }
            
            $errors = $this->categoryModel->addCategory($name, $description);
            if ($errors && is_array($errors)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Category added successfully']);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
            return;
        }
    }

    public function update($id) {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';

            if (!isset($name) || !isset($description)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid input']);
                return;
            }

            $result = $this->categoryModel->updateCategory($id, $name, $description);
            if ($result) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
                return;
            }
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Category update failed']);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
            return;
        }
    }
}