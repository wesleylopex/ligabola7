<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

class Home extends BaseController {
  public function index (): string {
    return view('manager/home/index', [
      'currentTeam' => $this->currentTeam
    ]);
  }
}
