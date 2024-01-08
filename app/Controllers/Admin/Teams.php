<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\TeamModel;

class Teams extends BaseController {
  public function index () {
    $teamModel = new TeamModel();
    $teams = $teamModel->findAll();

    return view('admin/teams/index', [
      'teams' => $teams
    ]);
  }

  public function create () {
    return view('admin/teams/form');
  }

  public function update (int $teamId) {
    $teamModel = new TeamModel();
    $team = $teamModel->find($teamId);

    if (!$team) {
      return redirect('admin/teams');
    }

    return view('admin/teams/form', [
      'team' => $team
    ]);
  }

  public function delete (int $teamId) {
    $teamModel = new TeamModel();
    $team = $teamModel->find($teamId);

    if (!$team) {
      return $this->response->setJSON(['success' => false, 'error' => 'Time não encontrado']);
    }

    $teamModel->delete($teamId);

    return $this->response->setJSON(['success' => true, 'teamId' => $teamId]);
  }

  public function save () {
    $validationRules = [
      'name' => 'required',
      'email' => 'required|valid_email',
      'password' => 'permit_empty',
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $team = [
      'id' => $this->request->getPost('id'),
      'name' => $this->request->getPost('name'),
      'email' => $this->request->getPost('email'),
      'password' => hashPassword($this->request->getPost('password'))
    ];

    $teamModel = new TeamModel();

    $imageFile = $this->request->getFile('image');

    if ($imageFile && $imageFile->isValid()) {
      $team['image'] = setFileName('perfil-' . $imageFile->getClientName());
      $imageFile->move('uploads/images/teams', $team['image']);

      if (!empty($team['id'])) {
        $previousTeam = $teamModel->find($team['id']);
        deleteFileFromFolder('uploads/images/teams/' . $previousTeam->image);
        
        $this->resizeProfileImage('uploads/images/teams/' . $team['image']);
      }
    }

    $teamModel->save($team);

    return $this->response->setJSON(['success' => true]);
  }

  private function resizeProfileImage (string $path) {
    $image = \Config\Services::image()
      ->withFile($path)
      ->resize(300, 300, true, 'height')
      ->save($path);
  }
}