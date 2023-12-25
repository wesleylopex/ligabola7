<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\ChampionshipModel;
use App\Models\DivisionModel;
use App\Models\MemberModel;
use App\Models\TeamModel;

class Championships extends BaseController {
  public function create () {
    return view('admin/championship/form');
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

    return view('admin/championship/index', [
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
}