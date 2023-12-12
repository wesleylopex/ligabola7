<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\TeamModel;

class Login extends BaseController {
  public function index (): string {
    return view('manager/login/index');
  }

  public function attempt () {
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $teamModel = new TeamModel();

    $account = $teamModel->where('email', $email)->first();

    if (!$account) {
      return redirect()->back()->withInput()->with('error', 'Email not found');
    }

    if (!password_verify($password, $account->password)) {
      return redirect()->back()->withInput()->with('error', 'Password is incorrect');
    }

    session()->set('teamId', $account->id);

    return $this->response->setJSON(['success' => true]);
  }

  public function logout () {
    session()->remove('teamId');

    return redirect()->to('/manager/login');
  }
}
