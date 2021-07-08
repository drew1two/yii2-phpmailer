<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 13.03.21 01:46:56
 */

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

/**  */
define('YII_DEBUG', true);
/** */
define('YII_ENV', 'dev');

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

/** @var string */
const CHARSET = 'UTF-8';

/** @var string[] */
const FROM = ['test@dicr.org' => 'PHPUnit'];

/** @var string[] */
const TO = ['develop@dicr.org' => 'Dicr'];

/** @var string */
const SUBJ = 'Yii2 PHPMailer Test';

// приложение
new yii\web\Application([
    'id' => 'test-app',
    'basePath' => __DIR__,
    'components' => [
        'cache' => yii\caching\FileCache::class,

        'request' => [
            'scriptFile' => __FILE__,
            'scriptUrl' => '/',
        ],

        'mailer' => [
            'class' => dicr\phpmailer\PHPMailerMailer::class,
            'transportConfig' => [
                'CharSet' => CHARSET
            ],
            'messageConfig' => [
                'from' => FROM
            ]
        ]
    ]
]);
