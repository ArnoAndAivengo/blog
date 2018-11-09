<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

    // Добавить подписчика
    public static function add($email)
    {
        $sub = new staitc; // создание нового текущего класса
        $sub->email = $email;
        $sub->token = str_random(100);
        $sub->save();

        return $sub;
    }

    // Удалить подписчика
    public function remove()
    {
        $this->delete();
    }
}
