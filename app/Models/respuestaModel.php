<?php
namespace App\Models;
use CodeIgniter\Model;

class respuestaModel extends Model
{
    protected $table = 'respuesta';
    protected $primaryKey = 'id_respuesta';
    protected $allowedFields = ['id_mural', 'id_user','fecha_respuesta'];

    public function insertRespuesta(array $data)
    {
        // Generar la consulta SQL para insertar el mural
        $builder = $this->db->table($this->table);
        $builder->insert($data);
    }
}