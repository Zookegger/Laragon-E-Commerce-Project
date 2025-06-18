<?php
class DefaultController {
    public function info() {
        $title = "Info";
        $description = "Info";
        include 'app/views/home/info.php';
    }

    public function index() {
        session_start();
        $title = "Home";
        $description = "Home";
        include 'app/views/home/index.php';
    }
}