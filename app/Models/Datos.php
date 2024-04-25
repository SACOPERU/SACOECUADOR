<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datos extends Model
{
    protected $table = 'datos'; // Especifica el nombre de la tabla en la base de datos

    protected $fillable = ['nombre', 'email']; // Lista de campos asignables

    // Agrega otros campos si es necesario
}


