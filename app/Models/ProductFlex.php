<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFlex extends Model
{
    protected $table = 'ProductFlex';
		public $timestamps = false;

		protected $fillable = [
			'__Empresa', '__CodProducto','__Bodega'
		];
}
