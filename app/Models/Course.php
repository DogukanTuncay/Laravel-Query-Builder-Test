<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
       protected $fillable = ['title', 'description', 'instructor_id', 'category_id', 'level', 'is_published'];

    public static array $allowedFilters = ['title', 'level'];
    public static array $allowedSorts = ['title', 'created_at'];
    public static array $allowedIncludes = ['instructor', 'category'];
    public static array $allowedFields = ['id', 'title', 'description', 'level','is_published', 'instructor_id', 'category_id'];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
