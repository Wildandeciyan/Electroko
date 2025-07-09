<?php
namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\DetailTransaksiModel;
use App\Models\TransaksiModel;

class AdminController extends BaseController
{
    public function index()
    {
        $produkModel = new ProdukModel();
        $transaksiModel = new TransaksiModel();
        $detailTransaksiModel = new DetailTransaksiModel();
    
        $search = $this->request->getGet('search');
    
        // Filter produk berdasarkan pencarian
        $builder = $produkModel->builder();
        if ($search !== null && $search !== '') {
            $builder->like('nama_produk', $search);
        }
    
        // Ambil data produk
        $data['produk'] = $builder->get()->getResultArray();
    
        // Ambil semua transaksi dari pelanggan
        $transaksiList = $transaksiModel
            ->join('tabel_pengguna', 'tabel_pengguna.id = tabel_transaksi.id_pengguna')
            ->where('tabel_pengguna.role', 'pelanggan')
            ->where('tabel_transaksi.status !=', 'menunggu pembayaran') // Filter status
            ->select('tabel_transaksi.*, tabel_pengguna.id AS id_pelanggan, tabel_pengguna.username AS nama_pelanggan,tabel_pengguna.alamat') // Memastikan data pengguna ditambahkan
            ->findAll();

    
    
    
        $transaksiData = [];
    
        foreach ($transaksiList as $transaksi) {
            // Ambil detail transaksi (produk yang dibeli)
            $detailTransaksi = $detailTransaksiModel
                ->join('tabel_produk', 'tabel_produk.id_produk = tabel_detail_transaksi.id_produk')
                ->where('id_transaksi', $transaksi['id_transaksi'])
                ->findAll();
    
            // Hitung total harga transaksi
            $totalHarga = 0;
            foreach ($detailTransaksi as $item) {
                $totalHarga += $item['harga'] * $item['jumlah'];
            }
    
            // Gabungkan transaksi, detail, dan total harga
            $transaksiData[] = [
                'transaksi' => $transaksi,
                'detail' => $detailTransaksi,
                'totalHarga' => $totalHarga,
            ];
        }
    
        // Kirim data produk dan transaksi ke view
        $data['transaksi'] = $transaksiData;
    
        return view('admin', $data);  // Menampilkan produk dan transaksi pada halaman admin
    }
    public function updateResi($id_transaksi)
    {
        $resi = $this->request->getPost('resi');
        $transaksiModel = new TransaksiModel();
        
        // Cek apakah resi kosong
        if (!$resi) {
            return redirect()->to(site_url('admin'))->with('error', 'Nomor Resi tidak boleh kosong!');
        }
        
        // Update nomor resi dan status menjadi 'dikirim' pada transaksi
        $transaksiModel->update($id_transaksi, [
            'resi_pengiriman' => $resi, // Insert nomor resi
            'status' => 'dikirim'       // Update status menjadi 'dikirim'
        ]);
        
        // Redirect dengan pesan sukses
        return redirect()->to(site_url('admin') . '#kelola-pesanan')->with('message', 'Resi Berhasil Di Update!');
    }
    
    

    

    public function logoutadmin()
    {
        session()->destroy();
        return redirect()->to(site_url('/'));
    }
}
