<?php include 'app/views/shared/header.php' ?>

<head>
    <title>Add Product</title>
    <script>
        function validateForm() {
            let name = document.getElementById('name').value;
            let price = document.getElementById('price').value;
            let errors = [];

            if (name.length <= 0 || name.length > 100) {
                errors.push('Product name must be between 10 and 100 characters');
            }

            if (price <= 0 || isNaN(price)) {
                errors.push('Price must be a number > 0')
            }

            if (errors.length > 0) {
                alert(errors.join('\n'));
                return false;
            }

            return true;
        }
    </script>
</head>

<div class="container">
    <div class="card mx-auto" style="max-width: 800px;">
        <h1 class="card-header">Add new Product</h1>
        <?php if (!empty($errors)): ?>
            <ul class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" action="/webbanhang/Product/add" id="add_product" onsubmit="return validateForm();" class="card-body px-4">
            <div class="form-floating mb-3">
                <input type="text" name="name" id="name" placeholder="" required class="form-control">
                <label for="name" class="form-label text-muted">Product name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="description" id="description" placeholder="" required class="form-control">
                <label for="description" class="form-label text-muted">Product description</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="price" id="price" placeholder="" required class="form-control">
                <label for="price" class="form-label text-muted">Product price</label>
            </div>

            <!-- Category Dropdown -->
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" name="category_id" id="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>">
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="formFileLg" class="form-label">Product Image</label>
                <input class="form-control" type="file" name="image" id="image">
            </div>
        </form>
        <div class="card-footer d-flex justify-content-between py-2">
            <a href="/webbanhang/Product/index" class="btn btn-lg btn-outline-secondary">Return to list</a>
            <button type="submit" form="add_product" class="btn btn-lg btn-primary">Add Product</button>
        </div>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>