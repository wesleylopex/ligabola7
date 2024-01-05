<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\ChampionshipModel;
use App\Models\DivisionModel;
use App\Models\MemberModel;
use App\Models\TeamModel;
use App\Models\TeamDivisionModel;

class Championships extends BaseController {
  public function create () {
    return view('admin/championships/form');
  }

  public function save () {
    $validationRules = [
      'name' => 'required',
      'start_date' => 'required|valid_date',
      'end_date' => 'required|valid_date',
      'divisions' => 'required'
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $championshipModel = new ChampionshipModel();

    $championship = [
      'id' => $this->request->getPost('id'),
      'name' => $this->request->getPost('name'),
      'start_date' => $this->request->getPost('start_date'),
      'end_date' => $this->request->getPost('end_date')
    ];

    $championshipModel->save($championship);

    $championshipId = empty($championship['id']) ? $championshipModel->getInsertID() : $championship['id'];

    $divisions = json_decode($this->request->getPost('divisions'));

    $divisionModel = new DivisionModel();

    foreach ($divisions as $division) {
      $division->championship_id = $championshipId;
      $divisionModel->save($division);
    }

    $deletedDivisions = json_decode($this->request->getPost('deletedDivisions'));

    foreach ($deletedDivisions as $divisionId) {
      $divisionModel->delete($divisionId);
    }

    return $this->response->setJSON(['success' => true]);
  }

  public function divisions (int $championshipId) {
    $championshipModel = new ChampionshipModel();
    $championship = $championshipModel->find($championshipId);

    $divisionModel = new DivisionModel();
    $divisions = $divisionModel->where('championship_id', $championshipId)->findAll();

    return view('admin/championships/index', [
      'championship' => $championship,
      'divisions' => $divisions
    ]);
  }

  public function division (int $divisionId) {
    $divisionModel = new DivisionModel();
    $division = $divisionModel->find($divisionId);

    $memberModel = new MemberModel();
    $members = $memberModel->getForDivision($divisionId);

    $teamModel = new TeamModel();
    $teams = $teamModel->getByDivision($divisionId);

    return view('admin/division/index', [
      'division' => $division,
      'members' => $members,
      'teams' => $teams
    ]);
  }

  public function teams (int $championshipId) {
    $championshipModel = new ChampionshipModel();
    $championship = $championshipModel->find($championshipId);

    $teamModel = new TeamModel();
    $teams = $teamModel->orderBy('name', 'ASC')->findAll();

    $divisionModel = new DivisionModel();
    $divisions = $divisionModel->where('championship_id', $championshipId)->findAll();

    $teamDivisionModel = new TeamDivisionModel();
    $teamsDivisions = $teamDivisionModel->findAll();

    return view('admin/championships/teams', [
      'championship' => $championship,
      'teams' => $teams,
      'divisions' => $divisions,
      'teamsDivisions' => $teamsDivisions
    ]);
  }

  public function saveTeamsDivisions () {
    $validationRules = [
      'teamsDivisions' => 'required'
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $teamDivisionModel = new TeamDivisionModel();

    $teamsDivisions = json_decode($this->request->getPost('teamsDivisions'));

    foreach ($teamsDivisions as $teamDivision) {
      $teamDivisionModel->save($teamDivision);
    }

    $deletedTeamsDivisions = json_decode($this->request->getPost('deletedTeamsDivisions'));

    foreach ($deletedTeamsDivisions as $teamDivision) {
      $teamDivisionModel->delete($teamDivision);
    }

    return $this->response->setJSON(['success' => true]);
  }

  public function settings (int $championshipId) {
    $championshipModel = new ChampionshipModel();
    $championship = $championshipModel->find($championshipId);

    return view('admin/championships/settings', [
      'championship' => $championship
    ]);
  }

  public function saveSettings () {
    $validationRules = [
      'id' => 'required|is_natural_no_zero',
      'name' => 'required',
      'start_date' => 'required|valid_date',
      'end_date' => 'required|valid_date'
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $championship = [
      'id' => $this->request->getPost('id'),
      'name' => $this->request->getPost('name'),
      'start_date' => $this->request->getPost('start_date'),
      'end_date' => $this->request->getPost('end_date')
    ];

    $championshipModel = new ChampionshipModel();
    $championshipModel->save($championship);

    return $this->response->setJSON(['success' => true]);
  }
}