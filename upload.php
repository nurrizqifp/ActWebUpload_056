<?php
$target_dir = "uploads/";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

$message = "";
$messageType = "danger";

if (file_exists($target_file)) {
    $message = "Maaf, berkas sudah ada di server.";
    $messageType = "warning";
    $uploadOk = 0;
}

if ($_FILES["fileToUpload"]["size"] > 5000000) {
    $message = "Maaf, berkas Anda terlalu besar (Maksimal 5 MB).";
    $messageType = "danger";
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    if (empty($message)) {
        $message = "Maaf, berkas Anda tidak dapat diunggah.";
    }
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $message = "Berkas <strong>" . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . "</strong> telah berhasil diunggah.";
        $messageType = "success";
    } else {
        $message = "Maaf, terjadi kesalahan saat memindahkan berkas di server. Periksa hak akses folder uploads.";
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Unggahan - WebUpload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #2d3748;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08);
        }
        .status-icon {
            font-size: 4rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
            <i class="bi bi-cloud-arrow-up-fill text-info fs-4"></i>
            <span class="fw-bold tracking-tight">WebUpload</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav gap-2">
                <li class="nav-item">
                    <a class="nav-link" href="index.html"><i class="bi bi-house-door me-1"></i> Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lihat_file.php"><i class="bi bi-folder2-open me-1"></i> Lihat File</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="glass-card p-4 p-md-5 text-center">
                
                <?php if ($messageType === 'success'): ?>
                    <div class="status-icon text-success mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-2">Unggahan Berhasil!</h3>
                <?php elseif ($messageType === 'warning'): ?>
                    <div class="status-icon text-warning mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-2">Unggahan Tertunda</h3>
                <?php else: ?>
                    <div class="status-icon text-danger mb-3">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-2">Unggahan Gagal</h3>
                <?php endif; ?>

                <p class="fs-5 text-secondary px-3 mb-4"><?php echo $message; ?></p>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="index.html" class="btn btn-primary px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </a>
                    <a href="lihat_file.php" class="btn btn-outline-secondary px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-folder2-open"></i> Lihat Semua Berkas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
