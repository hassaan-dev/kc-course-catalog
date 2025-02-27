<?php

namespace App\Controllers;

use App\Models\Course;

class CourseController {
    /**
     * @param $category_id
     * @return array
     */
    public static function getAllCourses($category_id = null): array
    {
        return Course::getAll($category_id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getCourseById($id): mixed
    {
        return Course::getById($id);
    }
}