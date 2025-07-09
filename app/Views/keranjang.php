<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    // Mengupdate jumlah produk di database dan menghitung ulang total setelah perubahan
    $(".jumlah-input").on("input", function() {
        var jumlah = $(this).val();
        var idKeranjang = $(this).data("id");
        updateJumlah(idKeranjang, jumlah);
        hitungTotal();  // Hitung ulang total setelah jumlah diubah
    });

    $(".jumlah-increment").click(function() {
        var input = $(this).siblings(".jumlah-input");
        var jumlah = parseInt(input.val()) + 1;
        input.val(jumlah);
        var idKeranjang = input.data("id");
        updateJumlah(idKeranjang, jumlah);
        hitungTotal();  // Hitung ulang total setelah jumlah diubah
    });

    $(".jumlah-decrement").click(function() {
        var input = $(this).siblings(".jumlah-input");
        var jumlah = Math.max(1, parseInt(input.val()) - 1);  // Pastikan jumlah tidak kurang dari 1
        input.val(jumlah);
        var idKeranjang = input.data("id");
        updateJumlah(idKeranjang, jumlah);
        hitungTotal();  // Hitung ulang total setelah jumlah diubah
    });

    // Fungsi untuk update jumlah di database menggunakan AJAX
    function updateJumlah(idKeranjang, jumlah) {
        $.ajax({
            url: "<?= site_url('/keranjang/updateJumlah') ?>",  // Ganti dengan URL yang sesuai di server
            method: "POST",
            data: {
                id_keranjang: idKeranjang,
                jumlah: jumlah
            },
            success: function(response) {
                console.log("Jumlah berhasil diperbarui di database");
            },
            error: function() {
                alert("Gagal memperbarui jumlah produk.");
            }
        });
    }

    // Fungsi untuk menghitung total harga berdasarkan checkbox yang dicentang dan jumlah produk
    function hitungTotal() {
        var totalHarga = 0;

        // Ambil nilai jumlah terbaru dari checkbox yang dicentang dan input jumlah yang diperbarui
        $(".produk-checkbox:checked").each(function() {
            var harga = $(this).data("harga");  // Ambil harga produk dari data atribut
            var jumlah = $(this).closest(".d-flex").find(".jumlah-input").val();  // Ambil nilai jumlah dari input terkait produk

            // Pastikan jumlah adalah angka yang valid dan lebih besar dari 0
            if (!isNaN(jumlah) && jumlah > 0) {
                jumlah = parseInt(jumlah);  // Pastikan jumlah adalah angka
                harga = parseInt(harga);  // Pastikan harga adalah angka

                totalHarga += harga * jumlah;  // Hitung total harga dengan harga * jumlah
            }
        });

        // Perbarui total harga di UI
        $("#totalHarga").text("Rp " + totalHarga.toLocaleString());

        // Jika tidak ada checkbox yang dicentang, kembalikan total harga ke 0
        if ($(".produk-checkbox:checked").length === 0) {
            $("#totalHarga").text("Rp 0");
        }
    }

    // Panggil hitungTotal() saat checkbox berubah untuk menghitung total harga awal
    $(".produk-checkbox").change(function() {
        hitungTotal();  // Hitung ulang total setelah checkbox berubah
    });

    // Set total harga awal ke 0
    $("#totalHarga").text("Rp 0");
});

document.addEventListener("DOMContentLoaded", function() {
    $(".btn-beli").click(function(e) {
        e.preventDefault();
        var selectedProducts = $(".produk-checkbox:checked");
        
        if (selectedProducts.length === 0) {
            alert("Silakan pilih produk yang ingin dibeli terlebih dahulu!");
        } else {
            var data = [];
            selectedProducts.each(function() {
                var idKeranjang = $(this).closest(".d-flex").find(".jumlah-input").data("id");
                var jumlah = $(this).closest(".d-flex").find(".jumlah-input").val();
                
                // Menambahkan data ke array
                data.push({ id_keranjang: idKeranjang, jumlah: jumlah });
            });

            // Log data untuk memeriksa struktur yang dikirim
            console.log("Data yang dikirim:", data);

            // Memastikan data tidak kosong
            if (data.length === 0) {
                alert("Tidak ada produk yang dipilih.");
                return;
            }

            // Mengirim data ke server menggunakan AJAX
            $.ajax({
                url: "<?= site_url('/keranjang/checkout') ?>", 
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({ produk: data }),
                success: function(response) {
                    alert("Transaksi berhasil disimpan!");
                    window.location.href = "<?= site_url('/keranjang/bayar') ?>"; // Redirect setelah sukses
                },
                error: function(xhr, status, error) {
                    // Log error untuk debugging
                    console.log("Status: " + status);
                    console.log("Error: " + error);
                    console.log("Response: " + xhr.responseText);

                    // Menampilkan pesan error ke pengguna
                    alert("Gagal menyimpan transaksi. Silakan coba lagicc.");
                }
            });
        }
    });
});


    </script>
