<?php
/**
 * index.php — главная страница лабораторной работы по ООП.
 * Подключает классы из папки classes/ и выводит результаты заданий.
 */

require_once __DIR__ . '/classes/AbstractClasses.php';
require_once __DIR__ . '/classes/Encapsulation.php';
require_once __DIR__ . '/classes/Interfaces.php';
require_once __DIR__ . '/classes/Inheritance.php';

// ── Задание 1: Абстрактные классы ──
$russian = new RussianHuman('Никита');
$english = new EnglishHuman('John');

// ── Задание 2: Инкапсуляция ──
$cat1 = new Cat('Барсик', 'рыжая');
$cat2 = new Cat('Мурка', 'чёрная');

// ── Задание 3: Интерфейсы ──
$shapes = [
    new Square(5),
    new Circle(3),
    new Rectangle(4, 6),
    new Point(1, 2), // не реализует интерфейс
];

// ── Задание 4: Наследование ──
$paidLesson = new PaidLesson(
    'Урок о наследовании в PHP',
    'Лол, кек, чебурек',
    'Ложитесь спать, утро вечера мудренее',
    99.90
);

// Перехват var_dump() в строку
ob_start();
var_dump($paidLesson);
$dumpOutput = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа — ООП в PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <a class="header-logo" href="#">
        <img src="data:image/png;base64,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" alt="Логотип МосПолитеха">
        <span class="header-logo-text">Московский<br>Политех</span>
    </a>
    <span class="header-title">Лабораторная работа: ООП в PHP</span>
</header>

<main>

    <!-- ЗАДАНИЕ 1 -->
    <section class="task">
        <div class="task-num">01</div>
        <h2>Абстрактные классы</h2>
        <p class="task-desc">Абстрактный класс <code>HumanAbstract</code> и наследники, говорящие на разных языках.</p>
        <div class="output">
            <div class="output-line"><?= $russian->introduceYourself() ?></div>
            <div class="output-line"><?= $english->introduceYourself() ?></div>
        </div>
    </section>

    <!-- ЗАДАНИЕ 2 -->
    <section class="task">
        <div class="task-num">02</div>
        <h2>Инкапсуляция</h2>
        <p class="task-desc">Класс <code>Cat</code> с приватными свойствами и геттерами. Кошка называет имя и цвет.</p>
        <div class="output">
            <div class="output-line"><?= $cat1->sayHello() ?></div>
            <div class="output-line"><?= $cat2->sayHello() ?></div>
            <div class="output-meta">Цвет через геттер: <?= $cat1->getColor() ?>, <?= $cat2->getColor() ?></div>
        </div>
    </section>

    <!-- ЗАДАНИЕ 3 -->
    <section class="task">
        <div class="task-num">03</div>
        <h2>Интерфейсы</h2>
        <p class="task-desc">Интерфейс <code>CalculateSquare</code> и функция <code>get_class()</code> для определения класса.</p>
        <div class="output">
            <?php foreach ($shapes as $shape): ?>
                <?php $implements = $shape instanceof CalculateSquare; ?>
                <div class="output-line <?= $implements ? 'ok' : 'no' ?>">
                    <?= printSquare($shape) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ЗАДАНИЕ 4 -->
    <section class="task">
        <div class="task-num">04</div>
        <h2>Наследование</h2>
        <p class="task-desc">Класс <code>PaidLesson</code> — наследник <code>Lesson</code> со свойством <code>price</code>. Вывод через <code>var_dump()</code>.</p>
        <div class="output">
            <pre class="dump"><?= htmlspecialchars($dumpOutput) ?></pre>
        </div>
    </section>

</main>

<footer>
    <p>Задание для самостоятельной работы</p>
</footer>

</body>
</html>
