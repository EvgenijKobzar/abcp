### Тестовое задание для разработчика на PHP
Мы ожидаем, что Вы:
* найдете и исправите все возможные ошибки (синтаксические, проектирования, безопасности и т.д.);
* отрефакторите код файла `ReturnOperation.php` в лучший по вашему мнению вид;
* напишите в комментарии краткое резюме по коду: назначение кода и его качество.

### Комментарий к заданию
* Найденные ошибки отмечены, как `todo` в файлах `ReturnOperation.php` и `others.php`
* предложен один из вариантов рефакторинга `handler_test.php`
* Назначение кода - представлен обработчик возврата (если position это возврат) и рассылки уведомлений сотрудникам и клиенту.
* * Если поступил новый возврат, сотрудники, которые имеют права доступа, получают уведомления по почте.
Далее они получают уведомление на почту при смене статуса. 
* * Клиенты получают уведомления о смене статуса возврата только при его изменении. При наличии телефона получают push уведомление.
* Качество - в данном виде обработчик содержит критичные ошибки: синтаксические ошибки, ошибки безопасности (отсутствие проверки подписи токена, время жизни токена, работа с полями из REQUEST без белых списков и не всегда выполненная интернализация параметров), ошибки производительности (в цикле отправка уведомлений и отсутствие очередей) 
Написан в процедурном стиле, что со временем может создать дополнительные накладные расходы при развитии архитектуры 
