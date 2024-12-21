<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

use App\Models\MemberModel;
use App\Models\MemberTeamDivisionModel;
use App\Models\TeamModel;
use App\Models\TeamDivisionModel;
use App\Models\DivisionModel;

class Members extends BaseController {
  public function create () {
    if (!$this->currentDivision->subscriptions_opened) {
      return redirect()->to('/manager/home');
    }

    return view('manager/members/form');
  }

  public function update (int $memberId): string {
    $memberModel = new MemberModel();
    $member = $memberModel->find($memberId);

    $memberTeamDivisionModel = new MemberTeamDivisionModel();
    $memberTeamDivision = $memberTeamDivisionModel->where([
      'member_id' => $memberId,
      'team_division_id' => $this->currentTeamDivision->id
    ])->first();

    $teamModel = new TeamModel();
    $team = $teamModel->find($this->currentTeamDivision->team_id);
    
    $member->role = $memberTeamDivision->role;

    return view('manager/members/form', [
      'member' => $member,
      'memberTeamDivision' => $memberTeamDivision,
      'team' => $team
    ]);
  }

  public function save () {
    $validationRules = [
      'name' => ['label' => 'Nome', 'rules' => 'required'],
      'birth_date' => ['label' => 'Data de nascimento', 'rules' => 'required|valid_date[Y-m-d]'],
      'cpf' => ['label' => 'CPF', 'rules' => 'required'],
      'rg' => ['label' => 'RG', 'rules' => 'permit_empty'],
      'role' => ['label' => 'Tipo', 'rules' => 'required|in_list[athlete,coach,president,assistant]'],
    ];

    if (!$this->validate($validationRules)) {
      return $this->response->setJSON([
        'success' => false,
        'error' => $this->validator->getErrors(),
      ]);
    }

    $memberTeamDivisionModel = new MemberTeamDivisionModel();

    $limits = [
      'athlete' => [
        'name' => 'atletas',
        'limit' => 23
      ],
      'coach' => [
        'name' => 'treinadores',
        'limit' => 1
      ],
      'president' => [
        'name' => 'representantes legais',
        'limit' => 1
      ],
      'assistant' => [
        'name' => 'auxiliares',
        'limit' => 1
      ]
    ];

    $memberRole = $this->request->getPost('role');
    $memberTeamDivisionCount = $memberTeamDivisionModel->where([
      'team_division_id' => $this->currentTeamDivision->id,
      'role' => $memberRole,
      'status' => 'approved'
    ])->countAllResults();

    if ($memberTeamDivisionCount >= $limits[$memberRole]['limit']) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Limite de ' . $limits[$memberRole]['name'] . ' excedido'
      ]);
    }

    $memberModel = new MemberModel();

    $isUpdating = !empty($this->request->getPost('id'));
    $rg = $this->request->getPost('rg');

    $member = [
      'name' => $this->request->getPost('name'),
      'birth_date' => $this->request->getPost('birth_date'),
      'cpf' => $this->request->getPost('cpf'),
      'rg' => empty($rg) ? null : $rg
    ];

    $memberAlreadyExists = $memberModel->where('cpf', $member['cpf'])
      ->first();

    if ($memberAlreadyExists) {
      $member['id'] = $memberAlreadyExists->id;

      $ignoreMemberInAnotherTeam = $this->request->getPost('ignore_member_in_another_team');

      $memberInAnotherTeam = $memberTeamDivisionModel->where([
        'member_id' => $memberAlreadyExists->id,
        'team_division_id !=' => $this->currentTeamDivision->id,
        'status !=' => 'denied'
      ])->findAll();

      $memberInAnotherTeamInSameChampionship = null;

      $teamDivisionModel = new TeamDivisionModel();
      $divisionModel = new DivisionModel();

      foreach ($memberInAnotherTeam as $mtd) {
        $teamDivision = $teamDivisionModel->find($mtd->team_division_id);
        $division = $divisionModel->find($teamDivision->division_id);

        if ($division->championship_id === $this->currentDivision->championship_id) {
          $memberInAnotherTeamInSameChampionship = $mtd;
          break;
        }
      }

      if (!$ignoreMemberInAnotherTeam && $memberInAnotherTeamInSameChampionship) {
        $teamDivision = $teamDivisionModel->find($memberInAnotherTeamInSameChampionship->team_division_id);

        $teamModel = new TeamModel();
        $team = $teamModel->find($teamDivision->team_id);

        return $this->response->setJSON([
          'success' => false,
          'error' => 'Membro já cadastrado em outro time',
          'memberInAnotherTeam' => $team->name
        ]);
      }

      $memberAlreadyInTeam = $memberTeamDivisionModel->where([
        'member_id' => $member['id'],
        'team_division_id' => $this->currentTeamDivision->id,
        'role' => $memberRole
      ])->countAllResults() > 0;
  
      if ($memberAlreadyInTeam && !$isUpdating) {
        return $this->response->setJSON([
          'success' => false,
          'error' => 'Membro já cadastrado'
        ]);
      }
    }

    $rgDuplicated = false;
    $hasNoId = !array_key_exists('id', $member);

    if ($hasNoId) {
      $rgDuplicated = $memberModel->where('rg', $rg)
        ->countAllResults() > 0;
    } else {
      $rgDuplicated = $memberModel->where([
        'rg' => $rg,
        'id !=' => $member['id']
      ])->countAllResults() > 0;
    }

    if (!empty($rg) && $rgDuplicated) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'RG já cadastrado'
      ]);
    }

    $success = $memberModel->save($member);

    if (!$success) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Erro ao salvar membro'
      ]);
    }

    $memberId = array_key_exists('id', $member) ? $member['id'] : $memberModel->getInsertID();

    $memberTeamDivision = [
      'id' => $isUpdating ? $this->request->getPost('mtd_id') : null,
      'member_id' => $memberId,
      'team_division_id' => $this->currentTeamDivision->id,
      'status' => 'pending',
      'role' => $memberRole
    ];

    $success = $memberTeamDivisionModel->save($memberTeamDivision);

    return $this->response->setJSON(['success' => true]);
  }

  public function find () {
    $cpf = $this->request->getGet('cpf');

    $memberModel = new MemberModel();
    $member = $memberModel->where('cpf', $cpf)
      ->first();

    if (!$member) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Membro não encontrado'
      ]);
    }

    return $this->response->setJSON([
      'success' => true,
      'member' => $member
    ]);
  }
}
