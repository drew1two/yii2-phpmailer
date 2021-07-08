<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 13.03.21 01:55:35
 */

declare(strict_types = 1);
namespace drew1two\phpmailer;

use PHPMailer\PHPMailer\PHPMailer;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;

use function array_merge;

/**
 * Postal service using PHPMailer as a transport.
 */
class PHPMailerMailer extends BaseMailer
{
    /** @inheritDoc */
    public $messageClass = PHPMailerMessage::class;

    /** @var array Config PHPMailer */
    public $transportConfig = [];

    /**
     * Creates transport.
     *
     * @return PHPMailer
     * @throws InvalidConfigException
     */
    protected function createTransport(): PHPMailer
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(array_merge([
            'class' => PHPMailer::class
        ], $this->transportConfig ?: []));
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    protected function createMessage(): PHPMailerMessage
    {
        /** @var PHPMailerMessage $msg first we create a message with transport (before initialization) */
        $msg = Yii::createObject(array_merge([
            'class' => $this->messageClass,
            'mailer' => $this,
            'transport' => $this->createTransport()
        ]));

        // we initialize the message already with the transport
        if (! empty($this->messageConfig)) {
            Yii::configure($msg, $this->messageConfig);
        }

        return $msg;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function sendMessage($message): bool
    {
        if (! $message instanceof PHPMailerMessage) {
            throw new InvalidArgumentException('Unsupported message type');
        }

        try {
            return $message->transport->send();
        } catch (\PHPMailer\PHPMailer\Exception $ex) {
            throw new Exception('Send error', 0, $ex);
        }
    }
}
