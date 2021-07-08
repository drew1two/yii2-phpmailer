<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 13.03.21 01:47:18
 */

declare(strict_types = 1);
namespace dicr\tests;

use dicr\phpmailer\PHPMailerMailer;
use dicr\phpmailer\PHPMailerMessage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;
use Yii;

/**
 * Class ScssConverterTest
 */
class PHPMailerTest extends TestCase
{
    /**
     * Тест
     */
    public function testSend(): void
    {
        /** @var PHPMailerMailer $mailer */
        $mailer = Yii::$app->mailer;
        self::assertInstanceOf(PHPMailerMailer::class, $mailer);

        /** @var PHPMailerMessage $message */
        $message = $mailer->compose()
            ->setTo(TO)
            ->setSubject(SUBJ)
            ->setTextBody('Ok');

        self::assertInstanceOf(PHPMailerMessage::class, $message);
        self::assertInstanceOf(PHPMailer::class, $message->transport);
        self::assertSame(CHARSET, $message->transport->CharSet);
        self::assertSame(FROM, $message->from);
        self::assertSame(TO, $message->to);
        self::assertSame(SUBJ, $message->subject);

        $res = $message->send();

        self::assertTrue($res, $message->transport->ErrorInfo);
    }
}
