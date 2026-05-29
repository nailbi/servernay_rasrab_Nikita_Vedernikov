<?php
/**
 * add.php — модуль добавления новой записи в базу данных.
 * Содержит HTML-форму и PHP-код для сохранения записи.
 */

$message = '';

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button'])) {

    // Простая валидация: фамилия и имя обязательны
    if (empty(trim($_POST['surname'])) || empty(trim($_POST['name']))) {
        $message = '<p class="error">Ошибка: заполните обязательные поля (Фамилия, Имя).</p>';
    } else {
        try {
            global $pdo;

            $stmt = $pdo->prepare(
                'INSERT INTO contacts (surname, name, lastname, gender, date, phone, location, email, comment)
                 VALUES (:surname, :name, :lastname, :gender, :date, :phone, :location, :email, :comment)'
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
            ]);

            $message = '<p class="success">Запись добавлена успешно.</p>';

        } catch (Exception $e) {
            $message = '<p class="error">Ошибка: запись не добавлена. ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}

// Пустые значения для формы после добавления
$row    = [];
$button = 'Добавить';
?>

<?= $message ?>

<form name="form_add" method="post">
    <div class="column">
        <div class="add">
            <label>Фамилия *</label>
            <input type="text" name="surname" placeholder="Фамилия" value="">
        </div>
        <div class="add">
            <label>Имя *</label>
            <input type="text" name="name" placeholder="Имя" value="">
        </div>
        <div class="add">
            <label>Отчество</label>
            <input type="text" name="lastname" placeholder="Отчество" value="">
        </div>
        <div class="add">
            <label>Пол</label>
            <select name="gender">
                <option value="">— не указан —</option>
                <option value="мужской">мужской</option>
                <option value="женский">женский</option>
            </select>
        </div>
        <div class="add">
            <label>Дата рождения</label>
            <input type="date" name="date" value="">
        </div>
        <div class="add">
            <label>Телефон</label>
            <input type="text" name="phone" placeholder="Телефон" value="">
        </div>
        <div class="add">
            <label>Адрес</label>
            <input type="text" name="location" placeholder="Адрес" value="">
        </div>
        <div class="add">
            <label>Email</label>
            <input type="email" name="email" placeholder="Email" value="">
        </div>
        <div class="add">
            <label>Комментарий</label>
            <textarea name="comment" placeholder="Краткий комментарий"></textarea>
        </div>

        <button type="submit" name="button" value="Добавить" class="form-btn">Добавить</button>
    </div>
</form>
