<?php

namespace App\Models;

use App\Http\Livewire\Admin\BannerImagen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
        use HasFactory;

        protected $fillable = ['name','slug', 'image'];

        public function getRouteKeyName(){
            return 'slug';
        }



}
