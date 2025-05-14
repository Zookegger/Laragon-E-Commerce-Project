<?php include 'app/views/shared/header.php' ?>


<div class="container">
    <div class="card mx-auto" style="max-width: 800px;">
        <h1 class="card-header">Edit Product Details</h1>
        <form method="POST" enctype="multipart/form-data" action="/webbanhang/Product/edit/<?php echo $product->id ?>" class="card-body" id="edit_product">
            <div class="form-floating mb-3">
                <input type="text" name="name" id="name" placeholder="" required class="form-control" value="<?php echo htmlspecialchars($product->name); ?>">
                <label for="name" class="form-label">Product name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="description" id="description" placeholder="" required class="form-control" value="<?php echo htmlspecialchars($product->description); ?>">
                <label for="description" class="form-label">Product description</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="price" id="price" placeholder="" required class="form-control" value="<?php echo htmlspecialchars($product->price); ?>">
                <label for="price" class="form-label">Product price</label>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" name="category_id" id="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>" 
                            <?php echo $product->category_id == $category->id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <input class="form-control" type="file" name="image" id="image">
                <?php if ($product->image != null):?>
                    <img src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" class="card-img" id="preview" alt="Product Image" style="display: <?php echo $product->image ? 'block' : 'none'; ?>">
                <?php endif?> 
            </div>

            <script>
                $(document).ready(function () {
                    $('#image').on('change', function(e) {
                        const input = e.target;
                        const preview = $('#preview');

                        if (input.files && input.files[0]) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                preview.attr('src', event.target.result);
                                preview.show();
                            }

                            reader.readAsDataURL(input.files[0]);
                        }
                    });
                });
            </script>
        </form>
        <div class="card-footer d-flex justify-content-between py-2">
            <a href="/webbanhang/Product/index" class="btn btn-lg btn-outline-secondary">Return to list</a>
            <button type="submit" form="edit_product" class="btn btn-lg btn-primary">Update Product</button>
        </div>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>
