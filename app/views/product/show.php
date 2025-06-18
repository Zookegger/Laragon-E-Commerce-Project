<?php include 'app/views/shared/header.php' ?>

<div class="container mt-5">
    <h1 class="mb-4">Product Details</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!$product): ?>
        <div class="alert alert-warning" role="alert">
            <strong>Warning:</strong> Product not found.
        </div>
    <?php return; endif; ?>

    <div class="d-flex justify-content-center">
        <div class="spinner-border text-primary spinner-border-lg fs-4" style="width: 3rem; height: 3rem" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="d-flex justify-content-center" id="product-image">
            </div>
        </div>

        <div class="col-md-6" id='product-info'>

        </div>
    </div>
</div>

<?php include 'app/views/shared/footer.php' ?>

<script>
    $('document').ready(function () {
        const id = <?= $product->id ?>;

        const baseUrl = document.location.origin + '/webbanhang';
        const url = baseUrl + `/api/product/show/` + id;

        $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                $('.spinner-border').hide();

                if (data.product != null) {

                    // Append to Image container
                    const productImage = $(`
                    ${data.product.image ? `
                        <img id='product_img' src=${document.location.origin + '/' + data.product.image}  class="img-fluid rounded" style="max-height: 600px" alt="${data.product.name ?? 'product image'}">
                    ` : `
                        <div class="d-flex justify-content-center align-items-center" style="height: 600px;">
                            <span class="text-muted">No image</span>
                        </div>
                    `}`)
                    $('#product-image').append(productImage);

                    // Append to info panel
                    const product_name = $(`<h2>${data.product.name}</h2>`);
                    const product_category_name = $(`<span class="badge bg-primary fs-6"><i class="fa fa-tags me-2"></i>${data.product.category_name}</span>`)
                    const product_price = $(`<h4 class="text-success fw-bold fs-3 my-3">$${data.product.price}</h4>`);
                    const product_description = $(`<p class="fs-5 my-3">${data.product.description}</p>`);
                    const add_to_cart = $(`
                        <form method="post" action="${baseUrl}/Product/addToCart/${data.product.id}">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control" style="width: 100px;">
                            </div>
                            <button type="submit" class="btn btn-lg btn-primary hvr-icon-wobble-horizontal fw-semibold">
                                <i class="fa fa-cart-plus hvr-icon me-2"></i>Add to Cart
                            </button>
                        </form>
                    `)
                    $('#product-info').append(product_name);
                    $('#product-info').append(product_category_name);
                    $('#product-info').append(product_price);
                    $('#product-info').append(product_description);
                    $('#product-info').append(add_to_cart);
                }
            },
            error: function (xhr, status, error) {
                $('.spinner-border').hide();
                let errorMsg = 
                `
                    <div class="alert alert-danger" role="alert">
                        <span>Error: ${error.toString()}</span>
                    </div>
                `
                console.log('XHR: ' + xhr);
                console.log('Status: ' + status);
                console.log('Error message: ' + error);

                $(".container").add(errorMsg);
            }
        });
    });
</script>