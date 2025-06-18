<?php include 'app/views/shared/header.php' ?>

<head>
    <title>Edit Product</title>
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
        <h1 class="card-header">Edit Product Details</h1>
        <form method="POST" enctype="multipart/form-data" 
            class="card-body" id="edit_product" onsubmit="return validateForm();">
            <?php if (!empty($errors)): ?>
                <ul class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="form-floating mb-3">
                <input type="text" name="name" id="name" placeholder="" required class="form-control"
                    value="<?php echo htmlspecialchars($product->name); ?>">
                <label for="name" class="form-label">Product name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="description" id="description" placeholder="" required class="form-control"
                    value="<?php echo htmlspecialchars($product->description); ?>">
                <label for="description" class="form-label">Product description</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="price" id="price" placeholder="" required class="form-control"
                    value="<?php echo htmlspecialchars($product->price); ?>">
                <label for="price" class="form-label">Product price</label>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" name="category_id" id="category_id" required>
                    <option value="">-- Select Category --</option>
                    <!-- <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>" <?php echo $product->category_id == $category->id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option>
                    <?php endforeach; ?> -->
                </select>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input class="form-control" type="file" accept="image/*" name="image" id="image" value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">
                <?php if ($product->image != null): ?>
                    <img src="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                        class="card-img" id="preview" alt="Product Image"
                        style="display: <?php echo $product->image ? 'block' : 'none'; ?>">
                <?php endif ?>
                <label for="image" class="form-label text-muted">Choose an image</label>
            </div>
            <script>
                $(document).ready(function () {
                    // Use the direct image path as stored in the database
                    // This assumes the image path is already correct and complete
                    $('#preview').attr('src', '<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>');
                    
                    $('#image').on('change', function (e) {
                        const input = e.target;
                        let img = document.getElementById('preview');
                        let clearBtn = document.getElementById('clearBtn');
                        const fileName = $(this).val().split('\\').pop();

                        // Update label
                        if (fileName !== '') {
                            $(this).next('.form-label').html('');
                        } else {
                            if (img) img.remove();
                            if (clearBtn) clearBtn.remove();
                            $(this).next('.form-label').html('Choose an image');
                            return;
                        }

                        // Create preview image if not exists
                        if (!img) {
                            img = document.createElement('img');
                            img.id = 'preview';
                            img.alt = 'Image preview';
                            img.className = 'img-fluid';

                            // Style preview image
                            Object.assign(img.style, {
                                display: 'block',
                                width: '100%',
                                height: 'auto',
                                borderRadius: '5px',
                                marginTop: '10px',
                                marginBottom: '10px',
                                objectFit: 'cover',
                                objectPosition: 'center',
                                boxShadow: '0 4px 8px rgba(0, 0, 0, 0.2)'
                            });
                        }

                        // Read file and show preview
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function (event) {
                                img.src = event.target.result;
                            }
                            reader.readAsDataURL(input.files[0]);
                        }

                        // Create clear button if not exists
                        if (!clearBtn) {
                            clearBtn = document.createElement('button');
                            clearBtn.id = 'clearBtn';
                            clearBtn.type = 'button';
                            clearBtn.innerHTML = 'Clear Image';
                            clearBtn.className = 'btn btn-outline-danger';

                            clearBtn.onclick = function () {
                                img.remove();
                                this.remove();
                                $('#image').val('');
                                $('#image').next('.form-label').html('Choose an image');
                            }
                        }

                        // Insert preview and clear button into DOM
                        $(this).after(img);
                        $(img).after(clearBtn);
                    });
                });
            </script>

        </form>
        <div class="card-footer d-flex justify-content-between py-2">
            <a href="/webbanhang/Product/index" class="btn btn-lg btn-outline-secondary">Return to list</a>
            <button type="submit" id="submit" form="edit_product" class="btn btn-lg btn-primary">Update Product</button>
        </div>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>

<script>
    $(document).ready(function() {
        // Get product
        $.ajax({
            url: '/webbanhang/api/product/<?php echo $product->id; ?>',
            type: 'GET',
            success: function(response) {
                let product = response.product;
                $('#name').val(product.name);
                $('#description').val(product.description);
                $('#price').val(product.price);
                $('#category_id').val(product.category_id);
                $('#image').val(product.image);
            },
            error: function(xhr, status, error) {
                alert('Error fetching product: ' + error);
            }
        })

        // Get categories
        $.ajax({
            url: '/webbanhang/api/category/',
            type: 'GET',
            success: function(response) {
                let categories = response.categories;
                let categorySelect = $('#category_id');
                categories.forEach(function(category) {
                    categorySelect.append('<option value="' + category.id + '">' + category.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                alert('Error fetching categories: ' + error);
            }
        });
    });

    $('#edit_product').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/webbanhang/Product/update/' + <?php echo $product->id; ?>,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    window.location.href = '/webbanhang/Product/index';
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error updating product: ' + error);
            }
        });
    });
</script>