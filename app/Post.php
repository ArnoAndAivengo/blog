<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model
{
    use Sluggable;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;

    protected $fillable = ['title', 'content'];

    public function category()
    {
        return $this->hasOne(Category::class);
    }

    public function author()
    {
        return $this->hasOne(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id'
        );
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    // создание поста
    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->user_id = 1;
        $post->save();

        return $post;
    }
    // редактирование поста
    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }
    // удаление поста
    public function remove($fields)
    {
        // удалить картинку поста
        $this->delete();
    }
    // загрука картинки для поста
    public function uploadImage($image)
    {
        if ($image == null) { return;}
        Storage::delete('uploads/' . $this->image);
        $filename = str_random(10) . '.' . $image->extension();
        $image->saveAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }
    // вывод картинки
    public function getImage()
    {
        if ($this->image == null)
        {
            return 'img/no-image.png';
        }
        return '/upload/' . $this->image;
    }

    // установка категории
    public function setCategory($id)
    {
        if($id == null) {return;}
        $this->category_id = $id;
        $this->save();
    }
    // установка тега
    public function setTags($ids)
    {
        if($ids == null) {return;}
        $this->tags()->sync($ids);
    }

    // установка статуса
    public function setDraft()
    {
        $this->status = Post::IS_DRAFT;
        $this->save();
    }
    // установка статуса
    public function setPublic()
    {
        $this->status = Post::IS_PUBLIC;
        $this->save();
    }
    // переключение статуса
    public function toggleStatus($value)
    {
        if($value == null)
        {
            return $this->setDraft();
        }
        return $this->setPublic();
    }

    // установка рекомендовано
    public function setFeatured()
    {
        $this->is_featured = 1;
        $this->save();
    }
    // установка рекомендовано
    public function setStandart()
    {
        $this->is_featured = 0;
        $this->save();
    }
    // переключение рекомендовано
    public function toggleFeatured($value)
    {
        if($value == null)
        {
            return $this->setStandart();
        }
        return $this->setFeatured();
    }

}
