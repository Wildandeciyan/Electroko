<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'tabel_transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $allowedFields = ['id_pengguna','total_harga','status','bukti_bayar','resi_pengiriman','created_at'];
}
