<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentalConsentDocument extends Migration
{
    public function up()
    {
        $this->forge->addColumn('members', [
            'parental_consent_document' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'after' => 'phone'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('members', 'parental_consent_document');
    }
}