<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Complaint extends Model
{
    use HasFactory;

    protected $table = 'reclamaciones';

    protected $fillable = [
        'id',
        'documento_id',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'apellido_paterno',
        'apellido_materno',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'ciudad_id',
        'telefono',
        'email',

        
    ];

    public function documento()
    {
        return $this->belongsTo(Document::class, 'documento_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }

    public function provincia()
    {
        return $this->belongsTo(Province::class, 'provincia_id');
    }

    public function distrito()
    {
        return $this->belongsTo(District::class, 'distrito_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(City::class, 'ciudad_id');
    }

}








