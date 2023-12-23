<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends AdminController {
  function __construct () {
    parent::__construct();
  }

  public function index () {
    $this->load->model('SettingModel');
    $this->data['settings'] = $this->SettingModel->getLast();

    $this->load->view('admin/settings/index', $this->data);
  }

  public function save () {
    $this->form_validation->set_rules('id', 'ID', 'trim|is_natural_no_zero');
    $this->form_validation->set_rules('teams_can_create_members', 'Inscrições abertas', 'trim|required');
    $this->form_validation->set_rules('warning_text', 'Texto de informação', 'trim');

    if ($this->form_validation->run() !== true) {
      return response(['success' => false, 'error' => strip_tags(validation_errors())]);
    }

    $this->load->model('SettingModel');
    $settingsAlreadyCreated = $this->SettingModel->getLast();

    $settings = [
      'id' => !empty($settingsAlreadyCreated) ? $settingsAlreadyCreated->id : null,
      'teams_can_create_members' => boolval($this->input->post('teams_can_create_members')),
      'warning_text' => $this->input->post('warning_text'),
    ];

    $saved = $this->SettingModel->save($settings);

    if (!$saved) {
      return response(['success' => false, 'error' => 'Não foi possível salvar as configurações']);
    }

    return response(['success' => true]);
  }
}