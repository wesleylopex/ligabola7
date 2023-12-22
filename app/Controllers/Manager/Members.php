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

    $memberModel = new MemberModel();

    $subscriptionNumber = $this->request->getPost('subscription_number');

    $member = [
      'subscription_number' => empty($subscriptionNumber) ? null : $subscriptionNumber,
      'name' => $this->request->getPost('name'),
      'birth_date' => $this->request->getPost('birth_date'),
      'cpf' => $this->request->getPost('cpf'),
      'rg' => $this->request->getPost('rg')
    ];

    $success = $memberModel->save($member);

    if (!$success) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Erro ao salvar membro'
      ]);
    }

    $memberId = $memberModel->getInsertID();

    $memberTeamDivision = [
      'member_id' => $memberId,
      'team_division_id' => 1,
      'status' => 'pending',
      'role' => $this->request->getPost('role')
    ];

    $memberTeamDivisionModel = new MemberTeamDivisionModel();
    $success = $memberTeamDivisionModel->save($memberTeamDivision);

    return $this->response->setJSON(['success' => true]);
  }

  public function find () {
    $query = $this->request->getGet('query');

    $memberModel = new MemberModel();
    $member = $memberModel->where('cpf', $query)
      ->orWhere('subscription_number', $query)
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
