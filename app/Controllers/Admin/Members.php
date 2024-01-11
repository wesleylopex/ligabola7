<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\TeamDivisionModel;
use App\Models\MemberTeamDivisionModel;
use App\Models\MemberModel;

class Members extends BaseController {
  
  public function index () {
    $divisionId = $this->input->get('divisionId');
    $where = [];

    $this->load->model('DivisionModel');

    if ($divisionId) {
      $this->data['division'] = $this->DivisionModel->getByPrimary($divisionId);

      $where = ['teams.division_id' => $divisionId];
    }

    $this->load->model('TeamMemberModel');
    $this->data['teamMembers'] = $this->TeamMemberModel->getAllWithTeamName($where);
    
    $this->load->model('TeamModel');
    $this->data['teams'] = $this->TeamModel->getAllWhere($where);

    $this->load->view('admin/team-members/index', $this->data);
  }

  public function deleteMTD (int $memberTeamDivisionId) {
    $memberTeamDivisionModel = new MemberTeamDivisionModel();
    $memberTeamDivision = $memberTeamDivisionModel->where('id', $memberTeamDivisionId)->first();

    if (!$memberTeamDivision) {
      return $this->response->setJSON(['success' => false, 'error' => 'Não foi possível encontrar o membro do time']);
    }

    $memberTeamDivisionModel->delete($memberTeamDivisionId);

    return $this->response->setJSON(['success' => true, 'id' => $memberTeamDivisionId]);
  }

  public function update (int $mtdId) {
    $memberTeamDivisionModel = new MemberTeamDivisionModel();
    $memberTeamDivision = $memberTeamDivisionModel->where('id', $mtdId)->first();

    if (!$memberTeamDivision) {
      return redirect()->back();
    }

    $memberModel = new MemberModel();
    $member = $memberModel->where('id', $memberTeamDivision->member_id)->first();

    if (!$member) {
      return redirect()->back();
    }

    return view('admin/members/form', [
      'member' => $member,
      'mtd' => $memberTeamDivision,
    ]);
  }

  public function save () {
    $validationRules = [
      'member_id' => 'trim|required|is_natural_no_zero',
      'mtd_id' => 'trim|required|is_natural_no_zero',
      'name' => 'trim|max_length[255]',
      'subscription_number' => 'trim|max_length[255]',
      'cpf' => 'trim|max_length[255]',
      'rg' => 'trim|permit_empty|max_length[255]',
      'birth_date' => 'trim|max_length[255]',
      'role' => 'trim|in_list[athlete,coach,assistant,president]',
      'status' => 'trim|in_list[pending,approved,denied]',
      'denied_reason' => 'trim|permit_empty|max_length[255]',
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $rg = $this->request->getPost('rg');
    $subscriptionNumber = $this->request->getPost('subscription_number');

    $member = [
      'id' => $this->request->getPost('member_id'),
      'name' => $this->request->getPost('name'),
      'subscription_number' => empty($subscriptionNumber) ? null : $subscriptionNumber,
      'cpf' => $this->request->getPost('cpf'),
      'rg' => empty($rg) ? null : $rg,
      'birth_date' => $this->request->getPost('birth_date'),
    ];

    $memberModel = new MemberModel();
    $memberModel->save($member);

    $memberTeamDivision = [
      'id' => $this->request->getPost('mtd_id'),
      'member_id' => $member['id'],
      'role' => $this->request->getPost('role'),
      'status' => $this->request->getPost('status'),
      'denied_reason' => $this->request->getPost('denied_reason'),
    ];

    if ($memberTeamDivision['status'] !== 'denied') {
      $memberTeamDivision['denied_reason'] = null;
    }

    $memberTeamDivisionModel = new MemberTeamDivisionModel();
    $memberTeamDivisionModel->save($memberTeamDivision);

    return $this->response->setJSON(['success' => true]);
  }

  public function approve () {
    $validationRules = [
      'id' => 'required|is_natural_no_zero',
      'subscription_number' => 'permit_empty|max_length[255]',
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $memberTeamDivisionModel = new MemberTeamDivisionModel();
    $memberTeamDivision = $memberTeamDivisionModel->where([
      'id' => $this->request->getPost('id'),
    ])->first(); 

    if (!$memberTeamDivision) {
      return $this->response->setJSON(['success' => false, 'error' => 'Não foi possível encontrar o membro do time']);
    }

    $memberTeamDivisionModel->save([
      'id' => $memberTeamDivision->id,
      'status' => 'approved'
    ]);

    $subscriptionNumber = $this->request->getPost('subscription_number');

    if (!empty($subscriptionNumber)) {
      $memberModel = new MemberModel();

      $subscriptionNumberAlreadyExists = $memberModel->where([
        'subscription_number' => $subscriptionNumber,
        'id !=' => $memberTeamDivision->member_id
      ])->first();

      if ($subscriptionNumberAlreadyExists) {
        return $this->response->setJSON([
          'success' => false,
          'error' => 'Já existe um atleta com esse número de inscrição'
        ]);
      }

      $memberModel->update($memberTeamDivision->member_id, [
        'subscription_number' => $subscriptionNumber
      ]);
    }

    return $this->response->setJSON([
      'success' => true,
      'id' => $memberTeamDivision->id,
    ]);
  }
}