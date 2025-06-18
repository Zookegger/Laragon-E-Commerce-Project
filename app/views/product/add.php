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

        $('#add_product_btn').click(function() {
            if (!validateForm()) {
                alert('Please fill in all fields correctly');
                return;
            }

            let formData = new FormData(document.getElementById('add_product'));
            $.ajax({
                url: '/webbanhang/Product/save',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        window.location.href = '/webbanhang/Product/index';
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error adding product: ' + error);
                }
            });
        });
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
        <form method="POST" enctype="multipart/form-data" action="/webbanhang/Product/save" id="add_product" class="card-body px-4">
            <!-- Name -->
            <div class="form-group mb-3">
                <label for="name" class="form-label text-muted">Product name</label>
                <input type="text" name="name" id="name" placeholder="Enter product name" required class="form-control">
            </div>
            <!-- Price -->
            <div class="form-group mb-3">
                <label for="price" class="form-label text-muted">Product price</label>
                <input type="text" name="price" id="price" placeholder="Enter product price" required class="form-control">
            </div>

            <!-- Category Dropdown -->
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" name="category_id" id="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php /*
                     foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>">
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; */
                    ?>
                </select>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label text-muted">Product description</label>
                <textarea type="text" name="description" id="description" placeholder="Enter product description" required class="form-control" rows="3"></textarea>
            </div>

            <!-- Image -->
            <div class="form-group">
                <label for="formFileLg" class="form-label">Product Image</label>
                <input class="form-control" type="file" accept="image/*" name="image" id="image">
                <label for="image" class="form-label text-muted">Choose an image</label>
            </div>
            <script>
                $(document).ready(function() {
                    $('#image').change(function() {
                        // Create or get image preview element
                        var img = document.getElementById('preview');
                        var clearBtn = document.getElementById('clearBtn');
                        var fileName = $(this).val().split('\\').pop();

                        if (fileName !== '') {
                            $(this).next('.form-label').html('');
                        } else {
                            if (img) {
                                img.remove();
                            }

                            if (clearBtn) {
                                clearBtn.remove();
                            }
                            // Reset label text
                            $(this).next('.form-label').html('Choose an image');
                            return;
                        }

                        if (!img) {
                            // Create image preview element
                            img = document.createElement('img');
                            img.id = 'preview';
                            img.alt = 'Image preview';
                            img.src = URL.createObjectURL(this.files[0]);
                            img.onload = function() {
                                URL.revokeObjectURL(img.src);
                            }

                            // Image preview styles
                            img.style.display = 'block';
                            img.style.width = '100%';
                            img.style.height = 'auto';
                            img.style.borderRadius = '5px';
                            img.style.marginTop = '10px';
                            img.style.marginBottom = '10px';
                            img.style.objectFit = 'cover';
                            img.style.objectPosition = 'center';
                            img.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
                        }

                        if (!clearBtn) {
                            clearBtn = document.createElement('button');
                            clearBtn.id = 'clearBtn';
                            clearBtn.innerHTML = 'Clear Image';
                            clearBtn.type = 'button';
                            clearBtn.className = 'btn btn-outline-danger';

                            clearBtn.onclick = function() {
                                img.remove();
                                this.remove();
                                $('#image').val('');
                                $('#image').next('.form-label').html('Choose an image');
                            }
                        }
                        $(this).after(img);
                        $(img).after(clearBtn);
                    });
                });        
            </script>

        </form>
        <div class="card-footer d-flex justify-content-between py-2">
            <a href="/webbanhang/Product/index" class="btn btn-lg btn-outline-secondary">Return to list</a>
            <button type="submit" form="add_product" id="add_product_btn" class="btn btn-lg btn-primary">Add Product</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const jwtToken = localStorage.getItem('jwtToken');
        if (!jwtToken) {
            alert('Please login to continue');
            window.location.href = '/webbanhang/account/loginWithJwt';
            return;
        }

        $.ajax({
            url: '/webbanhang/api/category/',
            type: 'GET',
            success: function(response) {
                console.log(response);
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
</script>

<?php include 'app/views/shared/footer.php' ?>