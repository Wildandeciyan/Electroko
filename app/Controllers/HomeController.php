<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\KeranjangModel;  // Pastikan Anda sudah import model KeranjangModel

class HomeController extends BaseController
{
    public function index()
    {
        $produkModel = new ProdukModel();
        $kategoriModel = new KategoriModel();
        $keranjangModel = new KeranjangModel();  // Model untuk keranjang

        // Ambil semua kategori
        $data['kategori'] = $kategoriModel->findAll();

        // Ambil filter dan pencarian dari URL
        $min_harga = $this->request->getGet('min_harga');
        $max_harga = $this->request->getGet('max_harga');
        $kategori_id = $this->request->getGet('kategori');
        $search = $this->request->getGet('search');

        // Buat query untuk filter produk
        $builder = $produkModel->builder();

        // Filter berdasarkan harga
        if ($min_harga !== null && $min_harga !== '') {
            $builder->where('harga >=', $min_harga);
        }

        if ($max_harga !== null && $max_harga !== '') {
            $builder->where('harga <=', $max_harga);
        }

        // Filter berdasarkan kategori
        if ($kategori_id !== null && $kategori_id !== '') {
            $builder->where('kategori', $kategori_id);
        }

        // Filter pencarian berdasarkan nama produk
        if ($search !== null && $search !== '') {
            $builder->like('nama_produk', $search);
        }

        // Ambil data produk sesuai filter dan pencarian
        $data['produk'] = $builder->get()->getResultArray();

        // Menambahkan jumlah item keranjang yang ada untuk pengguna yang login
        $user_id = session()->get('id');  // ID pengguna yang sedang login

        if ($user_id) {
            // Mengambil jumlah item di keranjang pengguna
            $jumlahItemKeranjang = $keranjangModel->where('id_pengguna', $user_id)->countAllResults();
            $data['jumlahItemKeranjang'] = $jumlahItemKeranjang;
        } else {
            $data['jumlahItemKeranjang'] = 0;
        }

        return view('home', $data);  // Menampilkan produk pada halaman home
    }
}
