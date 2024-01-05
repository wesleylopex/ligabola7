<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDivisionsSettingsColumns extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();

        $this->forge->addColumn('divisions', [
            'subscriptions_opened' => [
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => true,
                'after' => 'color'
            ],
            'warning_text' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
                'after' => 'subscriptions_opened'
            ],
        ]);


        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropColumn('divisions', 'subscriptions_opened');
        $this->forge->dropColumn('divisions', 'warning_text');
    }
}