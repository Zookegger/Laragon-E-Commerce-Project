<?php
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/config/Database.php';
require_once 'app/utils/JWTHandler.php';

class ProductApiController {
    private $productModel;
    private $categoryModel;
    private $jwtHandler;
    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
        $this->jwtHandler = new JWTHandler();
    }
    
    public function authenticate() {
        $header = apache_request_headers();
        if (!isset($header['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized', 'success' => false]);
            return false;
        }
        $authHeader = $header['Authorization'];
        $arr = explode(' ', $authHeader);
        $jwt = $arr[1] ?? null;
        if (!$jwt) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized', 'success' => false]);
            return false;
        }
        $payload = $this->jwtHandler->decode($jwt);
        return $payload ? true : false;
    }

    public function test_connection() {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Connection successful', 'success' => true]);
        return;
    }

    public function show($id) {
        header('Content-Type: application/json');
        try {
            $product = $this->productModel->get_product_by_id($id);
            
            if (!$product) {
                http_response_code(404);
                echo json_encode(['message' => 'Product not found', 'success' => false]);
                return;
            }
     
            echo json_encode(['product' => $product, 'success' => true]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error: '. $e->getMessage(), 'success' => false]);
            return;
        }
    }
    
    public function list() {
        header('Content-Type: application/json');
        try {
            $products = $this->productModel->get_products();

            if (!$products) {
                http_response_code(404);
                echo json_encode(['message' => 'No products found', 'success' => false]);
                return;
            }
            echo json_encode(['products' => $products, 'success' => true]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error: '. $e->getMessage(), 'success' => false]);
            return;
        }
    }
    
    public function store() {
        header('Content-Type: application/json');
        try {
            if (!$this->authenticate()) {
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized', 'success' => false]);
                return;
            }
            $data = json_decode(file_get_contents("php://input"), true);
            
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';
            $price = $data['price'] ?? '';
            $category_id = $data['category_id'] ?? null;
            $image = $data['image'] ?? null;

            if (!isset($name) || !isset($description) || !isset($price) || !isset($category_id)) 
            {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input', 'success' => false]);
                return;
            }


            $errors = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
            
            if ($errors && !is_array($errors)) {
                http_response_code(201);
                echo json_encode(['message' => 'Product added successfully', 'success' => true]);
                return;
            }

            http_response_code(400);
            echo json_encode(['errors' => $errors, 'success' => false]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error: '. $e->getMessage(), 'success' => false]);
            return;
        }
    }

    public function search($query)
    {
        header('Content-Type: application/json');
        try {
            $query = urldecode($query);

            if (preg_match('/[<>"\']/', $query) || !$query)
            {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input', 'success' => false]);
                return;
            }

            $products = $this->productModel->search_product($query);
            if ($products) {
                if (count($products) > 1) {
                    http_response_code(200);
                    echo json_encode(['products' => $products, 'success' => true]);
                    return;
                } else {
                    http_response_code(200);
                    echo json_encode(['products' => $products, 'success' => true]);
                    return;
                }
            }
            // Return 200 status because the API connection was successful
            // even though the product wasn't found
            http_response_code(200);
            echo json_encode(['query' => $query, 'message' => 'Product not found', 'success' => false]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error: '. $e->getMessage(), 'success' => false]);
            return;
        }
    }

    public function update($id) {
        header('Content-Type: application/json');
        try {
            if (!$this->authenticate()) {
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized', 'success' => false]);
                return;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';
            $price = $data['price'] ?? '';
            $category_id = $data['category_id'] ?? null;
            $image = $data['image'] ?? null;
            
            if (isset($image) && $image == null) {
                $image = $this->productModel->get_product_by_id($id)->image;
            }
            
            
            if (!isset($name) || !isset($description) || !isset($price) || !isset($category_id)) 
            {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input', 'success' => false]);
                return;
            }

            
            $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
            if ($result) {
                http_response_code(200);
                echo json_encode(['message'=> 'Product updated successfully', 'success' => true]);
                return;
            }
            
            http_response_code(404);
            echo json_encode(['message' => 'Product update failed', 'success' => false]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error: '. $e->getMessage(), 'success' => false]);
            return;
        }
    }
    
    public function delete($id) {
        header('Content-Type: application/json');
        try {
            if (!$this->authenticate()) {
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized', 'success' => false]);
                return;
            }
            $product = $this->productModel->get_product_by_id($id);
            if (!$product){
                http_response_code(404);
                echo json_encode(['message' => 'Product not found', 'success' => false]);
                return;
            }

            $result = $this->productModel->deleteProduct($id);
            if ($result) {
                http_response_code(200);
                echo json_encode(['message'=> 'Product deleted successfully', 'success' => true]);
                return;
            }
            
            http_response_code(404);
            echo json_encode(['message' => 'Product deletion failed', 'success' => false]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error: '. $e->getMessage(), 'success' => false]);
            return;
        }
    }
}