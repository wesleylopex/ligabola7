<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\TeamModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Throwable;

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

    return $this->response->setJSON([
      'success' => true,
      'message' => 'Senha redefinida com sucesso'
    ])->setStatusCode(200);
  }

  public function handle () {
    $email = $this->request->getPost('email');

    $teamModel = new TeamModel();
    $team = $teamModel->where('email', $email)->first();

    if (!$team) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Email não encontrado'
      ])->setStatusCode(404);
    }

    // code with 12 random chars, a-z, A-Z, 0-9
    $randomCode = bin2hex(random_bytes(6));

    $sendEmailResult = $this->sendEmail($team->email, $randomCode);

    if (!$sendEmailResult['success']) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $sendEmailResult['error']
      ])->setStatusCode(500);
    }

    $teamModel->update($team->id, [
      'reset_code' => $randomCode
    ]);

    return $this->response->setJSON([
      'success' => true,
      'message' => 'E-mail enviado com sucesso'
    ])->setStatusCode(200);
  }

  private function sendEmail (string $email, string $randomCode): array {
    try {
      $emailConfig = config('Email');

      $mailer = new PHPMailer(true);
      $mailer->isSMTP();
      $mailer->Host       = $emailConfig->SMTPHost;
      $mailer->SMTPAuth   = true;
      $mailer->Username   = $emailConfig->SMTPUser;
      $mailer->Password   = $emailConfig->SMTPPass;
      $mailer->SMTPSecure = $emailConfig->SMTPCrypto;
      $mailer->Port       = $emailConfig->SMTPPort;
      $mailer->Timeout    = $emailConfig->SMTPTimeout;
      $mailer->CharSet    = $emailConfig->charset;

      $mailer->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
      $mailer->addAddress($email);
      $mailer->isHTML(true);
      $mailer->Subject = 'Recuperação de senha';
      $mailer->Body    = view('manager/forgot-password/email', [
        'url' => base_url('manager/forgot-password/reset?code=' . $randomCode)
      ]);

      $mailer->send();

      return ['success' => true, 'error' => null];
    } catch (PHPMailerException $exception) {
      return ['success' => false, 'error' => $exception->getMessage()];
    } catch (Throwable $exception) {
      return ['success' => false, 'error' => 'Erro crítico ao enviar e-mail: ' . $exception->getMessage()];
    }
  }
}
