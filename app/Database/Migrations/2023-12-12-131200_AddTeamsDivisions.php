<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeamsDivisions extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'team_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'division_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
        ]);
        
        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('team_id', 'teams', 'id', 'CASCADE', 'CASCADE', 'fk_teams_divisions_team_id');
        $this->forge->addForeignKey('division_id', 'divisions', 'id', 'CASCADE', 'CASCADE', 'fk_teams_divisions_division_id');

        $this->forge->createTable('teams_divisions');

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('teams_divisions');
    }
}
