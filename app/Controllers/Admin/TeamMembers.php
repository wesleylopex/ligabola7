<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class TeamMembers extends AdminController {
  function __construct () {
    parent::__construct();
  }
  
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

  public function update (int $memberId) {
    $this->load->model('TeamMemberModel');
    $this->data['member'] = $member = $this->TeamMemberModel->getByPrimary($memberId);

    if (!$member) {
      return redirect('admin');
    }

    $this->load->view('admin/team-members/form', $this->data);
  }

  public function delete (int $memberId) {
    $this->load->model('TeamMemberModel');
    $this->TeamMemberModel->delete($memberId);

    return response(['success' => true, 'memberId' => $memberId]);
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

    $member = [
      'id' => $this->input->post('id'),
      'name' => $this->input->post('name'),
      'subscription_number' => $this->input->post('subscription_number'),
      'cpf' => $this->input->post('cpf'),
      'rg' => $this->input->post('rg'),
      'birth_date' => $this->input->post('birth_date'),
      'type' => $this->input->post('type'),
      'status' => $this->input->post('status'),
      'denied_reason' => $this->input->post('denied_reason')
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
    $this->form_validation->set_rules('id', 'ID', 'trim|is_natural_no_zero');
    $this->form_validation->set_rules('subscription_number', 'Número de inscrição', 'trim|max_length[255]');

    if ($this->form_validation->run() !== true) {
      return response(['success' => false, 'error' => strip_tags(validation_errors())]);
    }

    $this->load->model('TeamMemberModel');

    $memberId = $this->input->post('id');
    $memberIsPending = $this->TeamMemberModel->count(['id' => $memberId, 'status' => 'pending']) === 1;

    if (!$memberIsPending) {
      return response(['success' => false, 'error' => 'Não foi possível aprovar o membro']);
    }

    $member = [
      'id' => $memberId,
      'subscription_number' => $this->input->post('subscription_number'),
      'status' => 'approved'
    ];

    $saved = $this->TeamMemberModel->update($member);

    if (!$saved) {
      return response(['success' => false, 'error' => 'Não foi possível salvar o membro']);
    }

    return response(['success' => true, 'memberId' => $memberId, 'subscriptionNumber' => $member['subscription_number']]);
  }
}