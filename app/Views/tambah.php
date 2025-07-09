<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tambah Produk</title>
    <!-- Tambahkan CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


</head>
<body class="bg-light">
        <!-- Tombol Kembali -->
        <a href="<?= site_url(relativePath: 'admin') ?>" class="position-absolute top-0 start-3 m-3" style="text-decoration: none; color: inherit;">
            <button class="btn btn-close"></button>
        </a>    

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="bg-white p-4 rounded">
                <h1 class="text-center">Tambah Produk</h1>
                <p class="text-center">Masukkan informasi produk baru</p>

                <form method="POST" action="<?= site_url('produk/tambah') ?>">
                    <!-- Gambar Produk -->
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-file-earmark-image"></i></span>
                            <input type="text" class="form-control" id="gambar" name="gambar" required>
                        </div>
                    </div>

                    <!-- Nama Produk -->
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box"></i></span>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                        </div>
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
                    </div>

                    <!-- Harga Produk -->
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Produk</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>
                    </div>

                    <!-- Stok Produk -->
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok Produk</label>
                        <input type="number" class="form-control" id="stok" name="stok" required>
                    </div>

                    <!-- Kategori Produk -->
                    <div class="mb-3">
                        <label for="kategoriT" class="form-label">Kategori</label>
                        <select class="form-select" id="kategoriT" name="kategoriT">
                            <option value="">Semua Kategori</option>
                            <!-- Looping kategori dari database -->
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat['id_kategori'] ?>" <?= isset($kategori) && $kategori == $kat['id_kategori'] ? 'selected' : '' ?>><?= $kat['nama_kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-success w-100">Tambah Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Tambahkan icon Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
