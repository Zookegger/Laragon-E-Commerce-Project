<?php include 'app/views/shared/header.php' ?>

<div class="container">
    <h1>Product List</h1>
    <a href="/webbanhang/Product/Add" role="button" class="btn btn-primary mb-3">Add new Product</a>

    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <!-- Optional image placeholder -->
                        <img src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                            </h5>
                            <p class="card-text">
                                <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <p class="card-text">
                                <strong>Category:</strong>
                                <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <p class="card-text">
                                <strong>Price:</strong> $
                                <?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                        <div class="card-footer text-end">
                            <a href="/webbanhang/Product/edit/<?php echo $product->id; ?>"
                                class="btn btn-warning btn-sm">Edit</a>
                            <a href="/webbanhang/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this product?');">Remove</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No products found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>