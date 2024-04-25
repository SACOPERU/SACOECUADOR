<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\TabCompletion\Matcher\FunctionsMatcher;

class OrderPartner extends Model
{
    use HasFactory;

    protected $fillable = [

        'id',
      	'order',
        'name_order',
        'phone_order',
        'dni_order',
        'ruc',
        'razon_social',
        'direccion_fiscal',
        'contacto_ruc',
        'email_ruc',
        'phone_ruc',

        'name',
        'dni',
        'phone',
        'email',

        'total',
        'created_at',
        'update_at',
        'status',
        'content',
        'selected_store',
        'courrier',
        'tracking_number',

        'total',
        'xmlData',
        'status',
        'tracking_number',
        'guia_remision',
        'alto_paquete',
        'ancho_paquete',
        'largo_paquete',
        'peso_paquete',
        'observacion',

    ];

    //PARA ventas partner solo para esos casos
    const RESERVADO     = 1;
    const PAGADO        = 2;
    const APROBADO      = 3; //solo para el caso de partners
    const DESPACHADO    = 4;
    const ENTREGADO     = 5;
    const ANULADO       = 6;

    //Relacion uno a mucho inversa
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->morphMany(Image::class, "imageable");
    }

}
