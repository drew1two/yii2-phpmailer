<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 13.03.21 01:55:35
 */

declare(strict_types = 1);
namespace dicr\phpmailer;

use PHPMailer\PHPMailer\PHPMailer;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;

use function array_merge;

/**
 * Почтовый сервис, использующий PHPMailer в качестве транспорта.
 */
class PHPMailerMailer extends BaseMailer
{
    /** @inheritDoc */
    public $messageClass = PHPMailerMessage::class;

    /** @var array Конфиг PHPMailer */
    public $transportConfig = [];

    /**
     * Создает транспорт.
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
        /** @var PHPMailerMessage $msg сначала создаем сообщение с транспортом (до инициализации) */
        $msg = Yii::createObject(array_merge([
            'class' => $this->messageClass,
            'mailer' => $this,
            'transport' => $this->createTransport()
        ]));

        // инициализируем сообщение уже с транспортом
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
            throw new InvalidArgumentException('Не поддерживаемый тип сообщений');
        }

        try {
            return $message->transport->send();
        } catch (\PHPMailer\PHPMailer\Exception $ex) {
            throw new Exception('Ошибка отправки', 0, $ex);
        }
    }
}
