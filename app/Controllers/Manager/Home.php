<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

use App\Models\MemberModel;

class Home extends BaseController {
  public function index (): string {
    $memberModel = new MemberModel();

    $teamId = $this->currentTeam->id;
    $championshipId = 11;

    $members = $memberModel->getMembers($teamId, $championshipId);

    return view('manager/home/index', [
      'currentTeam' => $this->currentTeam,
      'members' => $members
    ]);
  }
}
