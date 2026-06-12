<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicService extends Model
{
    /** @use HasFactory<\Database\Factories\PublicServiceFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ar_name' ,
        'fr_name' ,
        'en_name' , 

        'ar_short_description' ,
        'fr_short_description' ,
        'en_short_description' ,
        
        'ar_description' , 
        'fr_description' , 
        'en_description' , 
        
        'image' ,  
        'type' ,

        'is_published' ,
    ];
}
