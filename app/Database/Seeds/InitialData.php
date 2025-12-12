<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialData extends Seeder
{
    public function run()
    {
        // 1. Admin User
        $this->db->table('admin_users')->insert([
            'email' => 'admin@admin.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Championships
        $championships = [
            [
                'name' => 'Copa Verão 2024',
                'start_date' => date('Y-m-d', strtotime('+1 week')),
                'end_date' => date('Y-m-d', strtotime('+2 months')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Liga Principal 2024',
                'start_date' => date('Y-m-d', strtotime('+1 month')),
                'end_date' => date('Y-m-d', strtotime('+6 months')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        
        $this->db->table('championships')->insertBatch($championships);
        $championshipId1 = $this->db->insertID() - 1; // Assuming auto-increment sequential
        $championshipId2 = $this->db->insertID();     
        // NOTE: insertID() returns the ID of the *first* row in a batch insert in some drivers, 
        // or the last in others. To be safe, I will fetch them.
        
        $championshipIds = $this->db->table('championships')->select('id')->get()->getResultArray();
        $championshipId1 = $championshipIds[0]['id'];
        $championshipId2 = $championshipIds[1]['id'];

        // 3. Divisions
        $divisions = [
            [
                'championship_id' => $championshipId1,
                'name' => 'Série A',
                'color' => '#FF0000',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'championship_id' => $championshipId1,
                'name' => 'Série B',
                'color' => '#00FF00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'championship_id' => $championshipId2,
                'name' => 'Única',
                'color' => '#0000FF',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('divisions')->insertBatch($divisions);
        
        $divisionIds = $this->db->table('divisions')->select('id')->get()->getResultArray(); // 0, 1, 2

        // 4. Teams
        $teams = [];
        for ($i = 1; $i <= 6; $i++) {
            $teams[] = [
                'name' => 'Time ' . $i . ' FC',
                'email' => 'time' . $i . '@test.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'image' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('teams')->insertBatch($teams);
        $teamIds = $this->db->table('teams')->select('id')->get()->getResultArray();

        // 5. Teams Divisions (Link)
        // Link first 2 teams to Div 1 (Série A)
        // Link next 2 teams to Div 2 (Série B)
        // Link last 2 teams to Div 3 (Única)
        $teamsDivisions = [];
        
        // Div 1
        $teamsDivisions[] = ['team_id' => $teamIds[0]['id'], 'division_id' => $divisionIds[0]['id'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
        $teamsDivisions[] = ['team_id' => $teamIds[1]['id'], 'division_id' => $divisionIds[0]['id'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
        
        // Div 2
        $teamsDivisions[] = ['team_id' => $teamIds[2]['id'], 'division_id' => $divisionIds[1]['id'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
        $teamsDivisions[] = ['team_id' => $teamIds[3]['id'], 'division_id' => $divisionIds[1]['id'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];

        // Div 3
        $teamsDivisions[] = ['team_id' => $teamIds[4]['id'], 'division_id' => $divisionIds[2]['id'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
        $teamsDivisions[] = ['team_id' => $teamIds[5]['id'], 'division_id' => $divisionIds[2]['id'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];

        $this->db->table('teams_divisions')->insertBatch($teamsDivisions);
        
        // We need IDs of teams_divisions to link members
        $tdIds = $this->db->table('teams_divisions')->select('id')->get()->getResultArray();

        // 6. Members
        $members = [];
        for ($i = 1; $i <= 20; $i++) {
            $members[] = [
                'name' => 'Jogador ' . $i,
                'cpf' => '111.111.111-' . str_pad($i, 2, '0', STR_PAD_LEFT), // Fake CPF pattern
                'rg' => '22.222.222-' . $i,
                'birth_date' => date('Y-m-d', strtotime('-' . (18 + $i) . ' years')), // Various ages
                'email' => 'jogador' . $i . '@test.com',
                'phone' => '(11) 99999-99' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('members')->insertBatch($members);
        $memberIds = $this->db->table('members')->select('id')->get()->getResultArray();

        // 7. Members Teams Divisions (Link)
        // Distribute members among teams
        $membersTeamsDivisions = [];
        $roles = ['athlete', 'coach', 'president', 'assistant'];
        
        foreach ($memberIds as $index => $member) {
            // Assign to one of the teams_divisions
            // $index % 6 will cycle through the 6 team_divisions we created
            $tdId = $tdIds[$index % count($tdIds)]['id'];
            
            $membersTeamsDivisions[] = [
                'team_division_id' => $tdId,
                'member_id' => $member['id'],
                'status' => 'approved',
                'role' => $roles[$index % 4], // Distribute roles
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        $this->db->table('members_teams_divisions')->insertBatch($membersTeamsDivisions);
    }
}
