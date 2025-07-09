<?php

namespace App\Controllers;

use App\Models\PenggunaModel;
use CodeIgniter\Controller;

class DaftarController extends Controller
{
    public function index()
    {
        return view('daftar'); // Tampilan form pendaftaran
    }

    public function processDaftar()
    {
        // Ambil data inputan form
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $alamat = $this->request->getPost('alamat');
        $nomor_telepon = $this->request->getPost('nomor_telepon');
        $foto = $this->request->getPost('foto');

        // Validasi input
        if (!$this->validate([
            'username' => 'required|is_unique[tabel_pengguna.username]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Username Sudah Digunakan!');
        }
        if (!$this->validate([
            'nomor_telepon' => 'required|numeric', // Validasi nomor telepon
        ])) {
            return redirect()->back()->withInput()->with('error', 'No Telepon harus berupa Angka!');
        }
        if (!$this->validate([
            'username' => 'required|is_unique[tabel_pengguna.username]',
            'password' => 'required',
            'alamat' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Pastikan semua Data Valid!');
        }

        // Persiapkan data pengguna
        $data = [
            'username' => $username,
            'password' => $password,
            'alamat' => $alamat,
            'nomor_telepon' => $nomor_telepon,
            'role' => 'pelanggan', // Role otomatis 'pelanggan'
            'foto' => $foto
        ];

        // Masukkan data ke dalam tabel_pengguna
        $penggunaModel = new PenggunaModel();
        
        if ($penggunaModel->insert($data)) { // Gunakan insert() untuk menambahkan data
            // Jika berhasil, redirect ke halaman login
            return redirect()->to(site_url('login'))->with('success', 'Pendaftaran berhasil, silakan login');
        } else {
            // Jika gagal, kirimkan pesan error
            return redirect()->to(site_url('daftar'))->with('error', 'Pendaftaran gagal, coba lagi');
        }
    }
}
