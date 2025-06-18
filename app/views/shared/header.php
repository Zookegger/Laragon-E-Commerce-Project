<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '' ?> - Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hover.css/2.3.1/css/hover-min.css"
        integrity="sha512-csw0Ma4oXCAgd/d4nTcpoEoz4nYvvnk21a8VA2h2dzhPAvjbUIK6V3si7/g/HehwdunqqW18RwCJKpD7rL67Xg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/webbanhang/public/css/shared.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>

    <script src="/webbanhang/public/js/site.js"></script>

</head>

<body class="d-flex flex-column h-100">
    <header>
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid me-2">
                <a class="navbar-brand" href="/webbanhang">MyApp</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Products
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="nav-link text-dark ms-3" href="/webbanhang/product/index">List Products</a>
                            </ul>
                            <?php if (SessionHelper::isAdmin()): ?>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="nav-link text-dark ms-3" href="/webbanhang/product/add">Add Product</a>
                                </ul>
                            <?php endif; ?>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="nav-link text-dark ms-3" href="/webbanhang/category/index">List Category</a>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex ms-auto gap-2">
                        <?php
                        $totalItems = 0;
                        if (!empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $totalItems++;
                            }
                        }
                        ?>
                        <a href="/webbanhang/product/cart" class="btn btn-primary hvr-icon-wobble-horizontal">
                            <i class="fa fa-shopping-cart hvr-icon" aria-hidden="true"></i>
                            <?php if ($totalItems > 0): ?>
                                <span class="ms-2 fw-semibold"><?= $totalItems > 0 ? $totalItems : '' ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="btn-group gap-1">
                            <?php if (SessionHelper::isLoggedIn()): ?>
                                <div>
                                    <a href="/webbanhang/user/profile" class="btn btn-outline-primary text-white">
                                        <?php if (isset($_SESSION['image'])): ?>
                                            <img src="<?= $_SESSION['image'] ?>" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
                                            <span class="ms-2"><?= $_SESSION['username'] ?></span>
                                        <?php else: ?>
                                            <i class="fa-solid fa-user me-2"></i>
                                            <span class=""><?= $_SESSION['username'] ?></span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <a href="/webbanhang/account/login" class="btn btn-primary fw-semibold"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</a>
                            <?php endif; ?>

                            <?php if (SessionHelper::isLoggedIn()): ?>
                                <a href="/webbanhang/account/logout" id="logout" class="btn btn-outline-danger"><i class="fa fa-sign-out me-2"></i>Logout</a>
                            <?php else: ?>
                                <a href="/webbanhang/account/register" class="btn btn-secondary"><i class="fa-solid fa-user-plus me-2"></i>Register</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Script -->
        <script>
            $(document).ready(function() {
                $('#logout').click(function() {
                    localStorage.removeItem('jwtToken');
                    window.location.href = '/webbanhang/account/checklogin';
                });
            });
        </script>
    </header>
    <!-- Your Page Content Goes Here -->
    <main class="container-fluid mt-4 flex-fill">