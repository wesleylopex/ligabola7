<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\ChampionshipModel;
use App\Models\DivisionModel;
use App\Models\MemberModel;
use App\Models\TeamModel;

class Championships extends BaseController {
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