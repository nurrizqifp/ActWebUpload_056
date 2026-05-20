<?php
session_start();

$dir = "uploads/";

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "delete") {
    if (isset($_POST["file"]) && !empty($_POST["file"])) {
        $filename = basename($_POST["file"]);
        $filePath = $dir . $filename;
        
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                $_SESSION["msg"] = "Berkas <strong>" . htmlspecialchars($filename) . "</strong> berhasil dihapus secara permanen.";
                $_SESSION["msg_type"] = "success";
            } else {
                $_SESSION["msg"] = "Gagal menghapus berkas. Periksa hak akses file.";
                $_SESSION["msg_type"] = "danger";
            }
        } else {
            $_SESSION["msg"] = "Berkas tidak ditemukan.";
            $_SESSION["msg_type"] = "warning";
        }
    }
    header("Location: lihat_file.php");
    exit;
}

$files = array_diff(scandir($dir), array('.', '..'));

function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'webp':
        case 'svg':
            return 'bi-file-earmark-image text-success';
        case 'pdf':
            return 'bi-file-earmark-pdf text-danger';
        case 'zip':
        case 'rar':
        case 'tar':
        case 'gz':
            return 'bi-file-earmark-zip text-warning';
        case 'txt':
        case 'md':
            return 'bi-file-earmark-text text-secondary';
        case 'php':
        case 'html':
        case 'css':
        case 'js':
            return 'bi-file-earmark-code text-primary';
        default:
            return 'bi-file-earmark text-muted';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Berkas Terunggah - WebUpload</title>
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
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem;
        }
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        .btn-action {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s ease;
            border: 1px solid;
            background: none;
        }
        .btn-action:hover {
            transform: translateY(-2px);
        }
        .preview-img {
            max-width: 60px;
            max-height: 40px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .preview-modal-body img {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .preview-modal-body pre {
            background-color: #1e1e1e;
            color: #d4d4d4;
            padding: 1.5rem;
            border-radius: 8px;
            max-height: 450px;
            overflow-y: auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            text-align: left;
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
                    <a class="nav-link active" href="lihat_file.php"><i class="bi bi-folder2-open me-1"></i> Lihat File</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card p-4 p-md-5">
                
                <?php if (isset($_SESSION["msg"])): ?>
                    <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($_SESSION["msg_type"] === 'success'): ?>
                                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                            <?php else: ?>
                                <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                            <?php endif; ?>
                            <span><?php echo $_SESSION["msg"]; ?></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                    unset($_SESSION["msg"]);
                    unset($_SESSION["msg_type"]);
                    ?>
                <?php endif; ?>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Daftar Berkas Terunggah</h2>
                        <p class="text-muted mb-0">Kelola dan lihat detail berkas yang telah berhasil diunggah ke server.</p>
                    </div>
                    <a href="index.html" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-sm">
                        <i class="bi bi-plus-lg"></i> Unggah Baru
                    </a>
                </div>

                <?php if (empty($files)): ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-muted mb-3"><i class="bi bi-folder2"></i></div>
                        <h4 class="fw-semibold">Belum Ada Berkas</h4>
                        <p class="text-muted">Gunakan tombol 'Unggah Baru' untuk menambahkan berkas pertama Anda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive shadow-sm">
                        <table class="table table-hover align-middle bg-white">
                            <thead>
                                <tr>
                                    <th>Pratinjau</th>
                                    <th>Nama Berkas</th>
                                    <th>Ukuran</th>
                                    <th>Jenis</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($files as $file): 
                                    $filePath = $dir . $file;
                                    $size = formatSize(filesize($filePath));
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                ?>
                                    <tr>
                                        <td>
                                            <?php if ($isImage): ?>
                                                <img src="<?php echo $filePath; ?>" class="preview-img" alt="<?php echo htmlspecialchars($file); ?>">
                                            <?php else: ?>
                                                <span class="fs-3"><i class="bi <?php echo getFileIcon($file); ?>"></i></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark text-break"><?php echo htmlspecialchars($file); ?></span>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?php echo $size; ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border px-2 py-1.5 uppercase font-monospace"><?php echo strtoupper($ext); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-action btn-outline-info" 
                                                        title="Pratinjau File"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#previewModal" 
                                                        data-filename="<?php echo htmlspecialchars($file); ?>"
                                                        data-filepath="<?php echo $filePath; ?>"
                                                        data-ext="<?php echo $ext; ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="<?php echo $filePath; ?>" download class="btn btn-action btn-outline-success" title="Unduh File">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <a href="<?php echo $filePath; ?>" target="_blank" class="btn btn-action btn-outline-primary" title="Buka di Tab Baru">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                                <form action="lihat_file.php" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas <?= htmlspecialchars($file); ?> secara permanen?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="file" value="<?= htmlspecialchars($file); ?>">
                                                    <button type="submit" class="btn btn-action btn-outline-danger" title="Hapus File">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white rounded-top-4 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="previewModalLabel">
                    <i class="bi bi-eye-fill text-info"></i> Pratinjau Berkas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body preview-modal-body p-4 text-center">
                <div id="modalLoading" class="py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="modalContent"></div>
            </div>
            <div class="modal-footer bg-light border-top-0 rounded-bottom-4 py-3">
                <span id="modalFilename" class="me-auto text-muted text-truncate font-monospace small px-2"></span>
                <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
                <a id="modalDownloadBtn" href="#" download class="btn btn-success px-4 rounded-3 d-flex align-items-center gap-2">
                    <i class="bi bi-download"></i> Unduh
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const previewModal = document.getElementById('previewModal');
    previewModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const filename = button.getAttribute('data-filename');
        const filepath = button.getAttribute('data-filepath');
        const ext = button.getAttribute('data-ext');

        const modalContent = document.getElementById('modalContent');
        const modalLoading = document.getElementById('modalLoading');
        const modalFilename = document.getElementById('modalFilename');
        const modalDownloadBtn = document.getElementById('modalDownloadBtn');

        modalFilename.textContent = filename;
        modalDownloadBtn.setAttribute('href', filepath);
        modalLoading.style.display = 'block';
        modalContent.innerHTML = '';

        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        const textExtensions = ['txt', 'md', 'html', 'css', 'js', 'json', 'php'];

        if (imageExtensions.includes(ext)) {
            const img = document.createElement('img');
            img.src = filepath;
            img.className = 'img-fluid rounded shadow-sm';
            img.onload = function() {
                modalLoading.style.display = 'none';
                modalContent.appendChild(img);
            };
        } else if (textExtensions.includes(ext)) {
            fetch(filepath)
                .then(response => response.text())
                .then(data => {
                    modalLoading.style.display = 'none';
                    const pre = document.createElement('pre');
                    const code = document.createElement('code');
                    code.textContent = data;
                    pre.appendChild(code);
                    modalContent.appendChild(pre);
                })
                .catch(err => {
                    modalLoading.style.display = 'none';
                    modalContent.innerHTML = `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal memuat isi file: ${err.message}</div>`;
                });
        } else if (ext === 'pdf') {
            modalLoading.style.display = 'none';
            modalContent.innerHTML = `<iframe src="${filepath}" width="100%" height="450px" style="border: none; border-radius: 8px;"></iframe>`;
        } else {
            modalLoading.style.display = 'none';
            modalContent.innerHTML = `
                <div class="py-4">
                    <span style="font-size: 4rem;"><i class="bi bi-file-earmark-arrow-down text-secondary"></i></span>
                    <h5 class="mt-3 fw-bold">Pratinjau tidak tersedia</h5>
                    <p class="text-muted">Format file .${ext.toUpperCase()} tidak mendukung pratinjau langsung. Silakan unduh file untuk melihat kontennya.</p>
                </div>
            `;
        }
    });
});
</script>
</body>
</html>
