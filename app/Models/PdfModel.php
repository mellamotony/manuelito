<?php

namespace App\Models;

use CodeIgniter\Model;

class PdfModel extends Model
{
    protected $table = 'pdfs';
    protected $primaryKey = 'id_pdfs';
    protected $allowedFields = ['url_pdfs', 'posx', 'posy', 'height', 'width', 'border_color', 'border_style', 'border_radius', 'id_mural'];

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
