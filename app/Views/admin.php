<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Produk</title>
    <!-- Tambahkan CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .status-selesai {
            background-color: grey;
        }
        .status-diproses {
            background-color: red;
        }
        .status-dikirim {
            background-color: green;
        }
        .no-resi {
            position: absolute;
            bottom: 10px;
            right: 10px;
            padding: 5px 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-weight: bold;
        }
        .alert-custom {
            position: fixed;
            top: -100px; /* Mulai dari luar layar */
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: 80%;
            max-width: 400px;
            opacity: 0; /* Mulai dengan transparansi */
            animation: slideIn 0.5s forwards, fadeOut 0.5s 2.5s forwards; /* Menambahkan fadeOut setelah delay */
            text-align:center;
        }

        /* Animasi muncul (slide in) dari atas */
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

        /* Animasi menghilang (fade out) ke atas */
        @keyframes fadeOut {
            0% {
                top: 20px;
                opacity: 1;
            }
            100% {
                top: -100px; /* Pindah ke luar layar */
                opacity: 0; /* Menghilang */
            }
        }
    </style>
<body class="bg-light">
<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-custom" role="alert">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Kiri: Nama Toko -->
        <div>
            <h1 class="text-left">Electroko</h1>
            <p class="text-left">Toko Elektronik</p>
        </div>
        <!-- Kanan: Foto Profil dan Nama Pengguna -->
        <div class="d-flex align-items-center">
            <?php if (session()->get('logged_in')): ?>
            <!-- Menampilkan foto profil jika pengguna sudah login -->
            <div class="text me-3">
                <strong><?= session()->get('username') ?></strong><br>
                <small><?= session()->get('role') ?></small>
            </div>
            <div>
                <img src="<?= session()->get('foto') ?>" alt="Foto Profil" class="rounded-circle" style="width: 65px; height: 65px; object-fit: cover; border: 3px solid #007bff;padding: 2px;">
            </div>
            <?php endif; ?>
        </div>
    </div>
