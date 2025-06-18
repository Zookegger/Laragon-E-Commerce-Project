<?php include 'app/views/shared/header.php' ?>

<div class="h-100 container mt-5 mb-5">
    <div class="text-center" data-aos="fade-up">
        <h1 class="display-4">Welcome to WebBanHang</h1>
        <p class="lead">Your one-stop shop for electronics, fashion, home goods, books, and more!</p>
        <a href="/webbanhang/Product" class="btn btn-primary btn-lg mt-3">Shop Now</a>
    </div>

    <hr class="my-5">

    <div class="row text-center" >
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-delay="100">
            <h3>🚀 Fast Delivery</h3>
            <p>Get your products quickly with our reliable delivery service.</p>
        </div>
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-delay="300">
            <h3>💳 Secure Payments</h3>
            <p>Pay safely using trusted payment gateways.</p>
        </div>
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-delay="500">
            <h3>💬 24/7 Support</h3>
            <p>We're here to help anytime you need assistance.</p>
        </div>
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-delay="700">
            <h3>🔥 Best Deals</h3>
            <p>Save more with daily discounts and promotions.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 1000,
            easing: 'ease-in-sine',
            once: true
        });
    });
</script>

<?php include 'app/views/shared/footer.php' ?>