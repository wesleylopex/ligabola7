<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMembers extends Migration
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
            'subscription_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'null' => true,
                'default' => null
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'birth_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'cpf' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'null' => false
            ],
            'rg' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'null' => true,
                'default' => null
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
        $this->forge->createTable('members');
    }

    public function down()
    {
        $this->forge->dropTable('members');
    }
}