<br>

    <!-- Tab Navigasi -->
    <ul class="nav nav-pills" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="produk-tab" data-bs-toggle="tab" href="#kelola-produk" role="tab" aria-controls="kelola-produk" aria-selected="true">Kelola Produk</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="pesanan-tab" data-bs-toggle="tab" href="#kelola-pesanan" role="tab" aria-controls="kelola-pesanan" aria-selected="false">Kelola Pesanan</a>
        </li>
    </ul>

    <!-- Tab Konten -->
    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="kelola-produk" role="tabpanel" aria-labelledby="produk-tab">
            <!-- Kelola Produk -->
            <div class="d-flex justify-content-between mb-4">
                <div class="col-md-7">
                    <form method="get" action="<?= base_url('admin') ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                            <a href="javascript:void(0);" class="btn btn-outline-secondary" id="reset-search"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </form>
                </div>
                <div class="ms-2 d-flex align-items-center">
                    <a href="<?= site_url('produk/tambah') ?>" class="btn btn-success me-2">
                        <i class="bi bi-plus"></i> Tambah Produk
                    </a>
                    <?php if (session()->get('logged_in')): ?>
                        <a href="javascript:void(0);" class="btn btn-danger" onclick="confirmLogout()">Keluar <i class="bi bi-box-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Daftar Produk -->
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <?php if (empty($produk)): ?>
                            <div class="col-12">
                                <div class="card text-center" style="border: none;">
                                    <div class="card-body">
                                        <div class="alert alert-warning" role="alert">
                                            Produk tidak ditemukan :(
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($produk as $item): ?>
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100 d-flex flex-column border-0">
                                        <img src="<?= $item['gambar'] ?>" class="card-img-top" alt="<?= $item['nama_produk'] ?>" style="height: 250px; object-fit: contain;">
                                        <div class="card-body d-flex flex-column" style="min-height: 250px;">
                                            <h5 class="card-title" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 3em; line-height: 1.4em;">
                                                <?= $item['nama_produk'] ?>
                                            </h5>
                                            <p class="card-text" style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 6em;">
                                                <?= $item['deskripsi'] ?>
                                            </p>
                                            <div class="mt-auto">
                                                <p class="card-text">Stok: <?= $item['stok'] ?></p>
                                                <p class="card-text">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                                                <a href="<?= site_url('produk/edit/' . $item['id_produk']) ?>" class="btn btn-primary w-100 mb-2">Edit Produk</a>
                                                <a href="javascript:void(0);" class="btn btn-outline-secondary w-100" onclick="return confirmDelete(<?= $item['id_produk'] ?>)">Hapus Produk</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <!-- batas tab kelola produk -->
        <div class="tab-pane  class="bg-light"" id="kelola-pesanan" role="tabpanel" aria-labelledby="pesanan-tab">
            <!-- Tab Kelola Pesanan (Kosongkan untuk sekarang) -->
            <div class="row mt-4">
                <!-- Daftar Transaksi -->
                <div class="col-lg-12">
                <?php if (!empty($transaksi)): ?>
                <?php foreach ($transaksi as $item): ?>
                    <div class="bg-white p-3 rounded mb-4 position-relative">
                        <!-- Status Badge -->
                        <div class="status-badge <?php
                            if ($item['transaksi']['status'] == 'menunggu pembayaran') {
                                echo 'status-menunggu';
                            } elseif ($item['transaksi']['status'] == 'selesai') {
                                echo 'status-selesai';
                            } elseif ($item['transaksi']['status'] == 'diproses') {
                                echo 'status-diproses';
                            } elseif ($item['transaksi']['status'] == 'dikirim') {
                                echo 'status-dikirim';
                            }
                        ?>">
                        <?php if ($item['transaksi']['status'] == 'diproses'): ?>
                            Menunggu Dikirim
                        <?php else: ?>
                            <?= ucfirst($item['transaksi']['status']) ?>
                        <?php endif; ?>

                        </div>

                        <h5>ID Transaksi: <?= $item['transaksi']['id_transaksi'] ?></h5>
                        <hr class="my-2">
                        <h6>ID Pelanggan: <?= $item['transaksi']['id_pengguna'] ?> | Nama Pelanggan: <?= $item['transaksi']['nama_pelanggan'] ?></h6>
                        <p>Alamat Tujuan : <?= $item['transaksi']['alamat'] ?></p>
                            
                        <h6>Daftar Produk:</h6>
                        <?php foreach ($item['detail'] as $detail): ?>
                            <div class="d-flex align-items-center p-3 rounded mb-3" style="background-color:rgb(244, 244, 244);">
                                <div class="me-3">
                                    <img src="<?= $detail['gambar'] ?>" class="img-fluid" style="max-width: 100px; height: auto;">
                                </div>
                                <div class="flex-grow-1">
                                    <h6><?= $detail['nama_produk'] ?></h6>
                                    <p class="mb-1">Harga: <strong>Rp <?= number_format($detail['harga'], 0, ',', '.') ?></strong></p>
                                    <p class="mb-0">Jumlah: <strong><?= $detail['jumlah'] ?></strong></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <h5>Total Harga : <strong>Rp <?= number_format($item['totalHarga'], 0, ',', '.') ?></strong></h5>
                        <hr class="my-2">
                        <?php if ($item['transaksi']['status'] == 'diproses'): ?>
                            <div class="mt-3 d-flex gap-2">   
                                <!-- Tombol Lihat Bukti Bayar -->
                                <?php if ($item['transaksi']['bukti_bayar']): ?>                    
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lihatBuktiBayarModal<?= $item['transaksi']['id_transaksi'] ?>">Lihat Bukti Bayar</button>
                                <?php endif; ?>    
                                <!-- Tombol Update Resi -->
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#updateResiModal<?= $item['transaksi']['id_transaksi'] ?>">Update Resi</button>           
                            </div>                               
                        <?php endif; ?>

  

                        <!-- Modal untuk Update Resi -->
                        <div class="modal fade" id="updateResiModal<?= $item['transaksi']['id_transaksi'] ?>" tabindex="-1" aria-labelledby="updateResiModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 500px;"> <!-- Atur ukuran modal -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateResiModalLabel">Update Resi Pengiriman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center"> <!-- Tambahkan text-center untuk membuat konten modal berada di tengah -->
                                        <form method="POST" action="<?= site_url('admin/updateResi/' . $item['transaksi']['id_transaksi']) ?>">
                                            <div class="mb-3">
                                                <label for="resi" class="form-label">Nomor Resi</label>
                                                <input type="text" class="form-control" id="resi" name="resi" required>
                                            </div>
                                            <button type="submit" class="btn btn-success">Update Resi</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Modal untuk Lihat Bukti Bayar -->
                        <div class="modal fade" id="lihatBuktiBayarModal<?= $item['transaksi']['id_transaksi'] ?>" tabindex="-1" aria-labelledby="lihatBuktiBayarModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-m"> <!-- Ukuran modal tetap besar, tengah vertikal -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="lihatBuktiBayarModalLabel">Bukti Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex justify-content-center align-items-center">
                                        <!-- Gambar Bukti Pembayaran -->
                                        <img src="<?= $item['transaksi']['bukti_bayar'] ?>" alt="Bukti Pembayaran" class="img-fluid" style="max-height: 500px; max-width: 100%; object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No Resi -->
                        <div class="no-resi">
                            <i class="bi bi-truck"></i> : <?= $item['transaksi']['resi_pengiriman'] ?? '-' ?>
                        </div>




                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada transaksi</p>
            <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-primary text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Tentang Kami</h5>
                <p>Electroko adalah toko Elektronik terpercaya yang menyediakan berbagai produk elektronik dengan harga terbaik.</p>
            </div>
            <div class="col-md-4">
                <h5>Dibuat Oleh</h5>
                <ul class="list-unstyled">
                    <li>Nama: Wildan De Ciyan</li>
                    <li>NIM: 10122194</li>
                    <li>Kelas: IF-5</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Ikuti Kami</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white"><i class="bi bi-facebook"></i> Facebook</a></li>
                    <li><a href="#" class="text-white"><i class="bi bi-twitter"></i> Twitter</a></li>
                    <li><a href="#" class="text-white"><i class="bi bi-instagram"></i> Instagram</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <p>&copy; 2024 Electroko. All Rights Reserved.</p>
    </div>
</footer>

<!-- Tambahkan JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('reset-search').addEventListener('click', function() {
        document.getElementById('search').value = '';
        window.location.href = "<?= site_url('admin') ?>"; // Mengarahkan kembali ke tab aktif
        
    });

    function confirmDelete(id_produk) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            window.location.href = "<?= site_url('produk/hapus') ?>/" + id_produk;
        }
        return false;
    }

    function confirmLogout() {
        if (confirm("Apakah Anda yakin ingin logout?")) {
            window.location.href = "<?= site_url('logout') ?>";
        }
    }
</script>

</body>
</html>
