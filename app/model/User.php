<?php


namespace app\model;

use think\Model;

class User extends Model
{
    protected $autoWriteTimestamp = 'create_time';
    public function types()
    {
        return $this->hasMany(Type::class);
    }



}