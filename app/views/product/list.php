<?php include 'app/views/shared/header.php' ?>

<div class="container">
    <h1>Product List</h1>
    <a href="/webbanhang/Product/Add" role="button" class="btn btn-primary mb-3">Add new Product</a>

    <div class="row" id="product-list">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card hvr-float w-100" id="product-<?php echo $product->id; ?>">
                        <!-- Optional image placeholder -->
                        <div onclick="redirectToDetail()" style="cursor: pointer;">
                            <img src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                                class="card-img-top" style="height: 300px; object-fit: contain" alt="Product Image">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">
                                        <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                                    </h4>

                                    <p class="card-text">
                                        <span class="fs-4 fw-semibold text-success">
                                            $<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </p>
                                </div>
                                <p class="card-text badge bg-primary text-white">
                                    <i class="fa fa-tags"></i>
                                    <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <p class="card-text description-truncate">
                                    <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>

                        <div class="card-footer text-end justify-content-between d-flex" onclick="event.stopPropagation()">
                            <a href="/webbanhang/Product/addToCart/<?php echo $product->id; ?>"
                                class="btn btn-success align-self-start fw-semibold">Add to cart</a>
                            <div class="btn-group">
                                <a href="/webbanhang/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning">Edit</a>
                                <a href="/webbanhang/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this product?');">Remove</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No products found.</p>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Multi-line truncate for description text */
    .description-truncate {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>

<script>
    function redirectToDetail() {
        window.location.href = "/webbanhang/Product/show/" + this.id.split('-')[1];
    }
    $(document).ready(function () {
        $('#product-list').on('click', '.card', function () {
            var productId = $(this).attr('id').split('-')[1];
            window.location.href = "/webbanhang/Product/show/" + productId;
        });
    });
</script>

<?php include 'app/views/shared/footer.php' ?>