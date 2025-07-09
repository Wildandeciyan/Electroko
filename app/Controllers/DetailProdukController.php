<?php
namespace App\Controllers;

use App\Models\ProdukModel;

class DetailProdukController extends BaseController

{
    public function detail($id)
    {
        $produkModel = new ProdukModel();

        // Ambil data produk berdasarkan ID
        $produk = $produkModel->find($id);

        // Jika produk tidak ditemukan, redirect dengan pesan error
        if (!$produk) {
            return redirect()->to(site_url('produk'))->with('error', 'Produk tidak ditemukan');
        }

        // Kirim data produk ke view
        return view('detail_produk', ['produk' => $produk]);
    }
}