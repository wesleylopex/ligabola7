<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMembersTeamsDivisions extends Migration
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
            'team_division_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'member_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['approved', 'pending', 'denied'],
                'null' => false,
                'default' => 'pending'
            ],
            'denied_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['athlete', 'coach', 'president', 'assistant'],
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

        $this->forge->addForeignKey('team_division_id', 'teams_divisions', 'id', 'CASCADE', 'CASCADE', 'fk_teams_divisions_team_division_id');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE', 'fk_members_member_id');

        $this->forge->createTable('members_teams_divisions');

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('members_teams_divisions');
    }
}
