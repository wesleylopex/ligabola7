<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model {
  protected $DBGroup = 'default';
  protected $table = 'members';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $returnType = 'object';
  protected $useSoftDeletes = false;
  protected $protectFields = false;
  protected $allowedFields = [];

  // Dates
  protected $useTimestamps = true;
  protected $dateFormat = 'datetime';
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  protected $deletedField = 'deleted_at';

  // Validation
  protected $validationRules = [];
  protected $validationMessages = [];
  protected $skipValidation = false;
  protected $cleanValidationRules = true;

  // Callbacks
  protected $allowCallbacks = true;
  protected $beforeInsert = [];
  protected $afterInsert = [];
  protected $beforeUpdate = [];
  protected $afterUpdate = [];
  protected $beforeFind = [];
  protected $afterFind = [];
  protected $beforeDelete = [];
  protected $afterDelete = [];

  public function getMembers (int $teamId, int $championshipId): array {
    $db = \Config\Database::connect();
 
    $members = $db
      ->table('members_teams_divisions')
      ->select('
        members.*,
        members_teams_divisions.id AS mtd_id,
        members_teams_divisions.role AS role,
        members_teams_divisions.status AS status,
        members_teams_divisions.denied_reason AS denied_reason
      ')
      ->join('members', 'members_teams_divisions.member_id = members.id')
      ->join('teams_divisions', 'members_teams_divisions.team_division_id = teams_divisions.id')
      ->join('divisions', 'teams_divisions.division_id = divisions.id')
      ->join('teams', 'teams_divisions.team_id = teams.id')
      ->where('teams.id', $teamId)
      ->where('divisions.championship_id', $championshipId)
      ->get()
      ->getResult();

    return $members ?? [];
  }

  public function getForDivision (int $divisionId) {
    $db = \Config\Database::connect();

    $members = $db
      ->table('members_teams_divisions')
      ->select('
        members.id as member_id,
        members.name,
        members.cpf,
        members.subscription_number,
        members.rg,
        members.birth_date,
        
        members_teams_divisions.parental_consent_document,
        members_teams_divisions.id,
        members_teams_divisions.role as role,
        members_teams_divisions.status as status,
        members_teams_divisions.denied_reason as denied_reason,
        members_teams_divisions.created_at as created_at,

        teams.id as team_id,
        teams.name as team_name,
      ')
      ->join('members', 'members_teams_divisions.member_id = members.id')
      ->join('teams_divisions', 'members_teams_divisions.team_division_id = teams_divisions.id')
      ->join('teams', 'teams_divisions.team_id = teams.id')
      ->where('teams_divisions.division_id', $divisionId)
      ->orderBy('members_teams_divisions.created_at', 'DESC')
      ->get()
      ->getResult();

    return $members ?? [];
  }
}
