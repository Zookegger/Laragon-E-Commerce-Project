<?php include 'app/views/shared/header.php' ?>

<div class="container">
    <div class="d-flex mb-3">
        <h1>Product List</h1>
        <div class="ms-auto d-flex">
            <input class="form-control " id="input_searchQuery" type="search" name="query"
                placeholder="Search products..." aria-label="Search">
        </div>
    </div>
    <?php if (SessionHelper::isAdmin()): ?>
        <div class="alert alert-info" role="alert">
            <strong>Admin Mode:</strong> You can add, edit, or delete products.
        </div>
        <a href="/webbanhang/Product/Add" role="button" class="btn btn-primary mb-3 hvr-icon-grow fw-semibold"><i
                class="fa-solid fa-plus me-2 hvr-icon"></i>Add new Product</a>
    <?php endif; ?>

    <div class="row" id="product-list">

        <div class="d-flex justify-content-center">
            <div class="spinner-border text-primary spinner-border-lg fs-4" style="width: 3rem; height: 3rem"
                role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

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
    $(document).ready(function () {
        $.ajax({
            url: '/webbanhang/api/product/',
            type: 'GET',
            success: function (data) {
                console.log(data);
                appendList(data);
            },
            error: function (xhr, status, error) {
                alert("Error fetching product list: " + error);
            }
        });

        AOS.init({
            duration: 1000,
            easing: 'ease-in-sine',
            once: true
        });
        // debounce the search product
        $('#input_searchQuery').on('input', debounce(function () {
            var query = $(this).val();
            if (query.length > 0) {
                searchProduct(query);
            } else {
                // If the search query is empty, reload the original product list
                $.ajax({
                    url: '/webbanhang/api/product/',
                    type: 'GET',
                    success: function (data) {
                        appendList(data);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching product list:", error);
                    }
                });
            }
        }, 750));
    });

    function redirectToDetail(id) {
        window.location.href = "/webbanhang/product/show/" + id;
    }

    function searchProduct(query) {
        $.ajax({
            url: `/webbanhang/api/product/search/${encodeURI(query)}`,
            type: 'GET',
            success: function (data) {
                appendList(data);
                console.log(data.success);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching product list:", error);
            }
        });
    }

    function appendList(data) {
        console.log(data);
        if (data == null || data.success == false || data.products == null || data.products.length <= 0) {
            return;
        }
        const productList = $('#product-list');

        productList.empty();
        data.products.forEach(product => {
            const productItem = document.createElement('div');
            productItem.classList.add('col-lg-4', 'col-md-6', 'col-sm-12', 'mb-4');
            productItem.setAttribute('data-aos', 'fade-up');
            productItem.setAttribute('data-aos-anchor-placement', 'top-bottom');
            productItem.setAttribute('data-aos-delay', '100');


            const card = document.createElement('div');
            card.classList.add('card', 'hvr-float', 'w-100', 'h-100', 'd-flex', 'flex-column');
            card.setAttribute('id', `product-${product.id}`);
            card.setAttribute('onclick', `redirectToDetail(${product.id})`);

            card.innerHTML = `
                <div onclick="redirectToDetail(${product.id})" style="cursor: pointer;">
                    ${product.image ? `
                    <img
                        src="${document.location.origin + '/' + product.image}"
                        class="card-img-top"
                        style="height: 300px; object-fit: contain"
                        alt="Product Image"
                    >
                    ` : `
                    <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                        <span class="text-muted">No image</span>
                    </div>
                    `}
                </div>
                <div class="card-body" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">${product.name}</h4>
                        <p class="card-text">
                            <span class="fs-4 fw-bold text-success">
                                $${product.price}
                            </span>
                        </p>
                    </div>
                    <p class="card-text badge bg-primary text-white">
                        <i class="fa fa-tags"></i>
                        ${product.category_name}
                    </p>
                    <p class="card-text description-truncate">
                        ${product.description}
                    </p>
                </div>
                <div class="card-footer text-end justify-content-between d-flex" onclick="event.stopPropagation()">
                    <a href="/webbanhang/Product/addToCart/${product.id}" 
                        class="btn btn-success align-self-start fw-semibold hvr-icon-grow"
                    >
                        <i class="fa-solid fa-cart-plus hvr-icon me-2"></i>Add to cart
                    </a>
                    <?php if (SessionHelper::isAdmin()): ?>
                    <div class="btn-group">
                        <a href="/webbanhang/Product/edit/${product.id}" 
                            class="btn btn-secondary fw-semibold hvr-icon-rotate">
                            <i class="fa-solid fa-edit hvr-icon me-2"></i>Edit
                        </a>
                        <button 
                            class="btn btn-danger fw-semibold hvr-icon-buzz-out"
                            onclick="deleteProduct(${product.id})"
                        >
                            <i class="fa-solid fa-trash-can hvr-icon me-2"></i>Remove
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            `;

            productItem.appendChild(card);
            productList.append(productItem);

        });
    }

    function deleteProduct(id) {
        let userConfirm = confirm('Are you sure you want to delete this product?');
        if (userConfirm) {
            $.ajax({
                url: `/webbanhang/api/product/delete/${id}`,
                type: 'DELETE',
                success: function (data) {
                    window.location.href = "/webbanhang/product/index";
                },
                error: function (xhr, status, error) {
                    alert("Error deleting product: " + error);
                }
            });
        } else {
            return;
        }
    }
</script>

<?php include 'app/views/shared/footer.php' ?>