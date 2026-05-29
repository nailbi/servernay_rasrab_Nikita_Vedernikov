<?php
/**
 * edit.php — модуль редактирования существующей записи.
 * Выводит список контактов для выбора, затем форму редактирования.
 */

global $pdo;

$message = '';

// ── Сохранение изменений ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button'], $_POST['id'])) {
    $id = (int) $_POST['id'];

    if (empty(trim($_POST['surname'])) || empty(trim($_POST['name']))) {
        $message = '<p class="error">Ошибка: заполните обязательные поля (Фамилия, Имя).</p>';
    } else {
        try {
            $stmt = $pdo->prepare(
                'UPDATE contacts
                 SET surname=:surname, name=:name, lastname=:lastname, gender=:gender,
                     date=:date, phone=:phone, location=:location, email=:email, comment=:comment
                 WHERE id=:id'
            );
            $stmt->execute([
                ':surname'  => trim($_POST['surname']),
                ':name'     => trim($_POST['name']),
                ':lastname' => trim($_POST['lastname'] ?? ''),
                ':gender'   => $_POST['gender'] ?? '',
                ':date'     => $_POST['date']    ?? null,
                ':phone'    => trim($_POST['phone']    ?? ''),
                ':location' => trim($_POST['location'] ?? ''),
                ':email'    => trim($_POST['email']    ?? ''),
                ':comment'  => trim($_POST['comment']  ?? ''),
                ':id'       => $id,
            ]);
            $message = '<p class="success">Запись успешно обновлена.</p>';
        } catch (Exception $e) {
            $message = '<p class="error">Ошибка при обновлении: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}

// ── Список контактов для выбора ───────────────────────────────
$contacts = $pdo->query(
    'SELECT id, surname, name, lastname FROM contacts ORDER BY surname, name'
)->fetchAll();

// Текущая выбранная запись: из POST (после сохранения) или из GET
$currentId = (int) ($_POST['id'] ?? $_GET['id'] ?? ($contacts[0]['id'] ?? 0));

// ── Загрузка данных выбранной записи ─────────────────────────
$row = [];
if ($currentId) {
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = :id');
    $stmt->execute([':id' => $currentId]);
    $row = $stmt->fetch() ?: [];
}

$button = 'Сохранить';
?>

<?= $message ?>

<!-- Список контактов для выбора -->
<div class="div-edit">
    <?php foreach ($contacts as $c): ?>
        <?php $active = ((int)$c['id'] === $currentId) ? ' currentRow' : ''; ?>
        <a class="<?= $active ?>"
           href="index.php?page=edit&id=<?= $c['id'] ?>">
            <?= htmlspecialchars($c['surname'] . ' ' . $c['name'] . ' ' . $c['lastname']) ?>
        </a><br>
    <?php endforeach; ?>
</div>

<!-- Форма редактирования -->
<?php if ($row): ?>
<form name="form_edit" method="post">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <div class="column">
        <div class="add">
            <label>Фамилия *</label>
            <input type="text" name="surname" placeholder="Фамилия"
                   value="<?= htmlspecialchars($row['surname']) ?>">
        </div>
        <div class="add">
            <label>Имя *</label>
            <input type="text" name="name" placeholder="Имя"
                   value="<?= htmlspecialchars($row['name']) ?>">
        </div>
        <div class="add">
            <label>Отчество</label>
            <input type="text" name="lastname" placeholder="Отчество"
                   value="<?= htmlspecialchars($row['lastname']) ?>">
        </div>
        <div class="add">
            <label>Пол</label>
            <select name="gender">
                <option value="">— не указан —</option>
                <option value="мужской"  <?= $row['gender']==='мужской'  ? 'selected':'' ?>>мужской</option>
                <option value="женский"  <?= $row['gender']==='женский'  ? 'selected':'' ?>>женский</option>
            </select>
        </div>
        <div class="add">
            <label>Дата рождения</label>
            <input type="date" name="date" value="<?= htmlspecialchars($row['date'] ?? '') ?>">
        </div>
        <div class="add">
            <label>Телефон</label>
            <input type="text" name="phone" placeholder="Телефон"
                   value="<?= htmlspecialchars($row['phone']) ?>">
        </div>
        <div class="add">
            <label>Адрес</label>
            <input type="text" name="location" placeholder="Адрес"
                   value="<?= htmlspecialchars($row['location']) ?>">
        </div>
        <div class="add">
            <label>Email</label>
            <input type="email" name="email" placeholder="Email"
                   value="<?= htmlspecialchars($row['email']) ?>">
        </div>
        <div class="add">
            <label>Комментарий</label>
            <textarea name="comment" placeholder="Краткий комментарий"><?= htmlspecialchars($row['comment']) ?></textarea>
        </div>

        <button type="submit" name="button" value="<?= $button ?>" class="form-btn"><?= $button ?></button>
    </div>
</form>
<?php else: ?>
    <p>Нет доступных записей для редактирования.</p>
<?php endif; ?>
