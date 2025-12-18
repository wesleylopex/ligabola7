<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResetCode extends Migration
{
    public function up()
    {
        $this->forge->addColumn('teams', [
            'reset_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'unique' => true,
                'after' => 'password' // Good practice to specify order, usually after password
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('teams', 'reset_code');
    }
}
