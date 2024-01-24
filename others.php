<?php

namespace NW\WebService\References\Operations\Notification;

/**
 * @property Seller $Seller
 */
//TODO: один класс один файл.
//TODO: имя класса совпадает с названием файла
//TODO: общий стиль нотация в имени файлов Others.php, ReturnOperation.php
class Contractor
{
    const TYPE_CUSTOMER = 0; //TODO: тип для наследников должен переопределяться. сейчас для каждого наследника тип == 0
    public $id; //TODO: требуется тип
    public $type; //TODO: требуется тип
    public $name; //TODO: требуется тип

    public static function getById(int $resellerId): self
    {
        return new self($resellerId); // fakes the getById method
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }
}
//TODO: один класс один файл.
class Seller extends Contractor
{
}
//TODO: один класс один файл.
class Employee extends Contractor
{
}
//TODO: один класс один файл.
//TODO: класс может быть оформлен как класс перечисление
class Status
{
    public $id, $name; //TODO: необходим тип, но в данном случае типы разнве, поэтмоу каждая переменная на новой строке int, string. д

    public static function getName(int $id): string
    {
        $a = [
            0 => 'Completed',
            1 => 'Pending',
            2 => 'Rejected',
        ];

        return $a[$id];
    }
}
//TODO: один класс один файл.
abstract class ReferencesOperation
{
    abstract public function doOperation(): array;

    public function getRequest($pName)
    {
        return $_REQUEST[$pName]; // TODO: работаем только по описанному списку полей. ни каких пробросов массивов как есть. передаем только необходимые параметры key => value
    }
}
//TODO: требуется перенести класс в class Seller
function getResellerEmailFrom()
{
    return 'contractor@example.com';
}
//TODO: требуется перенести класс в class Seller
function getEmailsByPermit($resellerId, $event)
{
    // fakes the method
    return ['someemeil@example.com', 'someemeil2@example.com'];
}

//TODO: один класс-перечисление один файл.
class NotificationEvents
{
    const CHANGE_RETURN_STATUS = 'changeReturnStatus';
    const NEW_RETURN_STATUS    = 'newReturnStatus';
}

/*
╲╭━━━━╮╱╭━━━━━━╮
╲┃┏╮╭┓┃╱┃ⒷⓄⓄⓄⓄⒽ
╭╯╰╯╰╯╰╮╰━╮╭━━━╯
╰╮╭━━╮╭╯┈┈┻╯╲╲╲╲
╱┃╰━━╯╰━╮╲╲╲╲╲╲╲
╱╰━━━━━━╯╲╲╲╲╲╲╲