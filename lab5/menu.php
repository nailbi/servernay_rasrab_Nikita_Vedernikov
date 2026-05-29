<?php
/**
 * menu.php — модуль меню.
 * Содержит функцию menu() без параметров,
 * возвращающую HTML-строку с навигацией.
 */

function menu(): string
{
    // Текущая страница определяется по GET-параметру 'page
    $page = $_GET['page'] ?? 'view';

    // Пункты главного меню
    $items = [
        'view'   => 'Просмотр',
        'add'    => 'Добавление записи',
        'edit'   => 'Редактирование записи',
        'delete' => 'Удаление записи',
    ];

    $html = '<header>';
    $html .= '<img src="logo.png" alt="logo">';   // логотип (необязательно)
    $html .= '<nav>';

    foreach ($items as $key => $label) {
        $active = ($page === $key) ? ' select' : '';
        $html .= '<a href="index.php?page=' . $key . '" class="' . $active . '">' . $label . '</a>';
    }

    $html .= '</nav>';
    $html .= '</header>';

    // Подменю сортировки — показывается только на странице «Просмотр»
    if ($page === 'view') {
        $sort = $_GET['sort'] ?? 'id';

        $sorts = [
            'id'      => 'По порядку добавления',
            'surname' => 'По фамилии',
            'date'    => 'По дате рождения',
        ];

        $html .= '<div class="submenu">';
        foreach ($sorts as $key => $label) {
            $active = ($sort === $key) ? ' select' : '';
            $html .= '<a href="index.php?page=view&sort=' . $key . '" class="' . $active . '">' . $label . '</a>';
        }
        $html .= '</div>';
    }

    return $html;
}
