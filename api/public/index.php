<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\CategoryController;
use App\Controllers\CourseController;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

if ($request_uri === '/categories') {
    echo json_encode(CategoryController::getAllCategories());
} elseif (preg_match('/\/categories\/(.+)/', $request_uri, $matches)) {
    echo json_encode(CategoryController::getCategoryById($matches[1]));
} elseif ($request_uri === '/courses') {
    echo json_encode(CourseController::getAllCourses($_GET['category_id'] ?? null));
} elseif (preg_match('/\/courses\/(.+)/', $request_uri, $matches)) {
    echo json_encode(CourseController::getCourseById($matches[1]));
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}