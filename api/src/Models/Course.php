<?php

namespace App\Models;

use App\Database\Database;
use PDO;

class Course {
    public static function getAll($category_id = null) {
        $pdo = Database::getInstance();

        // Fetch all categories for hierarchical lookup
        $categories = $pdo->query("SELECT id, name, parent_id FROM categories")->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all courses with their direct category
        $sql = "SELECT courses.*, categories.id AS category_id, categories.name AS category_name
                FROM courses 
                JOIN categories ON courses.category_id = categories.id";

        if ($category_id) {
            $sql .= " WHERE courses.category_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id]);
        } else {
            $stmt = $pdo->query($sql);
        }

        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assign main category name for each course
        foreach ($courses as &$course) {
            $course['main_category_name'] = self::getMainCategoryName($course['category_id'], $categories);
        }

        return $courses;
    }

    public static function getById($id) {
        $pdo = Database::getInstance();

        // Fetch all categories for hierarchical lookup
        $categories = $pdo->query("SELECT id, name, parent_id FROM categories")->fetchAll(PDO::FETCH_ASSOC);

        // Fetch a single course
        $stmt = $pdo->prepare("SELECT courses.*, categories.id AS category_id, categories.name AS category_name
                               FROM courses 
                               JOIN categories ON courses.category_id = categories.id 
                               WHERE courses.id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course) {
            $course['main_category_name'] = self::getMainCategoryName($course['category_id'], $categories);
        }

        return $course;
    }

    private static function getMainCategoryName($categoryId, $categories) {
        while ($categoryId) {
            foreach ($categories as $category) {
                if ($category['id'] == $categoryId) {
                    if ($category['parent_id'] === null) {
                        return $category['name']; // Found top-level category
                    }
                    $categoryId = $category['parent_id'];
                    break;
                }
            }
        }
        return "Unknown"; // Fallback if category not found
    }
}