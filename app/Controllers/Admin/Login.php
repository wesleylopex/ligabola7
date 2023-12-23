<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;

class Login extends BaseController {
  public function index () {
    return view('admin/login/index');
  }

  public function attempt () {
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $adminUserModel = new AdminUserModel();

    $account = $adminUserModel->where('email', $email)->first();

    if (!$account) {
      return $this->response->setJSON(['success' => false, 'error' => 'E-mail não encontrado']);
    }

    if (!password_verify($password, $account->password)) {
      return $this->response->setJSON(['success' => false, 'error' => 'Senha incorreta']);
    }

    session()->set('adminId', $account->id);

    return $this->response->setJSON(['success' => true]);
  }

  public function logout () {
    session()->remove('adminId');

    return redirect()->to('/admin/login');
  }
}