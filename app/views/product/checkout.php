<?php include 'app/views/shared/header.php'; ?>
<div class="container">
    <form method="POST" class="card" action="/webbanhang/Product/processCheckout">
        <h1 class="card-header">Checkout</h1>
        <div class="card-body">
            <p class="card-title">Please fill in your details to proceed with the order.</p>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <div class="form-floating mb-3">
                <input type="text" name="name" id="name" placeholder="" required class="form-control">
                <label for="name" class="form-label text-muted">Full Name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="address" id="address" placeholder="" required class="form-control">
                <label for="address" class="form-label text-muted">Address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="phone" id="phone" placeholder="" required class="form-control">
                <label for="phone" class="form-label text-muted">Phone Number</label>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="/webbanhang/product/cart" class="btn btn-lg btn-outline-secondary hvr-icon-back"><i class="fa fa-arrow-left me-2 hvr-icon"></i>Return to Cart</a>
            <button class="btn btn-lg btn-success hvr-icon-back" type="submit"><i class="hvr-icon fa fa-shopping-cart me-2"></i>Proceed</button>
        </div>
    </form>
</div>

<?php include 'app/views/shared/footer.php'; ?>