<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Periksa apakah pengguna sudah login
        if (!$session->get('logged_in')) {
            return redirect()->to(site_url('login'))->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Periksa role jika diberikan dalam argument
        if ($arguments && !in_array($session->get('role'), $arguments)) {
            return redirect()->to(site_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tindakan setelah
    }
}
