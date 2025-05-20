<?php include 'app/views/shared/header.php'; ?>
<h1>Checkout</h1>
<form method="POST" action="/webbanhang/Product/processCheckout">
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
    <button type="submit">Process</button>
</form>

<?php include 'app/views/shared/footer.php'; ?>