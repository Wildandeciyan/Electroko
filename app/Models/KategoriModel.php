<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table      = 'tabel_kategori';  // Nama tabel kategori
    protected $primaryKey = 'id_kategori';     // Primary key tabel kategori

    protected $allowedFields = ['nama_kategori'];  // Nama kolom yang boleh diubah

    // Optionally, you can add validation rules or other properties if needed.
}
