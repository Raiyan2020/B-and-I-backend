<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class  Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends=['title','body'];
    protected $guarded = [''];

    public function getTitleAttribute(){
        $title = app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
        return $title;
    }
    public function getBodyAttribute(){
        $body = app()->getLocale() == 'ar' ? $this->body_ar : $this->body_en;
        return $body;
    }
}
