<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormFieldOption extends Model
{
    protected $fillable = ['form_field_id','label','value','position'];

    public function field(){ return $this->belongsTo(FormField::class); }
}
