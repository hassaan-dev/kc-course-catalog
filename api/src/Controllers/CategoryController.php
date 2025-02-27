<?php

namespace App\Controllers;

use App\Models\Category;

class CategoryController {
    public static function getAllCategories() {
        return Category::getAll();
    }

    public static function getCategoryById($id) {
        return Category::getById($id);
    }
}