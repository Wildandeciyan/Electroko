<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Toko Online</title>
    <!-- Tambahkan CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

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
    <!-- Kolom Pencarian -->
    <div class="d-flex justify-content-between mb-4 ">
        <div class="col-md-9">
            <form method="get" action="<?= base_url() ?>">
                <div class="input-group mb-4">
                    <!-- Kolom Pencarian Kiri -->
                    <input type="text" class="form-control" id="search" name="search" placeholder="Cari produk..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    <a href="javascript:void(0);" class="btn btn-outline-secondary" id="reset-search"><i class="bi bi-x-lg"></i></a>               
                </div>
            </form>
        </div>
        <!-- Tombol Login dan Daftar di sebelah kanan -->
        <div class="ms-auto">
            <?php if (session()->get('logged_in')): ?>
                <?php if (session()->get('role') == 'pelanggan'): ?>
                    <!-- Tombol Keranjang dengan Lingkaran Merah -->
                    <a href="<?= site_url('keranjang') ?>" class="btn btn-outline-primary ms-2 position-relative">
                        <i class="bi bi-cart-fill"></i> <!-- Icon Keranjang -->

                        <!-- Lingkaran Merah dengan jumlah item -->
                        <?php if ($jumlahItemKeranjang > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger">
                                <?= $jumlahItemKeranjang ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <!-- Tombol Notifikasi -->
                    <a href="<?= site_url('bayar') ?>" class="btn btn-outline-primary ms-2">
                        <i class="bi bi-bell-fill"></i> <!-- Ikon Notifikasi -->
                    </a>

                <?php endif;?>
                <!-- Jika sudah login, tampilkan tombol Logout -->
                <a href="javascript:void(0);" class="btn btn-danger ms-2" onclick="confirmLogout()">Keluar <i class="bi bi-box-arrow-right"></i></button></a>
            <?php else: ?>
                <!-- Jika belum login, tampilkan tombol Login dan Daftar -->
                <a href="<?= site_url('login') ?>" class="btn btn-success ms-2">Masuk</a>
                <a href="<?= site_url('daftar') ?>" class="btn btn-outline-success ms-2">Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- kolom filter -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <!-- Filter Rentang Harga -->
                    <h5>Filter Produk</h5>
                    <form method="get" action="<?= base_url() ?>">
                        <div class="mb-3">
                            <label for="min_harga" class="form-label">Harga Minimum</label>
                            <input type="number" class="form-control" id="min_harga" name="min_harga" placeholder="0" value="<?= isset($_GET['min_harga']) ? $_GET['min_harga'] : '' ?>">
                        </div>
                        <div class="mb-3">  
                            <label for="max_harga" class="form-label">Harga Maksimum</label>
                            <input type="number" class="form-control" id="max_harga" name="max_harga" placeholder="0" value="<?= isset($_GET['max_harga']) ? $_GET['max_harga'] : '' ?>">
                        </div>
                        <!-- Filter Kategori -->
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                <!-- Looping kategori dari database -->
                                <?php foreach ($kategori as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>" <?= isset($_GET['kategori']) && $_GET['kategori'] == $kat['id_kategori'] ? 'selected' : '' ?>><?= $kat['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Tombol Filter -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            <!-- Tombol Reset Filter -->
                            <a href="javascript:void(0);" class="btn btn-outline-secondary" id="reset-filter">Reset Filter</a>
                        </div>
                    </form> 
                </div>
            </div>
        </div>

        <!-- Daftar Produk -->
        <div class="col-md-9 mb-4">
            <div class="row">
                <?php if (empty($produk)): ?>
                    <!-- Menampilkan pesan jika tidak ada produk yang ditemukan -->                       
                    <div class="container text-center">
                        <!-- Gambar dengan ukuran besar -->
                        <img src="<?= base_url('Aset/tidak_ada.png') ?>" class="img-fluid mb-4" style="max-width: 400px;">
                        <!-- Pesan Keranjang Kosong -->
                        <h1 class="display-5 font-weight-bold mb-3">
                            Produk tidak ditemukan :(
                        </h1>
                    </div>
                <?php else: ?>
                    <!-- Looping produk jika ada produk -->
                    <?php foreach ($produk as $item): ?>
                        <div class="col-md-3 mb-4"> <!-- Mengubah col-md-4 menjadi col-md-3 untuk muat 4 produk per baris -->
                        <div class="card h-100 d-flex flex-column" 
                                <?php if (session()->get('role') !== 'admin'): ?>
                                    onclick="window.location='<?= site_url('detail_produk/' . $item['id_produk']) ?>'"
                                <?php endif; ?>
                                <!-- Menampilkan gambar produk dengan object-fit: contain untuk memastikan gambar tidak terpotong -->
                                <img src="<?= $item['gambar'] ?>" class="card-img-top" alt="<?= $item['nama_produk'] ?>" style="height: 250px; object-fit: contain;">
                                <div class="card-body d-flex flex-column" style="min-height: 250px;">
                                    <h5 class="card-title" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 3em; line-height: 1.4em;"><?= $item['nama_produk'] ?></h5>
                                    <p class="card-text" style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 6em;"><?= $item['deskripsi'] ?></p>
                                    <div class="mt-auto">
                                        <p class="card-text">Stok: <?= $item['stok'] ?></p>
                                        <p class="card-text">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
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
            <div class="col-md-4">
                <h5>Alamat</h5>
                <p>Jl.Dipati Ukur no 31 kelurahan Coblong Kota Bandung</p>
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
    document.getElementById('reset-filter').addEventListener('click', function() {
        // Reset harga minimum dan maksimum
        document.getElementById('min_harga').value = '';
        document.getElementById('max_harga').value = '';

        // Reset kategori ke default (Semua Kategori)
        document.getElementById('kategori').selectedIndex = 0;

        // Arahkan ulang ke halaman tanpa parameter GET (reset filter)
        window.location.href = "<?= base_url() ?>";
    });
    document.getElementById('reset-search').addEventListener('click', function() {
        // Reset harga minimum dan maksimum
        document.getElementById('min_harga').value = '';
        // Arahkan ulang ke halaman tanpa parameter GET (reset filter)
        window.location.href = "<?= base_url() ?>";
    });
    function confirmLogout() {
        // Menampilkan pesan konfirmasi
        if (confirm("Apakah Anda yakin ingin logout?")) {
            // Jika pengguna mengklik 'OK', arahkan ke URL logout
            window.location.href = "<?= site_url('logout') ?>";
        }
    }
</script>
</body>
</html>
