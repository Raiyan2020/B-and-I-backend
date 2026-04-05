<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function getValueForKey($key){
        $setting = GeneralSetting::where('key',$key)->first();
        return $setting?$setting->value:null;
    }
}
