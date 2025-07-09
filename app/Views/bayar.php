<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Menambahkan CDN Bootstrap Icons -->
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
        .status-menunggu {
            background-color: red;
        }
        .status-selesai {
            background-color: grey;
        }
        .status-diproses {
            background-color: orange;
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
        .alamat-icon {
            font-size: 20px; /* Ukuran ikon */
            margin-right: 10px; /* Jarak antara ikon dan teks */
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
            animation: slideIn 0.5s forwards, fadeOut 0.5s 5.0s forwards; /* Menambahkan fadeOut setelah delay */
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
</head>
<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-custom" role="alert">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>
<body class="bg-light">
        <!-- Tombol Kembali -->
        <a href="<?= site_url(relativePath: '/') ?>" class="position-absolute top-0 start-3 m-3" style="text-decoration: none; color: inherit;">
            <button class="btn btn-close"></button>
        </a>
<div class="container mt-5">

    <h3 class="mb-5">Informasi Transaksi</h3>

    <div class="row mt-4">
        <!-- Alamat Pengguna -->
        <div class="col-lg-12">
            <div class="bg-white p-3 rounded mb-4 shadow-sm">
                <h5><i class="bi bi-geo-alt alamat-icon"></i>Alamat Pengiriman</h5> <!-- Ikon Lokasi -->
                <p class="mb-0"><strong>dikirim ke : </strong><?= $pengguna['alamat'] ?></p>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Daftar Transaksi -->
        <div class="col-lg-12">
        <?php if (!empty($transaksiList)): ?>
        <?php foreach ($transaksiList as $item): ?>
            <div class="bg-white p-3 rounded mb-4 position-relative shadow-sm">
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
                    <?= ucfirst($item['transaksi']['status']) ?>
                </div>

                <h5>ID Transaksi : <?= $item['transaksi']['id_transaksi'] ?></h5>
                
                <h6>Daftar Produk:</h6>
                <?php foreach ($item['detailTransaksi'] as $detail): ?>
                    <div class="d-flex align-items-center bg-light p-3 rounded mb-3">
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
                <h5>Total Harga : <strong>Rp <?= number_format($item['totalTransaksi'], 0, ',', '.') ?></strong></h5>
                <?php if ($item['transaksi']['status'] == 'menunggu pembayaran'): ?>
                    <!-- Tombol Bayar -->
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#bayarModal" data-id="<?= $item['transaksi']['id_transaksi'] ?>" data-total="<?= $item['totalTransaksi'] ?>">Unggah bukti bayar</button>
                <?php endif; ?>

                <?php if ($item['transaksi']['status'] == 'dikirim'): ?>
                    <!-- Tombol Selesaikan Pesanan dengan aksi konfirmasi -->
                    <button class="btn btn-success mt-3" onclick="konfirmasiSelesaikanPesanan(<?= $item['transaksi']['id_transaksi'] ?>)">
                        Konfirmasi Pesanan Sampai
                    </button>
                <?php endif; ?>


                <!-- No Resi -->
                <div class="no-resi">
                    <i class="bi bi-truck"></i> : <?= $item['transaksi']['resi_pengiriman'] ?? '-' ?>
                </div>




            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Menampilkan pesan jika tidak ada produk yang ditemukan -->                       
        <div class="container text-center">
            <!-- Gambar dengan ukuran besar -->
            <img src="<?= base_url('Aset/shopping.png') ?>" class="img-fluid mb-4" style="max-width: 300px;">
            <!-- Pesan Keranjang Kosong -->
            <h1 class="display-6 font-weight-bold mb-3">
              Tidak ada Transaksi, Yu Belanja !
            </h1>
        </div>
    <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bayarModalLabel">Form Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?= site_url('/bayar/konfirmasiPembayaran') ?>" method="POST">
          <input type="hidden" name="id_transaksi" id="id_transaksi">
          <div class="mb-3">
            <h6>Transfer ke</h6>
            <p>BRI : 847584375834 <br>
               BNI : 98459475984 <br>
               <strong>A/N : ELECTROKO</strong>
            </p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <p class="form-label">Total Pembayaran Anda :</p>
                <p><strong>Rp <?= number_format($item['totalTransaksi'], 0, ',', '.') ?></strong></p>
            </div>
          </div>
          <div class="mb-3">
            <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran (link foto)</label>
            <input type="text" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Kirim bukti pembayaran</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script untuk mengisi data transaksi ke modal
    var bayarModal = document.getElementById('bayarModal');
    bayarModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Tombol Bayar yang diklik
        var idTransaksi = button.getAttribute('data-id');
        var totalTransaksi = button.getAttribute('data-total');

        // Mengisi nilai pada modal
        var idTransaksiInput = bayarModal.querySelector('#id_transaksi');
        var totalBayarInput = bayarModal.querySelector('#total_bayar');

        idTransaksiInput.value = idTransaksi;
        totalBayarInput.value = 'Rp ' + totalTransaksi.toLocaleString();
    });

    function konfirmasiSelesaikanPesanan(id_transaksi) {
        // Tampilkan alert konfirmasi
        if (confirm("Anda yakin ingin menyelesaikan pesanan? Pastikan produk yang diterima sesuai.")) {
            // Jika pengguna mengonfirmasi, kirimkan request ke server untuk mengubah status
            // Gunakan fetch API untuk mengirimkan request POST
            fetch("<?= site_url('/bayar/selesaikanPesanan') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    id_transaksi: id_transaksi
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Pesanan berhasil diselesaikan!");
                    location.reload(); // Reload halaman untuk memperbarui status
                } else {
                    alert("Gagal menyelesaikan pesanan. Coba lagi.");
                }
            })
            .catch(error => {
                alert("Terjadi kesalahan. Silakan coba lagi.");
            });
        }
    }
</script>
</body>
</html>
