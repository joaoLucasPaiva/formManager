<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormAnswer extends Model
{
    protected $fillable = [
        'form_submission_id','form_field_id','value','json_value','number_value','date_value','datetime_value'
    ];
    protected $casts = [
        'json_value' => 'array',
        'date_value' => 'date',
        'datetime_value' => 'datetime',
    ];

    public function submission(){ return $this->belongsTo(FormSubmission::class,'form_submission_id'); }
    public function field(){ return $this->belongsTo(FormField::class); }
}
