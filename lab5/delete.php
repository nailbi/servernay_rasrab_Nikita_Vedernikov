<?php
/**
 * delete.php — модуль удаления записи из базы данных.
 * Выводит список ссылок с именами контактов.
 * При переходе по ссылке запись удаляется и выводится сообщение.
 */

global $pdo;

$message = '';

// ── Удаление записи ───────────────────────────────────────────
if (isset($_GET['delete_id'])) {
    $deleteId = (int) $_GET['delete_id'];

    try {
        // Получаем фамилию перед удалением для сообщения
        $stmt = $pdo->prepare('SELECT surname FROM contacts WHERE id = :id');
        $stmt->execute([':id' => $deleteId]);
        $deleted = $stmt->fetch();

        if ($deleted) {
            $pdo->prepare('DELETE FROM contacts WHERE id = :id')
                ->execute([':id' => $deleteId]);

            $message = '<p class="success">Запись с фамилией «'
                . htmlspecialchars($deleted['surname'])
                . '» удалена.</p>';
        } else {
            $message = '<p class="error">Запись не найдена.</p>';
        }
    } catch (Exception $e) {
        $message = '<p class="error">Ошибка при удалении: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

// ── Список контактов ──────────────────────────────────────────
$contacts = $pdo->query(
    'SELECT id, surname, name, lastname FROM contacts ORDER BY surname, name'
)->fetchAll();
?>

<?= $message ?>

<main>
    <h2>Выберите запись для удаления:</h2>

    <?php if (empty($contacts)): ?>
        <p>Нет записей для удаления.</p>
    <?php else: ?>
        <ul class="delete-list">
            <?php foreach ($contacts as $c): ?>
                <li>
                    <a href="index.php?page=delete&delete_id=<?= $c['id'] ?>"
                       onclick="return confirm('Удалить запись «<?= htmlspecialchars($c['surname']) ?>»?')">
                        <?= htmlspecialchars($c['surname'] . ' '
                            . mb_substr($c['name'], 0, 1) . '. '
                            . ($c['lastname'] ? mb_substr($c['lastname'], 0, 1) . '.' : '')) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
