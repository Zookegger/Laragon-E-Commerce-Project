<?php
class DefaultController {
    public function index() {
        session_start();
        include 'app/views/home/index.php';
    }
}