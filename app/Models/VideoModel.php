<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoModel extends Model
{
    protected $table = 'videos';
    protected $primaryKey = 'id_video';
    protected $allowedFields = ['url_video', 'posx', 'posy', 'height', 'width', 'border_color', 'border_style', 'border_radius', 'formato', 'duracion', 'id_mural'];

    protected $returnType = 'array';
    protected $useTimestamps = false;

    // Agregar la relación con la tabla mural
    protected $with = ['mural'];

    // Relación con la tabla mural
    public function mural()
    {
        return $this->belongsTo(Mural_Model::class, 'id_mural', 'id_mural');
    }

    public function insertBatch(?array $data = null, ?bool $escape = null, int $batchSize = 100, bool $testing = false)
    {
        return parent::insertBatch($data, $escape, $batchSize, $testing);
    }
}