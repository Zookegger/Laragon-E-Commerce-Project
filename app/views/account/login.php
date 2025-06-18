<?php include 'app/views/shared/header.php'; ?>

<div class="container h-100 overflow-hidden">
    <form method="post" class="card shadow mx-auto mt-5" style="max-width: 500px;" id="login" action="/webbanhang/account/login">
        <h1 class="card-header text-center fw-bold bg-dark text-white">Login</h1>
        <div class="card-body">
            <p class="card-title">Please enter your credentials to login.</p>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <div class="form-floating mb-3">
                <input type="text" name="username" id="username" autocomplete="username" placeholder="" required class="form-control">
                <label for="username" class="form-label text-muted">Username</label>
            </div>
            <div class="d-flex">
                <div class="form-floating mb-3 flex-grow-1">
                    <input type="password" name="password" id="password" autocomplete="current-password" placeholder="" required class="form-control">
                    <label for="password" class="form-label text-muted">Password</label>
                </div>
                <button type="button" class="btn btn-outline-secondary inline-block" style="max-height: 58px" id="togglePassword">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="/webbanhang/oauth/facebook" class="btn btn-outline-primary d-flex align-items-center justify-content-center fw-bold fs-5">
                    <i class="fa-brands fa-facebook-f me-2 "></i>
                    <span>Login with Facebook</span>
                </a>
                <a href="/webbanhang/oauth/google" class="btn btn-outline-danger d-flex align-items-center justify-content-center fw-bold fs-5">
                    <i class="fa-brands fa-google me-2 "></i>
                    <span>Login with Google</span>
                </a>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <button class="btn btn-lg btn-success hvr-icon-back" form="login" type="submit"><i
                        class="hvr-icon fa fa-sign-in me-2"></i>Login</button>
                <a href="/webbanhang/account/register" class="btn btn-lg btn-outline-secondary hvr-icon-back"><i
                        class="fa fa-user-plus me-2 hvr-icon"></i>Register</a>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#togglePassword').click(function() {
            const passwordInput = $('#password');
            let isShow = passwordInput.attr('type') === 'password';
            passwordInput.attr('type', isShow ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye-slash fa-eye');
        });

        $('#login').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const jsonData = {};

            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            $.ajax({
                url: '/webbanhang/account/login',
                type: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(jsonData),
                success: function(response) {
                    const data = JSON.parse(response);
                    console.log(data);
                    if (data.success) {
                        localStorage.setItem('jwtToken', data.jwtToken);
                    }
                    alert(data.message);
                },
                error: function(response, xhr, status, error) {
                    alert("Error: " + error);
                },
                complete: function(xhr, status) {
                    if (status === 'success') {
                        window.location.href = '/webbanhang';
                    }
                }
            })
        });
    });
</script>

<?php include 'app/views/shared/footer.php'; ?>