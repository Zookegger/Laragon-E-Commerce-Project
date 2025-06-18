<?php 

include_once 'app/models/AccountModel.php';

class UserController {
    private $accountModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->accountModel = new AccountModel($db);
    }
    
    public function profile() {
        if (SessionHelper::isLoggedIn()) {
            $account = $this->accountModel->getAccountByUsername(SessionHelper::getUsername());
            include_once 'app/views/user/profile.php';
        } else {
            header('Location: /webbanhang/user/login');
        }
    }
    
}