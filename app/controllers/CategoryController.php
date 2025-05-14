<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';

class CategoryController {
    private $db;
    private $categoryModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // List all categories
    public function index() {
        $categories = $this->categoryModel->get_categories();
        include 'app/views/category/list.php';
    }

    // Show form to add a category and handle form submission
    public function add() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];

            // Validation
            if (empty($name)) {
                $errors[] = "Category name is required.";
            }

            if (empty($errors)) {
                $this->categoryModel->addCategory($name, $description);
                header('Location: /webbanhang/Category/index');
                exit();
            }
        }

        include 'app/views/category/add.php';
    }

    // Show form to edit a category and handle form submission
    public function edit($id) {
        $category = $this->categoryModel->get_category_by_id($id);
        $errors = [];

        if (!$category) {
            die("Category not found.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];

            // Validation
            if (empty($name)) {
                $errors[] = "Category name is required.";
            }

            if (empty($errors)) {
                $this->categoryModel->updateCategory($id, $name, $description);
                header('Location: /webbanhang/Category/index');
                exit();
            }
        }

        include 'app/views/category/edit.php';
    }

    // Delete a category
    public function delete($id) {
        $category = $this->categoryModel->get_category_by_id($id);

        if (!$category) {
            die("Category not found.");
        }

        $this->categoryModel->deleteCategory($id);
        header('Location: /webbanhang/Category/index');
        exit();
    }
}
