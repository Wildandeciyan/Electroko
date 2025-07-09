<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\DetailTransaksiModel;
use App\Models\PenggunaModel;
use App\Models\TransaksiModel;
use App\Models\KategoriModel;
use CodeIgniter\API\ResponseTrait;

class BayarController extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        // Ambil ID pengguna dari session
        $idPengguna = session('id');
        if (!$idPengguna) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil data pengguna
        $penggunaModel = new PenggunaModel();
        $data['pengguna'] = $penggunaModel->find($idPengguna);

        // Validasi apakah data pengguna ditemukan
        if (empty($data['pengguna'])) {
            return redirect()->to('/keranjang')->with('error', 'Data pengguna tidak ditemukan.');
        }

        // Ambil semua transaksi yang statusnya "menunggu pembayaran"
        $transaksiModel = new TransaksiModel();
        $transaksiList = $transaksiModel->where('id_pengguna', $idPengguna)
                                        ->findAll();

        // Ambil detail transaksi untuk setiap transaksi yang ditemukan
        $detailTransaksiModel = new DetailTransaksiModel();
        $allDetailTransaksi = [];
        $totalHarga = 0;

        foreach ($transaksiList as $transaksi) {
            // Ambil detail produk untuk masing-masing transaksi
            $detailTransaksi = $detailTransaksiModel->select('tabel_detail_transaksi.*, tabel_produk.nama_produk, tabel_produk.harga, tabel_produk.gambar, tabel_kategori.nama_kategori')
                ->join('tabel_produk', 'tabel_produk.id_produk = tabel_detail_transaksi.id_produk')
                ->join('tabel_kategori', 'tabel_kategori.id_kategori = tabel_produk.kategori')
                ->where('tabel_detail_transaksi.id_transaksi', $transaksi['id_transaksi'])
                ->findAll();

            // Hitung total harga untuk transaksi ini
            $totalTransaksi = 0;
            foreach ($detailTransaksi as $item) {
                $totalTransaksi += $item['harga'] * $item['jumlah'];
            }

            // Menambahkan detail transaksi dan total harga transaksi
            $allDetailTransaksi[] = [
                'transaksi' => $transaksi,
                'detailTransaksi' => $detailTransaksi,
                'totalTransaksi' => $totalTransaksi,
            ];
            $totalHarga += $totalTransaksi;
        }

        // Kirim data ke view
        $data['transaksiList'] = $allDetailTransaksi;
        $data['totalHarga'] = $totalHarga;

        return view('bayar', $data);
    }

    public function konfirmasiPembayaran()
    {
        $transaksiModel = new TransaksiModel();

        // Ambil data dari form yang dikirimkan
        $id_transaksi = $this->request->getPost('id_transaksi');
        $bukti_pembayaran = $this->request->getPost('bukti_pembayaran');

        // Update data transaksi dengan informasi pembayaran
        $data = [
            'bukti_bayar' => $bukti_pembayaran,
            'status' => 'diproses' // Mengubah status menjadi "menunggu verifikasi"
        ];

        // Update transaksi berdasarkan id
        $transaksiModel->update($id_transaksi, $data);

        // Redirect atau tampilkan pesan konfirmasi
        return redirect()->to('/bayar')->with('message', 'Terimakasih telah berbelanja, Kami akan segera mengirim pesanan Anda!');
    }
    public function selesaikanPesanan()
    {
        // Ambil data yang dikirimkan dari request
        $data = $this->request->getJSON();
        $id_transaksi = $data->id_transaksi;

        // Update status transaksi menjadi 'selesai'
        $transaksiModel = new TransaksiModel();
        $updateData = [
            'status' => 'selesai'
        ];

        if ($transaksiModel->update($id_transaksi, $updateData)) {
            return $this->respond(['success' => true], 200);
        } else {
            return $this->respond(['success' => false], 400);
        }
    }
}
