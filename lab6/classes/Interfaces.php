<?php
/**
 * Задание 3: Интерфейсы
 * Интерфейс CalculateSquare, фигуры его реализующие и не реализующие.
 * Используется get_class() для определения класса объекта.
 */

interface CalculateSquare
{
    public function calculateSquare(): float;
}

// Квадрат — реализует интерфейс
class Square implements CalculateSquare
{
    public function __construct(private float $side) {}

    public function calculateSquare(): float
    {
        return $this->side ** 2;
    }
}

// Круг — реализует интерфейс
class Circle implements CalculateSquare
{
    public function __construct(private float $radius) {}

    public function calculateSquare(): float
    {
        return M_PI * $this->radius ** 2;
    }
}

// Прямоугольник — реализует интерфейс
class Rectangle implements CalculateSquare
{
    public function __construct(private float $width, private float $height) {}

    public function calculateSquare(): float
    {
        return $this->width * $this->height;
    }
}

// Точка — НЕ реализует интерфейс CalculateSquare
class Point
{
    public function __construct(private float $x, private float $y) {}
}

/**
 * Функция выводит площадь, если объект реализует интерфейс,
 * иначе — сообщение об отсутствии реализации.
 * Использует get_class() для получения имени класса.
 */
function printSquare(object $shape): string
{
    $className = get_class($shape); // имя класса объекта

    if ($shape instanceof CalculateSquare) {
        $square = round($shape->calculateSquare(), 2);
        return "Объект класса {$className}: площадь = {$square}.";
    }

    return "Объект класса {$className} не реализует интерфейс CalculateSquare.";
}
