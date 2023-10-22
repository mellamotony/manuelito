<?php

namespace App\Models;

use CodeIgniter\Model;

class ImagenModel extends Model
{
    protected $table = 'imagenes';
    protected $primaryKey = 'id_imagenes';
    protected $allowedFields = ['url', 'height', 'width', 'posx', 'posy', 'alt', 'border_color', 'border_style', 'border_radius', 'id_mural'];

    protected $returnType = 'array';
    protected $useTimestamps = false;

    // Agregar la relación con la tabla mural
    protected $with = ['mural'];

    // Relación con la tabla mural
    /*
    public function mural()
    {
        return $this->belongsTo(Mural_Model::class, 'id_mural', 'id_mural');
    }*/

    public function insertBatch(?array $data = null, ?bool $escape = null, int $batchSize = 100, bool $testing = false)
    {
        return parent::insertBatch($data, $escape, $batchSize, $testing);
    }
}