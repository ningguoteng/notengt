<?php


namespace app\model;

use think\Model;

class Type extends Model
{
    protected $autoWriteTimestamp = 'create_time';

    public function contents()
    {
        return $this->hasMany(Content::class,'type_id');
    }
}