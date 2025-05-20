<?php include 'app/views/shared/header.php' ?>

<div class="container">
    <h1>Category List</h1>
    <a href="/webbanhang/category/add" role="button" class="btn btn-primary mb-3">Add new Category</a>

    <div class="table-responsive">
        <table class="table table-striped table-hover" border="1">
            <?php if (!empty($categories)): ?>
                <thead class="table-dark table-group-divider">
                    <tr>
                        <th scope="col">Category Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($categories as $category): ?>
                        <!-- Optional image placeholder -->
                        <!-- <img src="path_to_image.jpg" class="card-img-top" alt="Category Image"> -->
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td>

                                <div class="btn-group gap-1 text-end">
                                    <a href="/webbanhang/Category/edit/<?php echo $category->id; ?>" class="btn btn-secondary fw-semibold">Edit</a>
                                    <a href="/webbanhang/Category/delete/<?php echo $category->id; ?>" class="btn btn-danger fw-semibold"
                                    onclick="return confirm('Are you sure you want to delete this category?');">Remove</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            <?php else: ?>
                <p class="text-muted">No categories found.</p>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>