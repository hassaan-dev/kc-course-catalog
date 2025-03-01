<?php

namespace App\Models;

use App\Database\Database;
use PDO;

class Category {
    /**
     * @return array
     */
    public static function getAll(): array
    {
        $pdo = Database::getInstance()->getConnection();

        // Fetch all categories
        $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

        // Calculate course count for each category including child categories
        foreach ($categories as &$category) {
            $category['count_of_courses'] = self::countCoursesIncludingChildren($category['id'], $categories, $pdo);
        }

        return $categories;
    }

    private static function countCoursesIncludingChildren($categoryId, $categories, $pdo): mixed
    {
        $count = 0;

        // Count courses directly in this category
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $count += $stmt->fetchColumn();

        // Recursively count courses in child categories
        foreach ($categories as $cat) {
            if ($cat['parent_id'] == $categoryId) {
                $count += self::countCoursesIncludingChildren($cat['id'], $categories, $pdo);
            }
        }

        return $count;
    }

    public static function getById($id): mixed
    {
        $pdo = Database::getInstance()->getConnection();
        $categories = self::getAll();

        foreach ($categories as $category) {
            if ($category['id'] == $id) {
                return $category;
            }
        }

        return null;
    }
}