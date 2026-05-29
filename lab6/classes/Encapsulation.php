<?php
/**
 * Задание 2: Инкапсуляция
 * Класс Cat с приватными свойствами name и color, геттерами и методом sayHello().
 */

class Cat
{
    private string $name;
    private string $color; // приватное свойство

    public function __construct(string $name, string $color)
    {
        $this->name  = $name;
        $this->color = $color; // задаётся через конструктор
    }

    public function getName(): string
    {
        return $this->name;
    }

    // Геттер для приватного свойства color
    public function getColor(): string
    {
        return $this->color;
    }

    // Кошка называет имя и свой цвет
    public function sayHello(): string
    {
        return 'Меня зовут ' . $this->name . ', и я ' . $this->color . ' кошка.';
    }
}
