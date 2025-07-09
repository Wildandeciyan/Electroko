<?php

namespace App\Models;

use CodeIgniter\Model;

class KeranjangModel extends Model
{
    protected $table = 'tabel_keranjang';
    protected $primaryKey = 'id_keranjang';
    protected $allowedFields = ['id_produk', 'id_pengguna', 'jumlah'];

    // Mendapatkan keranjang berdasarkan id pengguna
    public function getKeranjangByUser($id_pengguna)
    {
        return $this->where('id_pengguna', $id_pengguna)->findAll();
    }

    // Mendapatkan produk berdasarkan ID produk
    public function getProdukById($id_produk)
    {
        // Asumsi Anda memiliki tabel produk di database
        $produkModel = new ProdukModel();
        return $produkModel->find($id_produk);
    }
}
