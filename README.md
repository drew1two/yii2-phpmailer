This is not mine.  It's only here so I could translate it from Russian to English.

Original can be found here:- https://gitlab.com/dicr/yii2-phpmailer

# PHPMailer adapter for Yii2

Mail service for Yii2 using as transport [PHPMailer](https://github.com/PHPMailer/PHPMailer).

Unlike standard SwiftMailer, it supports sending by php function method mail.

## Customization

```php
 $config = [
     'components' => [
        'mailer' => [
            'class' => dicr\phpmailer\PHPMailerMailer::class,
            
            // config \PHPMailer\PHPMailer\PHPMailer
            'transportConfig' => [
                'CharSet' => CHARSET
            ],
            
            // default message config
            'messageConfig' => [
                'from' => FROM
            ]
        ]
    ]
];
```
