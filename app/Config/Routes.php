<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */



$routes->get('/admin', 'AdminController::index', ['filter' => 'auth:admin']);
$routes->get('/', 'HomeController::index');
$routes->get('/login', 'LoginController::index');
$routes->post('/login/processLogin', 'LoginController::processLogin');
$routes->get('/logout', 'LoginController::logout');
$routes->get('/logoutadmin', 'AdminController::logout');
// $routes->get(from:'/daftar','DaftarController::index');

$routes->get('daftar', 'DaftarController::index'); // Menampilkan halaman pendaftaran
$routes->post('daftar/processDaftar', 'DaftarController::processDaftar'); // Proses pendaftaran


$routes->get('login', 'LoginController::index'); // Menampilkan halaman login

$routes->get('produk/tambah', 'ProdukController::tambah');
$routes->post('produk/tambah', 'ProdukController::tambah');

$routes->get('produk/edit/(:num)', 'ProdukController::edit/$1');
$routes->post('produk/edit/(:num)', 'ProdukController::edit/$1');

$routes->get('produk/hapus/(:num)', 'ProdukController::hapus/$1');

$routes->get('detail_produk/(:num)', 'DetailProdukController::detail/$1');

$routes->get('keranjang', 'KeranjangController::index');
$routes->post('keranjang/tambahKeranjang', 'KeranjangController::tambahKeranjang');
$routes->get('produk/(:num)', 'ProdukController::detail/$1');  // Menampilkan detail produk
$routes->get('keranjang/hapus/(:num)', 'KeranjangController::hapus/$1');
$routes->post('keranjang/updateJumlah', 'KeranjangController::updateJumlah');

$routes->get('keranjang/bayar', 'BayarController::index');
$routes->get('/keranjang/bayar', 'BayarController::index'); // Route untuk halaman bayar

$routes->get('/bayar', 'BayarController::index');

$routes->post('keranjang/checkout', 'KeranjangController::checkout');

$routes->get('/bayar', 'BayarController::index');
$routes->post('/bayar/konfirmasiPembayaran', 'BayarController::konfirmasiPembayaran');
$routes->post('/bayar/selesaikanPesanan', 'BayarController::selesaikanPesanan');

$routes->get('/admin', 'AdminController::index');

$routes->post('admin/updateResi/(:num)', 'AdminController::updateResi/$1');






































