<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\ChampionshipModel;

class Home extends BaseController {
  public function index () {
    $championshipModel = new ChampionshipModel();
    $championships = $championshipModel->findAll();

    return view('admin/home/index', [
      'championships' => $championships
    ]);
  }
}