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

  public function update (int $memberTeamDivisionId) {
    $this->load->model('TeamMemberModel');
    $this->data['member'] = $member = $this->TeamMemberModel->getByPrimary($memberId);

    if (!$member) {
      return redirect('admin');
    }

    $this->load->view('admin/team-members/form', $this->data);
  }

  public function delete (int $memberTeamDivisionId) {
    $memberTeamDivisionModel = new MemberTeamDivisionModel();
    $memberTeamDivision = $memberTeamDivisionModel->where('id', $memberTeamDivisionId)->first();

    if (!$memberTeamDivision) {
      return $this->response->setJSON(['success' => false, 'error' => 'Não foi possível encontrar o membro do time']);
    }

    $memberTeamDivisionModel->delete($memberTeamDivisionId);

    return response(['success' => true]);
  }

  public function save () {
    $this->form_validation->set_rules('id', 'ID', 'trim|is_natural_no_zero');
    $this->form_validation->set_rules('name', 'Nome', 'trim|max_length[255]');
    $this->form_validation->set_rules('subscription_number', 'Número de inscrição', 'trim|max_length[255]');
    $this->form_validation->set_rules('cpf', 'CPF', 'trim|max_length[255]');
    $this->form_validation->set_rules('rg', 'RG', 'trim|max_length[255]');
    $this->form_validation->set_rules('birth_date', 'Data de nascimento', 'trim|max_length[255]');
    $this->form_validation->set_rules('type', 'Tipo de membro', 'trim|in_list[athlete,coach,assistant,president]');
    $this->form_validation->set_rules('status', 'Status', 'trim|in_list[pending,approved,denied]');

    if ($this->form_validation->run() !== true) {
      return response(['success' => false, 'error' => strip_tags(validation_errors())]);
    }

    $rg = $this->request->getPost('rg');

    $member = [
      'id' => $this->request->getPost('id'),
      'name' => $this->request->getPost('name'),
      'subscription_number' => $this->request->getPost('subscription_number'),
      'cpf' => $this->request->getPost('cpf'),
      'rg' => empty($rg) ? null : $rg,
      'birth_date' => $this->request->getPost('birth_date'),
      'type' => $this->request->getPost('type'),
      'status' => $this->request->getPost('status'),
      'denied_reason' => $this->request->getPost('denied_reason')
    ];

    if ($member['status'] !== 'denied') {
      $member['denied_reason'] = null;
    }

    $this->load->model('TeamMemberModel');
    $saved = $this->TeamMemberModel->save($member);

    if (!$saved) {
      return response(['success' => false, 'error' => 'Não foi possível salvar o membro']);
    }

    return response(['success' => true]);
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