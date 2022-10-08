<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'blog_post';


    protected $fillable = [
        'title',
        'body',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
