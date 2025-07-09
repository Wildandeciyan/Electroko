<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Toko Online</title>
    <!-- Tambahkan CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .image-container {
            height: calc(100vh - 60px); /* Kurangi tinggi untuk memberi ruang tombol */
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            padding-bottom: 1rem;
        }
        .image-container img {
            width:88%;
            max-height: 88%; /* Batasi tinggi gambar agar ada ruang untuk tombol */
            max-width: 100%;
            object-fit: contain;
            margin-bottom: 1rem; /* Ruang di bawah gambar */
        }
        .content-container {
            height: 100vh;
            overflow-y: auto;
            padding: 2rem;
        }
        .alert-custom {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: 80%;
            max-width: 400px;
            opacity: 0;
            animation: slideIn 0.5s forwards, fadeOut 0.5s 2.5s forwards;
            text-align: center;
        }
        @keyframes slideIn {
            0% {
                top: -100px;
                opacity: 0;
            }
            100% {
                top: 20px;
                opacity: 1;
            }
        }
        @keyframes fadeOut {
            0% {
                top: 20px;
                opacity: 1;
            }
            100% {
                top: -100px;
                opacity: 0;
            }
        }
        .image-container button {
        max-width: 80%; /* Sesuaikan lebar tombol dengan gambar */
        width: 100%; /* Pastikan tombol adaptif */
    }
    </style>
</head>
<body>
    <!-- Tombol Kembali -->
    <a href="<?= site_url(relativePath: '/') ?>" class="position-absolute top-0 start-3 m-3" style="text-decoration: none; color: inherit;">
        <button class="btn btn-close"></button>
    </a>
<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-custom" role="alert">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
<div class="container-fluid">
    <div class="row">
        <!-- Bagian kiri: Gambar produk dan tombol -->
        <div class="col-md-6 image-container text-center">
            <img src="<?= $produk['gambar'] ?>" alt="<?= $produk['nama_produk'] ?>">
            <?php if (session()->get('logged_in')): ?>
                <?php if ($produk['stok'] > 0): ?>
                    <!-- Form untuk mengirimkan data produk ke keranjang -->
                    <form action="<?= site_url('keranjang/tambahKeranjang') ?>" method="POST" class="w-100 mt-4">
                        <input type="hidden" name="produk_id" value="<?= $produk['id_produk'] ?>">
                        <input type="hidden" name="produk_nama" value="<?= $produk['nama_produk'] ?>">
                        <input type="hidden" name="produk_harga" value="<?= $produk['harga'] ?>">
                        <button type="submit" class="btn btn-primary mt-4">+ Keranjang</button>
                    </form>
                <?php else: ?>
                    <!-- Jika stok produk 0 -->
                    <button class="btn btn-secondary mt-4" disabled>Produk Habis</button>
                <?php endif; ?>
            <?php else: ?>
                <!-- Tombol untuk pengguna yang belum login -->
                <a href="<?= site_url('login') ?>" class="btn btn-primary mt-4">Login untuk Tambah ke Keranjang</a>
            <?php endif; ?>
        </div>


        <!-- Bagian kanan: Detail produk -->
        <div class="col-md-6 content-container">
            <h1 class="mb-4"><?= $produk['nama_produk'] ?></h1>
            <h3 class="text-primary mb-4">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></h3>
            <p class="mb-4">Deskripsi:</p>
            <p class="mb-4" style="white-space: pre-wrap;">
                <?= $produk['deskripsi'] ?>
            </p>
            <p class="mb-4">Stok: <strong><?= $produk['stok'] ?></strong></p>
        </div>
    </div>
</div>

<!-- Tambahkan JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                