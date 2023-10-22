<?php
namespace App\Models;

use CodeIgniter\Model;

class publicarModel extends Model
{
    protected $table = 'publicar';
    protected $primaryKey = 'id_publicacion';
    protected $allowedFields = ['id_mural', 'id_user', 'fecha_publicacion','fin_publicacion'];

    public function insertPublicacion(array $data)
    {
        // Generar la consulta SQL para insertar el mural
        $builder = $this->db->table($this->table);
        $builder->insert($data);
    }

}
