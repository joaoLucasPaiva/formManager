<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_id','form_section_id','label','name','type','required','validation','ui','position','active'
    ];
    protected $casts = [
        'required' => 'bool',
        'active' => 'bool',
        'validation' => 'array',
        'ui' => 'array',
    ];

    public function form(){ return $this->belongsTo(Form::class); }
    public function section(){ return $this->belongsTo(FormSection::class,'form_section_id'); }
    public function options(){ return $this->hasMany(FormFieldOption::class)->orderBy('position'); }
}
