<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = ['form_id','submitter_type','submitter_id','external_ref','ip','user_agent'];

    public function form(){ return $this->belongsTo(Form::class); }
    public function answers(){ return $this->hasMany(FormAnswer::class); }
}
