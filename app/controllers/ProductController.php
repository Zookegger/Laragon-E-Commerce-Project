<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class ProductController
{
    private $db;
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        session_start();
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    // List all products
    public function index()
    {
        $title = "Product List";
        $products = $this->productModel->get_products();
        include 'app/views/product/list.php';
    }

    // Show form to add product and handle form submission
    public function add()
    {
        $title = 'Add Product';
        $categories = $this->categoryModel->get_categories();
        include_once 'app/views/product/add.php';
    }

    public function save()
    {
        $errors = [];
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
                $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
                if (is_array($result)) {
                    $errors += $result;
                    $categories = $this->categoryModel->get_categories();
                    include 'app/views/product/add.php';
                } else {
                    header('Location: /webbanhang/Product/');
                }
            }
        }
    }

    public function show($id)
    {
        $title = 'Product Details';
        $product = $this->productModel->get_product_by_id($id);
        $categories = $this->categoryModel->get_categories();

        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo 'Product not found';
        }
    }

    // Show form to edit product and handle form submission
    public function edit($id)
    {
        $title = 'Edit Product';
        $product = $this->productModel->get_product_by_id($id);
        $categories = $this->categoryModel->get_categories(); // pass over to view
        $errors = [];

        if (!$product) {
            die("Product not found.");
        } else {
            include 'app/views/product/edit.php';
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];

            $image = (isset($_FILES['image']) && $_FILES['image']['error'] == 0) ? $this->uploadImage($_FILES['image']) : null;

            if (empty($name) || strlen($name) < 3) {
                $errors['name'] = "Name must be at least 3 characters.";
            }
            if (!is_numeric($price) || $price <= 0) {
                $errors['price'] = "Price must be a positive number.";
            }

            if (empty($errors)) {
                if (!$image) {
                    $product = $this->productModel->get_product_by_id($id);
                    // If image is updated, delete the old image
                    if ($product && $product->image && $image && $image != $product->image) {
                        unlink($product->image); // Delete old image
                    } else {
                        $image = $product->image; // Keep old image if no new image is uploaded
                    }
                } 

                $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
                if ($result) {
                    header('Location: /webbanhang/Product/');
                } else {
                    echo 'An error has occurred while updating product informations';
                }
            }
        }
    }

    // Delete a product
    public function delete($id)
    {
        $title = 'Delete Product';
        $product = $this->productModel->get_product_by_id($id);
        if (!$product) {
            die("Product not found.");
        }

        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            header('Location: /webbanhang/Product/');
        } else {
            echo 'An error has occurred while';
        }
    }

    public function uploadImage($file)
    {
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

    public function addToCart($id)
    {
        $product = $this->productModel->get_product_by_id($id);
        if (!$product) {
            echo "Item not found.";
            return;
        }

        // Create new cart if non-exist
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
            ];
        }

        header('Location: /webbanhang/Product/cart'); // Redirect to action
    }

    public function updateCart($id)
    {
        $errors = [];
        if (!isset($_SESSION['cart'][$id])) {
            $errors[] = 'Item not found in cart.';
            return;
        }

        if (isset($_POST['quantity'])) {
            $quantity = $_POST['quantity'];
            if ($quantity > 0) {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }
    }

    public function removeFromCart($id)
    {
        $product = $this->productModel->get_product_by_id($id);

        if (!$product) {
            echo 'Item not found.';
            return;
        }

        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            echo 'Your cart is empty';
            return;
        }

        if (isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['quantity'] > 1) {
                $_SESSION['cart'][$id]['quantity']--;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
    }

    public function cart()
    {
        $title = 'Shopping Cart';
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        include 'app/views/product/cart.php';
    }
    public function checkout()
    {
        $title = 'Checkout';
        include 'app/views/product/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
        }

        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            echo 'Your cart is empty';
            return;
        }

        $this->db->beginTransaction();

        try {
            $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->execute();
            $orderId = $this->db->lastInsertId();

            $cart = $_SESSION['cart'];
            if (empty($cart)) {
                throw new Exception("Cart is empty");
            }

            // Insert order items
            foreach ($cart as $productId => $item) {
                $query = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (:order_id, :product_id, :quantity)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $productId,PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $stmt->execute();
            }

            unset($_SESSION['cart']); // Clear the cart after successful checkout
            $this->db->commit();

            header('Location: /webbanhang/Product/orderConfirmation');
        } catch (Exception $e) {
            $this->db->rollBack();
            echo "An error has occurred while processing your order: " . $e->getMessage();
        }
    }

    public function orderConfirmation()
    {
        $title = 'Order Confirmation';
        include 'app/views/product/orderConfirmation.php';
    }
}