<?php

namespace App\Controllers;

use App\Models\PenggunaModel;
use CodeIgniter\Controller;

class LoginController extends Controller
{
    public function index()
    {
        $penggunaModel = new PenggunaModel();
        $roles = $penggunaModel->distinct()->select('role')->findAll();
        return view('login', ['roles' => $roles]);
    }

    public function processLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $role = $this->request->getPost('role');
    
        $penggunaModel = new PenggunaModel();
        $pengguna = $penggunaModel->where('username', $username)->first();
    
        if ($pengguna && $password == $pengguna['password']) { // Ganti dengan password_verify jika hash
            if ($pengguna['role'] == $role) {
                // Simpan data ke session
                $session = session();
                $session->set([
                    'logged_in' => true,
                    'username' => $pengguna['username'],
                    'role' => $pengguna['role'],
                    'foto' => $pengguna['foto'],
                    'id' => $pengguna['id']
                ]);
    
                // Redirect berdasarkan role
                if ($role === 'admin') {
                    log_message('debug', 'Redirecting to admin page. Role: admin, User: ' . $pengguna['username']);
                    return redirect()->to('/admin');
                } elseif ($role === 'pelanggan') {
                    log_message('debug', 'Redirecting to home page. Role: pelanggan, User: ' . $pengguna['username']);
                    return redirect()->to(site_url('/'));
                }
            } else {
                log_message('error', 'Role mismatch for user: ' . $pengguna['username']);
                return redirect()->back()->with('error', 'Role tidak sesuai.')->withInput();
            }
        } else {
            log_message('error', 'Invalid username or password for username: ' . $username);
            return redirect()->back()->with('error', 'Username atau password salah.')->withInput();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('/'));
    }
}
