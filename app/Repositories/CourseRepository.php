<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;

class CourseRepository extends BaseRepository
{
    public function __construct(Course $model, Request $request)
    {
        parent::__construct($model, $request);
    }

    // Özel sorgular burada yer alabilir

}
