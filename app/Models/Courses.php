<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;


    protected $fillable = [
        'course_name',
        'course_desc',
        'course_price',
        'course_image',
        'created_at',
        'created_at'
    ];
}
