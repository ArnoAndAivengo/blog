<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const IS_BANNED = 1;
    const IS_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Создание пользователя
    public static function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->password = bcrypt($fields['password']);
        $user->save();
        return $user;
    }
    // Редактирование пользователя
    public function edit($fields)
    {
        $this->fill($fields);
        $this->password = bcrypt($fields['password']);
        $this->save();
    }
    // Удаление пользователя
    public function remove()
    {
        Storage::delete('uploads/' . $this->image);
        $this->delete();
    }
    // загрука Аватара для пользователя
    public function uploadAvatar($image)
    {
        if ($image == null) { return;}

        Storage::delete('uploads/' . $this->image);
        $filename = str_random(10) . '.' . $image->extension();
        $image->saveAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }
    // дефолтная Аватара для пользователя
    public function getImage()
    {
        if ($this->image == null)
        {
            return '/img/no-user-image.png';
        }

        return '/uploads/' . $this->image;
    }
    // присвоить статус админа
    public function makeAdmin()
    {
       $this->is_admin = 1;
    }
    // присвоить статус пользователя
    public function makeNormal()
    {
        $this->is_admin = 0;
    }
    // переключатель статусов статус пользователя
    public function toggleAdmin($value)
    {
        if ($value == null)
        {
            return $this->makeNormal();
        }

        return $this->makeAdmin();
    }

    // присвоить статус админа
    public function ban()
    {
        $this->status = User::IS_BANNED;
        $this->save();
    }
    // присвоить статус пользователя
    public function unban()
    {
        $this->status = User::IS_ACTIVE;
        $this->save();
    }
    // переключатель статусов статус пользователя
    public function toggleBan($value)
    {
        if ($value == null)
        {
            return $this->unban();
        }

        return $this->ban();
    }




}
