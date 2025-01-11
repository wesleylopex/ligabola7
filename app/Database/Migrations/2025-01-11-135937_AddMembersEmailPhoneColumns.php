<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMembersEmailPhoneColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('members', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'after' => 'rg'
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'after' => 'email'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('members', 'email');
        $this->forge->dropColumn('members', 'phone');
    }
}
