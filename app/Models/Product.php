<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'sku', 'name', 'subfamilia', 'bodega', 'stock_flex', 'slug', 'description', 'price', 'price_tachado', 'price_partner', 'subcategory_id', 'brand_id',
        'quantity', 'quantity_partner', 'status', 'destacado','product_principal',
        'atocong',
        'jockeypz',
        'megaplz',
        'huaylas',
        'principal',
        'puruchu'
    ];

    protected $bodegaColumns = [
        'atocong',
        'jockeypz',
        'megaplz',
        'huaylas',
        'principal',
        'puruchu'
    ];

    protected $table = 'products';

    use HasFactory;

    const BORRADOR = 1;
    const PUBLICADO = 2;

    const NODESTACADO = 1;
    const DESTACADO = 2;

    const PRINCIPAL = 1;
    const NOPRINCIPAL = 2;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    //accesores

    public function getStockAttribute()
    {
        if ($this->subcategory->size) {
            return  ColorSize::whereHas('size.product', function (Builder $query) {
                $query->where('id', $this->id);
            })->sum('quantity', 'quantity_partner');
        } elseif ($this->subcategory->color) {
            return  ColorProduct::whereHas('product', function (Builder $query) {
                $query->where('id', $this->id);
            })->sum('quantity', 'quantity_partner');
        } else {

            return $this->quantity;
            return $this->quantity_partner;
        }
    }
    //Relacion uno a muchos

    public function sizes()
    {
        return $this->hasMany(Size::class);
    }

    //Relacion uno a muchos inversa

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    //Relacion muchos a muchos

    public function colors()
    {
        return $this->BelongsToMany(Color::class)->withPivot('quantity', 'quantity_partner', 'id');
    }
    //Relacion uno a muchos polimorfica

    public function images()
    {
        return $this->morphMany(Image::class, "imageable");
    }

    //URL amigables

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function saveOrUpdate($data)
    {
        $sku = (string)$data->SKU;
        $product = self::where('sku', $sku)->first();

        if ($product) {
            // Actualizar los campos del producto existente basado en los datos del servicio SOAP
            $product->update([

                'sku' => $sku,
                'name' => (string)$data->Descripcion,
                'price' => (float)$data->Precio,
                'description' => "description",

                // Agrega otros campos según sea necesario
            ]);
        } else {
            // El producto no existe, crear uno nuevo
            self::create([

                'sku' => $sku,
                'name' => (string)$data->Descripcion,
                'price' => (float)$data->Precio,
                'description' => "description",

                // Agrega otros campos según sea necesario
            ]);
        }
    }

    // Utiliza el método mágico __get para acceder a las propiedades de bodega de manera dinámica
    public function __get($key)
    {
        if (in_array($key, $this->bodegaColumns)) {
            return $this->attributes[$key];
        }

        return parent::__get($key);
    }

    // Utiliza el método mágico __set para actualizar las propiedades de bodega de manera dinámica
    public function __set($key, $value)
    {
        if (in_array($key, $this->bodegaColumns)) {
            $this->attributes[$key] = $value;
        } else {
            parent::__set($key, $value);
        }
    }
}
