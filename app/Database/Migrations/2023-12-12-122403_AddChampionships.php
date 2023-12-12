<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddChampionships extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'end_date' => [
                'type' => 'DATE',
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
        $this->forge->createTable('championships');
    }

    public function down()
    {
        $this->forge->dropTable('championships');
    }
}
