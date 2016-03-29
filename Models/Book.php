<?php namespace App\Models;
  
use App\Models\Base\BaseModel;
  
class Book extends BaseModel
{
     protected $fillable = ['title', 'author', 'isbn'];
     
}
?>