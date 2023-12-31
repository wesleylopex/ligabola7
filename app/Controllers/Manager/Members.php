<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

use App\Models\MemberModel;
use App\Models\MemberTeamDivisionModel;

class Members extends BaseController {
  public function create (): string {
    return view('manager/members/form');
  }

  public function save () {
    $validationRules = [
      'name' => 'required',
      'birth_date' => 'required',
      'cpf' => 'required',
      'rg' => 'permit_empty',
      'role' => 'required|in_list[athlete,coach,president,assistant]'
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $memberTeamDivisionModel = new MemberTeamDivisionModel();

    $limitExceed = $memberTeamDivisionModel->where([
      'role' => 'athlete',
      'team_division_id' => $this->currentTeamDivision->id,
      'status' => 'approved'
    ])->countAllResults() >= 23;

    $memberRole = $this->request->getPost('role');

    if ($memberRole === 'athlete' && $limitExceed) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Limite de atletas excedido'
      ]);
    }

    $memberModel = new MemberModel();

    $rg = $this->request->getPost('rg');

    $member = [
      'name' => $this->request->getPost('name'),
      'birth_date' => $this->request->getPost('birth_date'),
      'cpf' => $this->request->getPost('cpf'),
      'rg' => empty($rg) ? null : $rg
    ];

    $memberAlreadyExists = $memberModel->where('cpf', $member['cpf'])
      ->first();

    if ($memberAlreadyExists) {
      $member['id'] = $memberAlreadyExists->id;
    }

    $success = $memberModel->save($member);

    if (!$success) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Erro ao salvar membro'
      ]);
    }

    $memberId = array_key_exists('id', $member) ? $member['id'] : $memberModel->getInsertID();

    $memberTeamDivision = [
      'member_id' => $memberId,
      'team_division_id' => $this->currentTeamDivision->id,
      'status' => 'pending',
      'role' => $memberRole
    ];

    $success = $memberTeamDivisionModel->save($memberTeamDivision);

    return $this->response->setJSON(['success' => true]);
  }

  public function find () {
    $cpf = $this->request->getGet('cpf');

    $memberModel = new MemberModel();
    $member = $memberModel->where('cpf', $cpf)
      ->first();

    if (!$member) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Membro não encontrado'
      ]);
    }

    return $this->response->setJSON([
      'success' => true,
      'member' => $member
    ]);
  }
}
