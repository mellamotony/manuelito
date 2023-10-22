<?php

namespace App\Models;

use CodeIgniter\Model;

class MyModel extends Model
{
    protected $returnID = false;

    public function insert($data = null, bool $returnID = true)
    {
        // Guardar el valor original de la propiedad $returnID
        $originalReturnID = $this->returnID;

        // Establecer $returnID según el parámetro de la función
        $this->returnID = $returnID;

        // Realizar la inserción
        parent::insert($data);

        // Restaurar el valor original de la propiedad $returnID
        $this->returnID = $originalReturnID;
    }
}
