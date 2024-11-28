<?php

$host = 'localhost';
$db = '';
$user = '';
$pass = '';

function connectToDatabase($host, $db, $user, $pass) {
    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }
}

function insertUser($pdo, $id, $name, $username, $email) {
    $stmt = $pdo->prepare("INSERT INTO users (id, name, username, email) VALUES (:id, :name, :username, :email)");
    $stmt->execute([':id' => $id, ':name' => $name, ':username' => $username, ':email' => $email]);
}

function insertPost($pdo, $id, $userId, $title, $body) {
    $stmt = $pdo->prepare("INSERT INTO posts (id, userId, title, body) VALUES (:id, :userId, :title, :body)");
    $stmt->execute([':id' => $id, ':userId' => $userId, ':title' => $title, ':body' => $body]);
}

function loadUsers($pdo, $url) {
    $usersJson = file_get_contents($url);
    $users = json_decode($usersJson, true);

    if (!is_array($users)) {
        die("Ошибка: некорректный JSON-формат для пользователей.");
    }

    foreach ($users as $user) {
        if (isset($user['id'], $user['name'], $user['username'], $user['email'])) {
            insertUser($pdo, $user['id'], $user['name'], $user['username'], $user['email']);
        } else {
            echo "Пропущена запись пользователя: " . json_encode($user) . "\n";
        }
    }

    return count($users);
}

function loadPosts($pdo, $url) {
    $postsJson = file_get_contents($url);
    $posts = json_decode($postsJson, true);

    if (!is_array($posts)) {
        die("Ошибка: некорректный JSON-формат для записей.");
    }

    foreach ($posts as $post) {
        if (isset($post['id'], $post['userId'], $post['title'], $post['body'])) {
            insertPost($pdo, $post['id'], $post['userId'], $post['title'], $post['body']);
        } else {
            echo "Пропущена запись поста: " . json_encode($post) . "\n";
        }
    }

    return count($posts);
}

$pdo = connectToDatabase($host, $db, $user, $pass);

$usersUrl = 'https://jsonplaceholder.typicode.com/users';
$postsUrl = 'https://jsonplaceholder.typicode.com/posts';

$userCount = loadUsers($pdo, $usersUrl);
$postCount = loadPosts($pdo, $postsUrl);

echo "Загружено $userCount пользователей и $postCount записей.\n";
?>
