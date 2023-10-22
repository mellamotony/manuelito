<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitarModel extends Model
{
    protected $table = 'solicitar';
    protected $primaryKey = 'id_solicitud';
    protected $allowedFields = ['id_mural', 'id_disenador','id_editor', 'fecha_solicitud'];

    public function insertSolicitud(array $data)
    {
        // Generar la consulta SQL para insertar el mural
        $builder = $this->db->table($this->table);
        $builder->insert($data);
    }

}

