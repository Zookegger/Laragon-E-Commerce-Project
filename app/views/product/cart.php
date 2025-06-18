<?php include "app/views/shared/header.php" ?>

<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="/webbanhang/public/css/cart.css">
</head>

<div class="container">
    <h1>Your Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <tr>
                            <td><img src="/webbanhang/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="product img" class="img-fluid me-4" width="30px"
                                    height="auto"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>$<?= number_format($item['price'], 2); ?></td>
                            <td class="px-2" style="max-width: 100px"><input type="number" style="max-width: 100px" name="quantity" id="quantity" min="1" data-product-id=<?=$id?>
                                    class="form-control quantity-input" value=<?= $item['quantity'] ?>></td>
                            <td class="total-cell">$<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <button class="btn btn-danger hvr-grow hvr-icon-rotate" id="btn_removeItem" data-product-id=<?=$id?>><i class="fa-solid fa-trash hvr-icon"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3 id="cart-grand-total">Total: $<?php
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        echo number_format($total, 2);
        ?></h3>

        <div class="d-flex flex-column flex-md-row align-items-stretch gap-3 mt-4" role="group" aria-label="Cart actions">
            <a href="/webbanhang/product/" class="btn btn-lg btn-outline-secondary w-100 w-md-auto hvr-icon-back">
                <i class="fa fa-arrow-left me-2 hvr-icon"></i> Continue Shopping
            </a>
            <a href="/webbanhang/product/checkout" class="btn btn-lg btn-primary w-100 w-md-auto hvr-icon-wobble-horizontal">
                <i class="fa-solid fa-shopping-cart me-2 hvr-icon"></i> Checkout
            </a>
        </div>

    <?php endif; ?>
</div>
<?php include "app/views/shared/footer.php" ?>

<script>
    $(document).ready(function () {
        $('.quantity-input').on('change', function () {
            var productId = $(this).data('product-id');
            var quantity = $(this).val();

            if (productId && quantity) {
                $.ajax({
                    url: '/webbanhang/Product/updateCart/' + productId,
                    type: 'POST',
                    data: { quantity: quantity },
                    success: function (response) {
                        var grandTotal = 0;
                        $('.total-cell').each(function () {
                            var price = parseFloat($(this).closest('tr').find('td:nth-child(2)').text().replace(/[^0-9.-]+/g, ""));
                            var quantity = parseInt($(this).closest('tr').find('.quantity-input').val());
                            var total = price * quantity;
                            grandTotal += total;
                            $(this).text('$' + total.toFixed(2));
                        });

                        $('#cart-grand-total').text('Total: $' + grandTotal.toFixed(2));

                    },
                    error: function (xhr, status, error) {
                        console.error('Error updating cart:', error);
                    }
                });
            }
        });
        $('#btn_removeItem').on('click', function () {
            var productId = $(this).data('product-id');
            if (productId) {
                $.ajax({
                    url: '/webbanhang/Product/removeFromCart/' + productId,
                    type: 'POST',
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.error('Error removing item from cart:', error);
                    }
                });
            }
        });
    });
</script>