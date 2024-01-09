<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

use App\Models\MemberModel;
use App\Models\MemberTeamDivisionModel;
use App\Models\TeamModel;
use App\Models\TeamDivisionModel;

class Members extends BaseController {
  public function create (): string {
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

    $limitExceed = $memberTeamDivisionModel->where([
      'role' => 'athlete',
      'team_division_id' => $this->currentTeamDivision->id,
      'status' => 'approved'
    ])->countAllResults() >= 23;

    $memberRole = $this->request->getPost('role');

    if ($memberRole === 'athlete' && $limitExceed) {
      return $this->response->setJSON([
        'success' => false,
        'error' => 'Limite de atletas excedido'
      ]);
    }

    $memberModel = new MemberModel();

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
        'team_division_id !=' => $this->currentTeamDivision->id
      ])->first();

      if (!$ignoreMemberInAnotherTeam && $memberInAnotherTeam) {
        $teamDivisionModel = new TeamDivisionModel();
        $teamDivision = $teamDivisionModel->find($memberInAnotherTeam->team_division_id);

        $teamModel = new TeamModel();
        $team = $teamModel->find($teamDivision->team_id);

        return $this->response->setJSON([
          'success' => false,
          'error' => 'Membro já cadastrado em outro time',
          'memberInAnotherTeam' => $team->name
        ]);
      }
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
