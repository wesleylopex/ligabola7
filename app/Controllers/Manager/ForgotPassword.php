<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\TeamModel;

class ForgotPassword extends BaseController {
  public function index (): string {
    return view('manager/forgot-password/index');
  }

  public function reset () {
    $code = $this->request->getGet('code');

    if (!$code) {
      return redirect()->to('manager/login');
    }

    $teamModel = new TeamModel();
    $team = $teamModel->where('reset_code', $code)->first();

    if (!$team) {
      return redirect()->to('manager/login');
    }

    return view('manager/forgot-password/reset', [
      'code' => $code
    ]);
  }

  public function save () {
    $validationRules = [
      'password' => 'required|min_length[8]|max_length[255]',
      'password_confirmation' => 'required|matches[password]',
      'code' => 'required'
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors()
      ])->setStatusCode(400);
    }

    $code = $this->request->getPost('code');

    $teamModel = new TeamModel();
    $team = $teamModel->where('reset_code', $code)->first();

    if (!$team) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Código inválido'
      ])->setStatusCode(400);
    }

    $teamModel->update($team->id, [
      'password' => hashPassword($this->request->getPost('password')),
      'reset_code' => null
    ]);

    return $this->response->setJSON(['success' => true])->setStatusCode(200);
  }

  public function handle () {
    $email = $this->request->getPost('email');

    $teamModel = new TeamModel();
    $team = $teamModel->where('email', $email)->first();

    if (!$team) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Se o email estiver cadastrado, você receberá o link para redefinição'
      ])->setStatusCode(400);
    }

    // code with 12 random chars, a-z, A-Z, 0-9
    $randomCode = bin2hex(random_bytes(6));
    
    $emailSent = $this->sendEmail($team->email, $randomCode);

    if (!$emailSent) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Erro ao enviar e-mail'
      ])->setStatusCode(500);
    }

    $teamModel->update($team->id, [
      'reset_code' => $randomCode
    ]);

    return $this->response->setJSON(['success' => true])->setStatusCode(200);
  }

  private function sendEmail (string $email, string $randomCode): bool {
    $url = base_url('manager/forgot-password/reset?code=' . $randomCode);

    $message = view('manager/forgot-password/email', [
      'url' => $url
    ]);

    $emailService = new \CodeIgniter\Email\Email();
    $emailService->setFrom('noreply@ligabola7.com.br', 'Liga Bola 7');
    $emailService->setTo($email);
    $emailService->setSubject('Recuperação de senha');
    $emailService->setMailType('html');
    $emailService->setMessage($message);

    return $emailService->send();
  }
}
