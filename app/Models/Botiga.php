<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Botiga extends Model
{

  protected $table="botiga";

 public function productes()
 {
        return $this->belongsToMany(Producte::class,'estoc_botiga');
 }
}
