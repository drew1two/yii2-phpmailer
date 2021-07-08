# PHPMailer адаптер для Yii2

Почтовый сервис для Yii2, использующий в качестве транспорта [PHPMailer](https://github.com/PHPMailer/PHPMailer).

В отличие от стандартного SwiftMailer, поддерживает отправку методом php-функции mail.

## Настройка

```php
 $config = [
     'components' => [
        'mailer' => [
            'class' => dicr\phpmailer\PHPMailerMailer::class,
            
            // конфиг \PHPMailer\PHPMailer\PHPMailer
            'transportConfig' => [
                'CharSet' => CHARSET
            ],
            
            // конфиг сообщения по-умолчанию
            'messageConfig' => [
                'from' => FROM
            ]
        ]
    ]
];
```
