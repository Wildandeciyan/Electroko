<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use CodeIgniter\Controller;

class ProdukController extends Controller
{
    public function tambah()
    {
        // Ambil data kategori untuk dropdown
        $kategoriModel = new KategoriModel();
        $data['kategori'] = $kategoriModel->findAll();
    
        // Ambil data dari form
        $produkData = [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga' => $this->request->getPost('harga'),
            'stok' => $this->request->getPost('stok'),
            'kategori' => intval($this->request->getPost('kategoriT')), // Mengonversi ke integer
            'gambar' => $this->request->getPost('gambar') // Pastikan gambar diinputkan dengan benar
        ];
    
        // Validasi dan simpan produk
        if ($this->validate([
            'nama_produk' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'kategoriT' => 'required|numeric',
            'gambar' => 'required'
        ])) {
            // Simpan produk ke database
            $produkModel = new ProdukModel();
            if ($produkModel->save($produkData)) {
                return redirect()->to(site_url('admin'))->with('success', 'Produk Berhasil Ditambahkan');
            }
            else {
                // Jika gagal, tampilkan pesan error
                session()->setFlashdata('error', 'Terjadi kesalahan saat menambahkan produk.');
            }
        }
    
        // Tampilkan form tambah produk (dengan data kategori)
        return view('tambah', ['kategori' => $data['kategori']]);
    }
    
    public function hapus($id_produk)
    {
        $produkModel = new ProdukModel();

        // Cek apakah produk ada
        $produk = $produkModel->find($id_produk);
        if ($produk) {
            // Hapus produk berdasarkan ID
            if ($produkModel->delete($id_produk)) {
                return redirect()->to(site_url('admin'))->with('success', 'Produk berhasil dihapus.');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus produk.');
            }
        } else {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }
    }

    public function edit($id)
    {
        $produkModel = new ProdukModel();
        $kategoriModel = new KategoriModel();
    
        // Ambil data produk berdasarkan ID
        $produk = $produkModel->find($id);
    
        // Jika produk tidak ditemukan
        if (!$produk) {
            return redirect()->to(site_url('produk'))->with('error', 'Produk tidak ditemukan');
        }
    
        // Ambil semua kategori untuk dropdown
        $data['kategori'] = $kategoriModel->findAll();
        $data['produk'] = $produk;  // Menambahkan data produk ke view
    
        // Validasi input dan proses update jika form disubmit
        if ($this->request->getPost()) {
            // Validasi input
            if (!$this->validate([
                'nama_produk' => 'required',
                'deskripsi' => 'required',
                'harga' => 'required|numeric',
                'stok' => 'required|numeric',
                'kategori' => 'required',
                'gambar' => 'required',
            ])) {
                return redirect()->back()->withInput()->with('error', 'Pastikan semua data valid!');
            }
    
            // Ambil data dari form setelah validasi
            $data_produk = [
                'nama_produk' => $this->request->getPost('nama_produk'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'harga' => $this->request->getPost('harga'),
                'stok' => $this->request->getPost('stok'),
                'kategori' => $this->request->getPost('kategori'),
                'gambar' => $this->request->getPost('gambar'),
            ];
    
            // Update data produk
            if ($produkModel->update($id, $data_produk)) {
                log_message('debug', 'Produk berhasil diperbarui');
                return redirect()->to(site_url('admin'))->with('success', 'Produk berhasil diperbarui');
            } else {
                log_message('debug', 'Gagal memperbarui produk');
                return redirect()->back()->with('error', 'Gagal memperbarui produk');
            }
        }
    
        // Tampilkan form edit dengan data produk
        return view('edit', $data);
    }
    
}
