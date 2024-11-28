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

function searchPosts($pdo, $searchTerm) {
    $stmt = $pdo->prepare("SELECT title, body FROM posts WHERE title ILIKE :searchTerm OR body ILIKE :searchTerm");
    $stmt->execute([':searchTerm' => '%' . $searchTerm . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = connectToDatabase($host, $db, $user, $pass);

$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = trim($_POST['search']);
    if (strlen($searchTerm) >= 3) {
        $searchResults = searchPosts($pdo, $searchTerm);
    } else {
        echo "Введите минимум 3 символа для поиска.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск постов</title>
</head>
<body>
    <h1>Поиск постов</h1>
    <form method="POST">
        <input type="text" name="search" placeholder="Введите текст для поиска" required>
        <button type="submit">Найти</button>
    </form>

    <div>
        <?php if (!empty($searchResults)): ?>
            <h2>Результаты поиска:</h2>
            <?php foreach ($searchResults as $post): ?>
                <div>
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo htmlspecialchars($post['body']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p>Нет результатов для вашего запроса.</p>
        <?php endif; ?>
    </div>
</body>
</html>
