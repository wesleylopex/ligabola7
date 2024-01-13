<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\TeamDivisionModel;
use App\Models\MemberTeamDivisionModel;
use App\Models\MemberModel;

class Members extends BaseController {
  public function index () {
    $memberModel = new MemberModel();
    $members = $memberModel->findAll();

    return view('admin/members/index', [
      'members' => $members
    ]);
  }

  public function create () {
    return view('admin/members/form');
  }

  public function delete (int $memberId) {
    $memberModel = new MemberModel();
    $memberModel->delete($memberId);

    return redirect()->back();
  }

  public function update (int $memberId) {
    $memberModel = new MemberModel();
    $member = $memberModel->where('id', $memberId)->first();

    if (!$member) {
      return redirect()->back();
    }

    return view('admin/members/form', [
      'member' => $member
    ]);
  }

  public function save () {
    $id = $this->request->getPost('id');

    $validationRules = [
      'id' => 'trim|permit_empty|is_natural_no_zero',
      'name' => 'trim|required|max_length[255]',
      'subscription_number' => 'trim|max_length[255]|is_unique[members.subscription_number,id,'. $id .']',
      'cpf' => 'trim|required|max_length[255]|is_unique[members.cpf,id,'. $id .']',
      'rg' => 'trim|permit_empty|max_length[255]|is_unique[members.rg,id,'. $id .']',
      'birth_date' => 'trim|max_length[255]',
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    // if (!empty($subscriptionNumber)) {
    //   $memberModel = new MemberModel();

    //   $subscriptionNumberAlreadyExists = $memberModel->where([
    //     'subscription_number' => $subscriptionNumber,
    //     'id !=' => $memberId
    //   ])->first();

    //   if ($subscriptionNumberAlreadyExists) {
    //     return $this->response->setJSON([
    //       'success' => false,
    //       'error' => 'Já existe um atleta com esse número de inscrição'
    //     ]);
    //   }
    // }

    $subscriptionNumber = $this->request->getPost('subscription_number');
    $rg = $this->request->getPost('rg');

    $member = [
      'id' => $this->request->getPost('id'),
      'name' => $this->request->getPost('name'),
      'subscription_number' => empty($subscriptionNumber) ? null : $subscriptionNumber,
      'cpf' => $this->request->getPost('cpf'),
      'rg' => empty($rg) ? null : $rg,
      'birth_date' => $this->request->getPost('birth_date'),
    ];

    $memberModel = new MemberModel();
    $memberModel->save($member);

    return $this->response->setJSON(['success' => true]);
  }
}