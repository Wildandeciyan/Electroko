<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table      = 'tabel_produk'; // Nama tabel di database
    protected $primaryKey = 'id_produk';   // Nama kolom primary key
    protected $allowedFields = ['nama_produk', 'deskripsi', 'harga', 'stok','kategori','gambar']; // Kolom yang dapat diisi
}
