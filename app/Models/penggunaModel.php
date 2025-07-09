<?php
namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table = 'tabel_pengguna';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'alamat', 'nomor_telepon', 'role','foto'];
    
}

