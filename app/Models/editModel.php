<?php
namespace App\Models;

use CodeIgniter\Model;

class editModel extends Model
{
    protected $table = 'edit';
    protected $primaryKey = 'id_edit';
    protected $allowedFields = ['id_mural', 'id_user', 'fecha_edicion'];

    public function insertEdit(array $data)
    {
        // Generar la consulta SQL para insertar el mural
        $builder = $this->db->table($this->table);
        $builder->insert($data);
    }

    public function getEditsDetails()
    {
        $builder = $this->db->table('edit');
        $builder->select("CONCAT(usuario.nombre, ' ', usuario.apellido_p) as modificado_por", false);
        $builder->select('mural.nombrem as nombre_mural');
        $builder->select('edit.fecha_edicion as ultima_modificacion');
        $builder->join('mural', 'mural.id_mural = edit.id_mural');
        $builder->join('usuario', 'usuario.id_user = edit.id_user');

        return $builder->get()->getResult();
    }

    public function getEditsDetail($id_user)
    {
        $builder = $this->db->table('edit');
        $builder->select("CONCAT(usuario.nombre, ' ', usuario.apellido_p) as modificado_por", false);
        $builder->select('mural.nombrem as nombre_mural');
        $builder->select('edit.fecha_edicion as ultima_modificacion');
        $builder->join('mural', 'mural.id_mural = edit.id_mural');
        $builder->join('usuario', 'usuario.id_user = edit.id_user');

        // Aplicar la condición WHERE para filtrar por id_user
        $builder->where('usuario.id_user', $id_user);

        return $builder->get()->getResult();
    }

}