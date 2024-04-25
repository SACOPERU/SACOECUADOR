<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Xcover extends Model
{
    use HasFactory;

        protected $fillable = ['name','slug', 'image'];

        public function getRouteKeyName(){
            return 'slug';
        }
}
