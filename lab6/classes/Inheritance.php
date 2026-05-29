<?php
/**
 * Задание 4: Наследование
 * Базовый класс Lesson и наследник PaidLesson со свойством price.
 */

class Lesson
{
    public function __construct(
        protected string $title,
        protected string $text,
        protected string $homework
    ) {}

    public function getTitle(): string    { return $this->title; }
    public function getText(): string     { return $this->text; }
    public function getHomework(): string { return $this->homework; }
}

class PaidLesson extends Lesson
{
    private float $price; // цена

    public function __construct(string $title, string $text, string $homework, float $price)
    {
        parent::__construct($title, $text, $homework);
        $this->price = $price; // устанавливается через конструктор
    }

    // Геттер
    public function getPrice(): float
    {
        return $this->price;
    }

    // Сеттер
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
