<?php
namespace App\Models;


use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table = 'tabel_detail_transaksi';
    protected $primaryKey = 'id_detail';
    protected $allowedFields = ['id_transaksi', 'id_produk', 'jumlah', 'harga'];
}
