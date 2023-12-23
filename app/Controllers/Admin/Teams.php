<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends AdminController {
  function __construct () {
    parent::__construct();
  }

  public function index () {
    $this->load->model('TeamModel');
    $this->data['teams'] = $this->TeamModel->getAllWithDivisionName();

    $this->load->model('DivisionModel');
    $this->data['divisions'] = $this->DivisionModel->getAll();

    $this->load->view('admin/teams/index', $this->data);
  }

  public function create () {
    $this->load->model('DivisionModel');
    $this->data['divisions'] = $this->DivisionModel->getAll();

    $this->load->view('admin/teams/form', $this->data);
  }

  public function update (int $teamId) {
    $this->load->model('TeamModel');
    $this->data['team'] = $team = $this->TeamModel->getByPrimary($teamId);

    if (!$team) {
      return redirect('admin');
    }

    $this->load->model('DivisionModel');
    $this->data['divisions'] = $this->DivisionModel->getAll();

    $this->load->view('admin/teams/form', $this->data);
  }

  public function delete (int $teamId) {
    $this->load->model('TeamModel');
    $this->TeamModel->delete($teamId);

    return response(['success' => true, 'teamId' => $teamId]);
  }

  public function save () {
    $this->form_validation->set_rules('id', 'ID', 'trim|is_natural_no_zero');
    $this->form_validation->set_rules('name', 'Nome', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('username', 'Nome de usuário', 'trim|required|max_length[255]');
    $this->form_validation->set_rules('password', 'Senha', 'trim|max_length[255]');
    $this->form_validation->set_rules('division_id', 'Divisão', 'trim|required|is_natural_no_zero');

    if ($this->form_validation->run() !== true) {
      return response(['success' => false, 'error' => strip_tags(validation_errors())]);
    }

    $team = [
      'id' => $this->input->post('id'),
      'name' => $this->input->post('name'),
      'username' => $this->input->post('username'),
      'password' => hashPassword($this->input->post('password')),
      'division_id' => $this->input->post('division_id')
    ];

    if ($team['id'] && !$team['password']) {
      unset($team['password']);
    }

    $this->load->model('TeamModel');
    $saved = $this->TeamModel->save($team);

    if (!$saved) {
      return response(['success' => false, 'error' => 'Não foi possível salvar o time']);
    }

    return response(['success' => true]);
  }
}