<?php
/**
 * index.php — единственный загружаемый в браузер документ.
 * Осуществляет всю маршрутизацию сайта.
 */

// ── Подключение базы данных ───────────────────────────────────
require_once 'db.php';

// ── Подключение модулей ───────────────────────────────────────
require_once 'menu.php';
require_once 'viewer.php';

// ── Определение текущей страницы ──────────────────────────────
$page = $_GET['page'] ?? 'view';

// Защита: допустимые страницы
$allowedPages = ['view', 'add', 'edit', 'delete'];
if (!in_array($page, $allowedPages)) {
    $page = 'view';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Записная книжка</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lemonada:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Дополнительные стили для пагинации и списка удаления */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin: 20px auto;
        }
        .pagination a {
            display: inline-block;
            padding: 6px 12px;
            border: 2px solid transparent;
            border-radius: 4px;
            font-family: sans-serif;
            font-size: 15px;
            color: var(--color_a);
            background: var(--back_a);
            text-decoration: none;
            transition: .2s;
        }
        .pagination a:hover,
        .pagination a.select {
            border-color: var(--color_a);
            background: rgba(92,103,168,0.8);
        }
        .delete-list {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
            margin-top: 16px;
        }
        .delete-list a {
            font-size: 17px;
            padding: 6px 18px;
            background: var(--back_a);
            border-radius: 5px;
            color: var(--color_a);
            transition: .2s;
        }
        .delete-list a:hover {
            background: rgba(92,103,168,0.8);
        }
        .div-edit a {
            display: block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            color: var(--color_a);
            text-decoration: none;
        }
        .div-edit a:hover { background: var(--back_a); }
        .div-edit a.currentRow {
            background: var(--back_a);
            font-weight: bold;
            border-left: 3px solid var(--color_a);
        }
        .success {
            padding: 10px 18px;
            border-radius: 5px;
            margin: 12px auto;
            max-width: 500px;
            text-align: center;
        }
        .error {
            padding: 10px 18px;
            border-radius: 5px;
            margin: 12px auto;
            max-width: 500px;
            text-align: center;
        }
        header nav { display: flex; gap: 4px; }
        header a.select { color: red; }
        h2 { margin: 16px 0 8px; font-size: 18px; color: var(--color_a); }
    </style>
</head>
<body>

<?php
// ── Меню (всегда вверху) ──────────────────────────────────────
echo menu();
?>

<main>
<?php
// ── Роутинг ───────────────────────────────────────────────────
switch ($page) {

    // ── ПРОСМОТР ──────────────────────────────────────────────
    case 'view':
        $sort    = $_GET['sort'] ?? 'id';
        $pageNum = max(1, (int) ($_GET['p'] ?? 1));
        echo viewer($sort, $pageNum);
        break;

    // ── ДОБАВЛЕНИЕ ────────────────────────────────────────────
    case 'add':
        require 'add.php';
        break;

    // ── РЕДАКТИРОВАНИЕ ────────────────────────────────────────
    case 'edit':
        require 'edit.php';
        break;

    // ── УДАЛЕНИЕ ──────────────────────────────────────────────
    case 'delete':
        require 'delete.php';
        break;
}
?>
</main>

<footer></footer>

</body>
</html>
