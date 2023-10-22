<?php
namespace App\Models;
use CodeIgniter\Model;
class user_model extends Model {

    protected $table = 'usuario'; // Nombre de la tabla de usuarios en la base de datos
    protected $primaryKey = 'id_user'; // Clave primaria de la tabla

    protected $allowedFields = ['nombre', 'contrasenia', 'apellido_p', 'apellido_m', 'id_rol', 'email'];


    public function getUserByEmail($email)
    {
        //return $this->where('email', $email)->first();
        return $this->select('usuario.*, rol.perfil as perfil')
            ->join('rol', 'rol.id_rol = usuario.id_rol', 'inner')
            ->where('email', $email)
            ->first();

    }

    public function insertUser($userData)
    {
        $this->db->table('usuario')->insert($userData);
        return true;
    }

    public function getUsers(){
        // Construir la consulta
        $builder = $this->db->table($this->table . ' as us');
        $builder->select('us.id_user, us.nombre as nombre, CONCAT(us.apellido_p, \' \', us.apellido_m) as Apellidos, us.email as correo, r.id_rol as idRol, r.perfil as rol');
        $builder->join('rol as r', 'us.id_rol = r.id_rol', 'inner');

        // Ejecutar la consulta
        $query = $builder->get();

        // Devolver el resultado
        return $query->getResult();
    }
    public function  getUserbyID($id_user){
        // Construir la consulta
        $builder = $this->db->table($this->table . ' as us');
        $builder->select('us.id_user, us.nombre as nombre, CONCAT(us.apellido_p, \' \', us.apellido_m) as apellidos, us.email as correo, r.id_rol as idRol, r.perfil as rol');
        $builder->join('rol as r', 'us.id_rol = r.id_rol', 'inner');
        $builder->where('us.id_user', $id_user); // Filtro para el id_user

        // Ejecutar la consulta
        $query = $builder->get();

        // Devolver el primer resultado (debería ser solo uno)
        return $query->getRow();
    }




}