<?php include 'app/views/shared/header.php' ?>

<head>
    <title>Add Category</title>
    <script>
        function validateForm() {
            let name = document.getElementById('name').value;
            let errors = [];

            if (name.length <= 0 || name.length > 100) {
                errors.push('Category name must be between 1 and 100 characters');
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
        <h1 class="card-header">Add new Category</h1>
        <?php if (!empty($errors)): ?>
            <ul class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="POST" action="/webbanhang/category/add" id="add_Category" onsubmit="return validateForm();"
            class="card-body">
            <div class="form-floating mb-3">
                <input type="text" name="name" id="name" placeholder="" required class="form-control">
                <label for="name" class="form-label">Category name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="description" id="description" placeholder="" required class="form-control">
                <label for="description" class="form-label">Category description</label>
            </div>
        </form>
        <div class="card-footer">
            <button type="submit" form="add_Category" class="btn btn-primary">Add Category</button>
            <a href="/webbanhang/category" class="btn btn-outline-secondary">Return to list</a>
        </div>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>