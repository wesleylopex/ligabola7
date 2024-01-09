<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

use App\Models\MemberModel;
use App\Models\DivisionModel;

class Home extends BaseController {
  public function index (): string {
    $memberModel = new MemberModel();

    $teamId = $this->currentTeam->id;
    $championshipId = $this->currentDivision->championship_id;

    $members = $memberModel->getMembers($teamId, $championshipId);

    return view('manager/home/index', [
      'currentTeam' => $this->currentTeam,
      'members' => $members,
      'division' => $this->currentDivision
    ]);
  }
}
