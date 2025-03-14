<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentalConsentDocument extends Migration
{
    public function up()
    {
        $this->forge->addColumn('members_teams_divisions', [
            'parental_consent_document' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'after' => 'role'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('members_teams_divisions', 'parental_consent_document');
    }
}