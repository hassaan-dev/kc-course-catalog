<?php

namespace App\Controllers;

use App\Models\Course;

class CourseController {
    public static function getAllCourses($category_id = null) {
        return Course::getAll($category_id);
    }

    public static function getCourseById($id) {
        return Course::getById($id);
    }
}