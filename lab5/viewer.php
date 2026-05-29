<?php
/**
 * viewer.php — модуль вывода содержимого базы данных.
 * Содержит функцию viewer($sort, $pageNum) с параметрами:
 *   $sort    — тип сортировки ('id' | 'surname' | 'date')
 *   $pageNum — номер страницы пагинации (начиная с 1)
 * Возвращает HTML-строку с таблицей и пагинацией.
 */

function viewer(string $sort = 'id', int $pageNum = 1): string
{
    global $pdo;

    // Допустимые колонки для сортировки (защита от SQL-инъекций)
    $allowedSort = ['id', 'surname', 'date'];
    if (!in_array($sort, $allowedSort)) {
        $sort = 'id';
    }

    $perPage = 10;
    $offset  = ($pageNum - 1) * $perPage;

    // Общее количество записей
    $total    = (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
    $totalPages = (int) ceil($total / $perPage);

    // Записи текущей страницы
    $stmt = $pdo->prepare(
        "SELECT * FROM contacts ORDER BY {$sort} ASC LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    // ── Таблица ──────────────────────────────────────────────
    $html = '<table>';
    $html .= '<thead><tr>
        <th>#</th>
        <th>Фамилия</th>
        <th>Имя</th>
        <th>Отчество</th>
        <th>Пол</th>
        <th>Дата рождения</th>
        <th>Телефон</th>
        <th>Адрес</th>
        <th>E-mail</th>
        <th>Комментарий</th>
    </tr></thead><tbody>';

    if (empty($rows)) {
        $html .= '<tr><td colspan="10">Записей нет</td></tr>';
    } else {
        foreach ($rows as $i => $row) {
            $html .= '<tr>';
            $html .= '<td>' . ($offset + $i + 1) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['surname'])  . '</td>';
            $html .= '<td>' . htmlspecialchars($row['name'])     . '</td>';
            $html .= '<td>' . htmlspecialchars($row['lastname']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['gender'])   . '</td>';
            $html .= '<td>' . htmlspecialchars($row['date'])     . '</td>';
            $html .= '<td>' . htmlspecialchars($row['phone'])    . '</td>';
            $html .= '<td>' . htmlspecialchars($row['location']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['email'])    . '</td>';
            $html .= '<td>' . htmlspecialchars($row['comment'])  . '</td>';
            $html .= '</tr>';
        }
    }

    $html .= '</tbody></table>';

    // ── Пагинация ─────────────────────────────────────────────
    if ($totalPages > 1) {
        $html .= '<div class="pagination">';
        for ($p = 1; $p <= $totalPages; $p++) {
            $active = ($p === $pageNum) ? ' class="select"' : '';
            $html .= '<a href="index.php?page=view&sort=' . $sort . '&p=' . $p . '"' . $active . '>' . $p . '</a>';
        }
        $html .= '</div>';
    }

    return $html;
}
