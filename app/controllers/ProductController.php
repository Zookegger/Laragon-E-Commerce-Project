<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class ProductController {
    private $db;
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    // List all products
    public function index() {
        $products = $this->productModel->get_products();
        include 'app/views/product/list.php';
    }

    // Show form to add product and handle form submission
    public function add() {
        $errors = [];
        $categories = $this->categoryModel->get_categories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            
            $image = (isset($_FILES['image']) && $_FILES['image']['error'] == 0) ? $this->uploadImage($_FILES['image']) : '';

            // Validation
            if (empty($name) || strlen($name) < 3) {
                $errors['name'] = "Name must be at least 3 characters.";
            }
            if (!is_numeric($price) || $price <= 0) {
                $errors['price'] = "Price must be a positive number.";
            }

            if (empty($errors)) {
                $this->productModel->addProduct($name, $description, $price, $category_id, $image);
                header('Location: /webbanhang/Product/index');
                exit();
            }
        }

        include 'app/views/product/add.php';
    }

    // Show form to edit product and handle form submission
    public function edit($id) {
        $product = $this->productModel->get_product_by_id($id);
        $categories = $this->categoryModel->get_categories(); // pass over to view
        $errors = [];

        if (!$product) {
            die("Product not found.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            
            $image = (isset($_FILES['image']) && $_FILES['image']['error'] == 0) ? $this->uploadImage($_FILES['image']) : '';

            if (empty($name) || strlen($name) < 3) {
                $errors['name'] = "Name must be at least 3 characters.";
            }
            if (!is_numeric($price) || $price <= 0) {
                $errors['price'] = "Price must be a positive number.";
            }


            if (empty($errors)) {
                $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
                header('Location: /webbanhang/Product/index');
                exit();
            }
        }

        include 'app/views/product/edit.php';
    }

    public function uploadImage($file) {
        // Define the folder where the uploaded image will be saved
        $target_dir = "uploads/";

        // Check if the folder exists, if not, create it with permission 0777
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // true allows creation of nested folders
        }
        
        // Create the full path for the target file using the uploaded filename
        $target_file = $target_dir . basename($file["name"]);
        
        // Extract and convert the file extension to lowercase for comparison
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verify if the uploaded file is an actual image
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File is not an image.");
        }

        // Check file size > (10mb)
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Image size is too large.");
        }

        // Check file extensions
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            throw new Exception("Only support JPG, JPEG, PNG, GIF and WEBP.");
        }

        // Move the uploaded file from temporary folder to the target location
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("An Error has occurred when trying to upload image");
        }
        
        // Return the path of the uploaded image
        return $target_file;
    }

    public function addToCart($id) {
        $product = $this->productModel->get_product_by_id($id);
        if (!$product) {
            echo "Item not found.";
            return;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price'=> $product->price,
                'quantity'=> 1,
                'image' => $product->image,
            ];
        }

        header('Location: /webbanhang/Product/cart'); // Redirect to action
    }

    // Delete a product
    public function delete($id) {
        $product = $this->productModel->get_product_by_id($id);
        if (!$product) {
            die("Product not found.");
        }

        $this->productModel->deleteProduct($id);
        header('Location: /webbanhang/Product/index');
        exit();
    }
}
