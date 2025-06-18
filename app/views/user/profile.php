<?php include 'app/views/shared/header.php' ?>

<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fa-solid fa-id-card me-3 fs-3"></i>
            <h1 class="mb-0">User Profile</h1>
        </div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="d-flex justify-content-between mx-3 flex-fill">
                <a href="/webbanhang/user/profile/<?= $account->id ?>" class="btn btn-outline-secondary me-2">
                    <i class="fa-solid fa-id-card me-2"></i>Profile
                </a>
                <div class="d-flex">
                    <a href="/webbanhang/user/edit-profile/<?= $account->id ?>" class="btn btn-outline-primary me-2">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Edit Profile
                    </a>
                    <a href="/webbanhang/user/change-password/<?= $account->id ?>" class="btn btn-outline-danger">
                        <i class="fa-solid fa-key me-2"></i>Change Password
                    </a>
                </div>
            </div>
        </nav>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                        <i class="fa-solid fa-user fa-5x text-secondary"></i>
                    </div>
                    <h4 class="mt-3"><?= $account->fullname ?></h4>
                    <span class="badge bg-<?= $account->role === 'admin' ? 'danger' : 'info' ?> fs-6 px-3 py-2"><?= ucfirst($account->role) ?></span>
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user me-2 text-primary"></i>Username</label>
                                <input type="text" class="form-control form-control-lg bg-light" value="<?= $account->username ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold"><i class="fa-solid fa-user me-2 text-primary"></i>Full Name</label>
                                <input type="text" class="form-control form-control-lg bg-light" value="<?= $account->fullname ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold"><i class="fa-solid fa-envelope me-2 text-primary"></i>Email Address</label>
                                <input type="email" class="form-control form-control-lg bg-light" value="<?= $account->email ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold"><i class="fa-solid fa-calendar me-2 text-primary"></i>Member Since</label>
                                <input type="text" class="form-control form-control-lg bg-light" value="<?= isset($account->created_at) ? date('F j, Y', strtotime($account->created_at)) : 'N/A' ?>" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shared/footer.php'; ?>