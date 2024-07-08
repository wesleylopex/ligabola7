<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMembersBanColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('members', [
            'banned_by' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'after' => 'rg'
            ],
            'banned_at' => [
                'type' => 'DATE',
                'null' => true,
                'default' => null,
                'after' => 'banned_by'
            ],
            'ban_expires_at' => [
                'type' => 'DATE',
                'null' => true,
                'default' => null,
                'after' => 'banned_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('members', 'banned_by');
        $this->forge->dropColumn('members', 'banned_at');
        $this->forge->dropColumn('members', 'ban_expires_at');
    }
}
