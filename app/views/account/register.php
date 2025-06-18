<?php include "app/views/shared/header.php" ?>

<div class="container h-100 overflow-hidden">
    <form method="POST" class="card shadow mx-auto mt-5" id="register" action="/webbanhang/account/register"
        style="max-width: 768px;">
        <h1 class="card-header">Register</h1>
        <div class="card-body">
            <p class="card-title">Please fill in your details to create an account.</p>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <?php if (isset($errors) && in_array('Username is required.', $errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <strong>Error:</strong> Username is required.
                            </div>
                        <?php endif; ?>

                        <input type="text" name="username" id="username" placeholder="" required class="form-control">
                        <label for="username" class="form-label text-muted">Username</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <?php if (isset($errors) && in_array('Full name is required.', $errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <strong>Error:</strong> Full name is required.
                            </div>
                        <?php endif; ?>

                        <input type="text" name="fullname" id="fullname" placeholder="" required class="form-control">
                        <label for="fullname" class="form-label text-muted">Full Name</label>
                    </div>
                </div>
            </div>
            <div class="form-floating mb-3">
                <?php if (isset($errors) && in_array('Password is required.', $errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Error:</strong> Password is required.
                    </div>
                <?php endif; ?>
                <input type="password" name="password" id="password" placeholder="" autocomplete="new-password" required class="form-control">
                <label for="password" class="form-label text-muted">Password</label>
            </div>
            <div class="form-floating mb-3">
                <?php if (isset($errors) && in_array('Password do not match.', $errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Error:</strong> Password do not match.
                    </div>
                <?php endif; ?>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="" required
                    class="form-control">
                <label for="confirmPassword" class="form-label text-muted">Confirm Password</label>
            </div>
        
            <div class="d-flex flex-column gap-2">
                <span class="text-left">Or register with</span>
            <a href="/webbanhang/oauth/facebook" class="btn btn-outline-primary d-flex align-items-center justify-content-center fw-bold fs-5">
                    <i class="fa-brands fa-facebook-f me-2 "></i>
                    <span>Register with Facebook</span>
                </a>
                <a href="/webbanhang/oauth/google" class="btn btn-outline-danger d-flex align-items-center justify-content-center fw-bold fs-5">
                    <i class="fa-brands fa-google me-2 "></i>
                    <span>Register with Google</span>
                </a>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="/webbanhang/account/login" class="btn btn-lg btn-outline-secondary hvr-icon-back"><i
                    class="fa fa-arrow-left me-2 hvr-icon"></i>Back to Login</a>
            <button class="btn btn-lg btn-success hvr-icon-back" form="register" type="submit"><i
                    class="hvr-icon fa fa-user-plus me-2"></i>Register</button>
        </div>
    </form>
</div>

<?php include "app/views/shared/footer.php" ?>