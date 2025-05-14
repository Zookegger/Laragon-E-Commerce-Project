<?php include 'app/views/shared/header.php' ?>

<div class="container">
    <div class="card mx-auto" style="max-width: 800px;">
        <h1 class="card-header">Edit Category Details</h1>
        <form method="POST" action="/webbanhang/Category/edit/<?php echo $category->id ?>" class="card-body" id="edit_category">
            <div class="form-floating mb-3">
                <input type="text" name="name" id="name" required class="form-control" value="<?php echo htmlspecialchars($category->name); ?>">
                <label for="name" class="form-label">Category name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="description" id="description" required class="form-control" value="<?php echo htmlspecialchars($category->description); ?>">
                <label for="description" class="form-label">Category description</label>
            </div>
        </form>
        <div class="card-footer d-flex justify-content-between py-2">
            <a href="/webbanhang/Product/index" class="btn btn-lg btn-outline-secondary">Return to list</a>
            <button type="submit" form="add_product" class="btn btn-lg btn-primary">Update Category</button>
        </div>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>
