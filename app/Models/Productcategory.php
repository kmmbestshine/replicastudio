<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productcategory extends Model
{
    protected $fillable = ['name', 'company_id','slug','slugss', 'status', 'created_by', 'modified_by', 'created_at', 'updated_at'];
}
