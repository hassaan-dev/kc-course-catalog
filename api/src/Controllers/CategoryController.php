<?php

namespace App\Controllers;

use App\Models\Category;

class CategoryController {
    /**
     * @return array
     */
    public static function getAllCategories(): array
    {
        return Category::getAll();
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public static function getCategoryById($id): mixed
    {
        return Category::getById($id);
    }
}