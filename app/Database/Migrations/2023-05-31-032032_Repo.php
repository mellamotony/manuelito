<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Repo extends Migration
{
    public function up()
    {

        $this->forge->addField([
            'id_categoria' =>[
                'type'     => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ]
        ]);
        $this->forge->addKey('id_categoria',true);
        $this->forge->createTable('nuevaTabla');
    }

    public function down()
    {
        $this->forge->dropTable('nuevaTabla');
    }
}
