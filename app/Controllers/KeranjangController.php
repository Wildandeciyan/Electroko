<?php

namespace App\Controllers;

use App\Models\KeranjangModel;
use App\Models\ProdukModel;

class KeranjangController extends BaseController
{
    // Menampilkan halaman keranjang
    public function index()
    {
        // Mendapatkan data keranjang pengguna yang sedang login
        $keranjangModel = new KeranjangModel();
        $produkModel = new ProdukModel();
        $keranjang = $keranjangModel->getKeranjangByUser(session()->get('id'));

        // Mengirim data keranjang ke view
        return view('keranjang', ['keranjang' => $keranjang, 'produkModel' => $produkModel]);
    }
    public function TambahKeranjang()
    {
        $produk_id = $this->request->getPost('produk_id');
        $user_id = session()->get('id'); // ID pengguna yang sedang login
        
        // Pastikan ID pengguna tersedia
        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengguna tidak ditemukan.']);
        }
        
        // Ambil data produk dari tabel produk
        $produkModel = new \App\Models\ProdukModel();
        $produk = $produkModel->find($produk_id);
    
        // Pastikan produk ada
        if (!$produk) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produk tidak ditemukan.']);
        }
    
        // Cek apakah produk sudah ada di keranjang
        $keranjangModel = new \App\Models\KeranjangModel();
        $existingItem = $keranjangModel->select('tabel_keranjang.*, tabel_produk.nama_produk, tabel_produk.harga')
                                       ->join('tabel_produk', 'tabel_produk.id_produk = tabel_keranjang.id_produk')
                                       ->where('tabel_keranjang.id_produk', $produk_id)
                                       ->first();
    
        if ($existingItem) {
            // Jika produk sudah ada di keranjang, update jumlahnya
            $keranjangModel->update($existingItem['id_keranjang'], [
                'jumlah' => $existingItem['jumlah'] + 1
            ]);
        } else {
            // Jika produk belum ada di keranjang, tambahkan produk ke keranjang
            $keranjangModel->save([
                'id_pengguna' => $user_id,  // ID pengguna
                'id_produk' => $produk_id,  // ID produk
                'jumlah' => 1               // Jumlah produk di keranjang
            ]);
        }

        session()->setFlashdata('message', 'Produk berhasil ditambahkan ke keranjang!');
        return redirect()->to(site_url("detail_produk/{$produk_id}"));
        
    }
    

    public function hapus($id_keranjang)
    {
        $user_id = session()->get('id'); // ID pengguna yang sedang login
    
        // Pastikan ID pengguna tersedia
        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengguna tidak ditemukan.']);
        }
    
        // Ambil model Keranjang dan Produk
        $keranjangModel = new KeranjangModel();
        $produkModel = new \App\Models\ProdukModel();
    
        // Cek apakah item ada di keranjang milik pengguna
        $keranjangItem = $keranjangModel->where('id_keranjang', $id_keranjang)
                                        ->where('id_pengguna', $user_id)
                                        ->first();
    
        if (!$keranjangItem) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item keranjang tidak ditemukan.']);
        }
    
        // Ambil data produk terkait dengan item yang dihapus
        $produk = $produkModel->find($keranjangItem['id_produk']);
    
        if (!$produk) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produk tidak ditemukan.']);
        }
    
        // Tambahkan stok produk sesuai jumlah yang dihapus
        $produkModel->update($keranjangItem['id_produk'], [
            'stok' => $produk['stok'] + $keranjangItem['jumlah']
        ]);
    
        // Hapus item dari keranjang
        $keranjangModel->delete($id_keranjang);
    
        // Redirect kembali ke halaman keranjang setelah berhasil menghapus
        return redirect()->to(site_url('keranjang'))->with('message', 'Produk berhasil dihapus dari keranjang.');
    }
    public function updateJumlah()
    {
        $id_keranjang = $this->request->getPost('id_keranjang');
        $jumlah = $this->request->getPost('jumlah');
        
        $keranjangModel = new \App\Models\KeranjangModel();
        $keranjangModel->update($id_keranjang, ['jumlah' => $jumlah]);
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function checkout()
    {
        // Cek data produk yang diterima
        $data = $this->request->getJSON(); // Mendapatkan data JSON yang dikirim via AJAX
    
        // Debugging: Cek apakah data produk valid
        // log_message('debug', 'Data produk yang diterima: ' . print_r($data, true));
    
        if (empty($data->produk)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada produk yang dipilih.']);
        }
    
        $produkDipilih = $data->produk;
        $idPengguna = session('id'); // Ambil id pengguna dari session
    
        if (empty($produkDipilih) || !$idPengguna) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Data tidak valid atau pengguna tidak terautentikasi']);
        }
    
        $transaksiModel = new \App\Models\TransaksiModel();
        $detailTransaksiModel = new \App\Models\DetailTransaksiModel();
        $keranjangModel = new \App\Models\KeranjangModel();
        $produkModel = new \App\Models\ProdukModel();
    
        // Hitung total harga
        $totalHarga = 0;
        foreach ($produkDipilih as $item) {
            $idKeranjang = $item->id_keranjang; // gunakan object notation, karena data datang dalam format objek
            $jumlah = $item->jumlah;
    
            // Ambil data keranjang dan produk
            $keranjang = $keranjangModel->find($idKeranjang);
            if (!$keranjang) {
                // log_message('error', 'Keranjang tidak ditemukan dengan ID: ' . $idKeranjang);
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Produk dalam keranjang tidak ditemukan']);
            }
    
            $produk = $produkModel->find($keranjang['id_produk']);  // Mengakses id_produk dengan array notation
            if (!$produk) {
                // log_message('error', 'Produk tidak ditemukan dengan ID: ' . $keranjang->id_produk);
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Produk tidak valid']);
            }
    
            $totalHarga += $produk['harga'] * $jumlah;  // Akses harga dengan array notation
            // Akses harga dengan -> (bukan [])
        }
    
        // Simpan transaksi
        $idTransaksi = $transaksiModel->insert([
            'id_pengguna' => $idPengguna,
            'total_harga' => $totalHarga,
            'status' => 'menunggu pembayaran', // Status awal
            'bukti_bayar' => null, // Belum ada bukti bayar
            'resi_pengiriman' => null, // Belum ada resi pengiriman
            'created_at' => date('Y-m-d H:i:s'), // Waktu transaksi dibuat
        ]);
    
        // Cek apakah transaksi berhasil disimpan
        if (!$idTransaksi) {
            // log_message('error', 'Gagal menyimpan transaksi');
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan transaksi']);
        }
    
        // Simpan detail transaksi dan hapus dari keranjang
        foreach ($produkDipilih as $item) {
            $idKeranjang = $item->id_keranjang; // gunakan object notation, karena data datang dalam format objek
            $jumlah = $item->jumlah;
            
            // Ambil data keranjang dan produk
            $keranjang = $keranjangModel->find($idKeranjang);
            if (!$keranjang) {
                // log_message('error', 'Keranjang tidak ditemukan dengan ID: ' . $idKeranjang);
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Produk dalam keranjang tidak ditemukan']);
            }
            
            $produk = $produkModel->find($keranjang['id_produk']);  // Mengakses id_produk dengan objek notation
            if (!$produk) {
                // log_message('error', 'Produk tidak ditemukan dengan ID: ' . $keranjang->id_produk);
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Produk tidak valid']);
            }
            
            // Simpan detail transaksi
            $detailTransaksiModel->insert([
                'id_transaksi' => $idTransaksi, // ID transaksi yang baru saja disimpan
                'id_produk' => $produk['id_produk'],  // Akses dengan -> bukan []
                'jumlah' => $jumlah,
                'harga' => $produk["harga"],
            ]);
            
            // Hapus dari keranjang setelah checkout
            $keranjangModel->delete($idKeranjang);

         // Kurangi stok produk di tabel_produk
         $produkModel->update($keranjang['id_produk'], [
            'stok' => $produk['stok'] - 1
         ]);            
         }
        
    
        // Kirimkan respons sukses ke AJAX
        return $this->response->setJSON(['message' => 'Transaksi berhasil']);
        
    }
     
}