</head>
<style>
    /* Menyembunyikan spinner (panah atas/bawah) pada input tipe number */
    .jumlah-input::-webkit-outer-spin-button,
    .jumlah-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .jumlah-input[type="number"] {
        -moz-appearance: textfield; /* Untuk Firefox */
    }
</style>

<body class="bg-light">
        <!-- Tombol Kembali -->
        <a href="<?= site_url(relativePath: '/') ?>" class="position-absolute top-0 start-3 m-3" style="text-decoration: none; color: inherit;">
            <button class="btn btn-close"></button>
        </a>    
<div class="container mt-5">
    <h3 class="mb-4">Keranjang Anda</h3>

    <?php if (empty($keranjang)): ?>
    <div class="container text-center">
        <img src="<?= base_url('Aset/tidak_ada.png') ?>" class="img-fluid mb-4" style="max-width: 500px;">
        <h1 class="display-4 font-weight-bold mb-3">Wah keranjangmu kosong nih!</h1>
        <p class="lead text-muted">Ayo, tambahkan produk favoritmu ke keranjang sekarang juga!</p>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-lg-8">
        <?php foreach ($keranjang as $item): ?>
            <?php
                $produk = $produkModel->find($item['id_produk']);
                $harga = $produk['harga'];
                $gambarProduk = $produk['gambar'] ? $produk['gambar'] : base_url('Aset/tidak_ada.png');
            ?>
            <div class="d-flex align-items-center bg-white p-3 rounded mb-3">
                <div class="me-3 ">
                    <input type="checkbox" class="form-check-input form-check-input-lg produk-checkbox" 
                        data-harga="<?= $harga ?>" 
                        style="width: 20px; height: 20px; border: 3px solid rgb(226, 226, 226);">
                    <img src="<?= $gambarProduk ?>" class="img-fluid" style="max-width: 100px; height: auto;" alt="gambar produk">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <h5><?= $produk['nama_produk'] ?></h5>
                        <p class="mb-0"><strong>Rp <?= number_format($harga, 0, ',', '.') ?></strong></p>
                    </div>
                    <!-- Tempatkan tombol panah kiri-kanan, jumlah, dan tombol hapus di bawah harga -->
                    <div class="d-flex justify-content-end align-items-center mt-2">
                        <button class="btn btn-sm jumlah-decrement">
                            <i class="bi bi-caret-left"></i>
                        </button>

                        <input type="number" class="form-control form-control-sm jumlah-input" 
                            value="<?= $item['jumlah'] ?>" data-id="<?= $item['id_keranjang'] ?>" style="width: 50px; text-align: center;">

                        <button class="btn btn-sm jumlah-increment">
                            <i class="bi bi-caret-right"></i>
                        </button>

                        <a href="<?= site_url('/keranjang/hapus/' . $item['id_keranjang']) ?>" class="btn btn-sm ms-2">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        </div>

        <div class="col-lg-4">
            <div class="bg-white p-3 rounded">
                <h5>Total Belanja</h5>
                <hr>
                <p><strong>Total:</strong> <span id="totalHarga">Rp 0</span></p>
                <a href="<?= site_url('/keranjang/bayar') ?>" class="btn btn-primary btn-lg w-100 btn-beli">Beli</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ZvpUoO/+PpHfM7bP74V3+PmYcMpncfHXg4+5VnlUoVXw2WZyGnp3pB2zL9MI8cXs" crossorigin="anonymous"></script>

</body>
</html>
