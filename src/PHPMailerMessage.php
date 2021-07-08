<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 13.03.21 01:49:28
 */

declare(strict_types = 1);
namespace dicr\phpmailer;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use yii\base\InvalidConfigException;
use yii\mail\BaseMessage;

use function array_shift;
use function is_numeric;
use function md5;
use function mt_rand;

/**
 * Сообщение.
 *
 * @property array $to
 * @property array $from
 * @property-write mixed $textBody
 * @property array $replyTo
 * @property null|string $subject
 * @property-write mixed $htmlBody
 * @property array $bcc
 * @property string $charset
 * @property array $cc
 * @property PHPMailerMailer $mailer
 */
class PHPMailerMessage extends BaseMessage
{
    /** @var PHPMailer */
    public $transport;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if (! $this->mailer instanceof PHPMailerMailer) {
            throw new InvalidConfigException('mailer');
        }

        if (! $this->transport instanceof PHPMailer) {
            throw new InvalidConfigException('transport');
        }
    }

    /**
     * @inheritDoc
     */
    public function getCharset(): string
    {
        return $this->transport->CharSet;
    }

    /**
     * @inheritDoc
     */
    public function setCharset($charset): self
    {
        $this->transport->CharSet = $charset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFrom()
    {
        return static::formatAddr([
            [$this->transport->From, $this->transport->FromName]
        ]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setFrom($from): self
    {
        foreach (static::normalizeAddr($from) as $email => $name) {
            $this->transport->setFrom($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTo(): array
    {
        return static::formatAddr($this->transport->getToAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setTo($to): self
    {
        $this->transport->clearAddresses();

        foreach (static::normalizeAddr($to) as $email => $name) {
            $this->transport->addAddress($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getReplyTo(): array
    {
        return static::formatAddr($this->transport->getReplyToAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setReplyTo($replyTo): self
    {
        $this->transport->clearReplyTos();

        foreach (static::normalizeAddr($replyTo) as $email => $name) {
            $this->transport->addReplyTo($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCc(): array
    {
        return static::formatAddr($this->transport->getCcAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setCc($cc): self
    {
        $this->transport->clearCCs();

        foreach (static::normalizeAddr($cc) as $email => $name) {
            $this->transport->addCC($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBcc(): array
    {
        return static::formatAddr($this->transport->getBccAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setBcc($bcc): self
    {
        $this->transport->clearBCCs();

        foreach (static::normalizeAddr($bcc) as $email => $name) {
            $this->transport->addBCC($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): ?string
    {
        return $this->transport->Subject;
    }

    /**
     * @inheritDoc
     */
    public function setSubject($subject): self
    {
        $this->transport->Subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTextBody($text): self
    {
        if (empty($this->transport->Body)) {
            $this->transport->Body = $text;
            $this->transport->isHTML(false);
        } else {
            $this->transport->AltBody = $text;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHtmlBody($html)
    {
        if (! empty($this->transport->Body)) {
            $this->transport->AltBody = $this->transport->Body;
        }

        $this->transport->Body = $html;
        $this->transport->isHTML();

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function attach($fileName, array $options = []): self
    {
        $this->transport->addAttachment(
            $fileName,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function attachContent($content, array $options = []): self
    {
        $this->transport->addStringAttachment(
            $content,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function embed($fileName, array $options = []): string
    {
        $cid = md5((string)mt_rand());

        $this->transport->addEmbeddedImage(
            $fileName,
            $cid,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $cid;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function embedContent($content, array $options = []): string
    {
        $cid = md5((string)mt_rand());

        $this->transport->addStringEmbeddedImage(
            $content,
            $cid,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $cid;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function toString(): string
    {
        return $this->transport->createHeader() . "\n" . $this->transport->createBody();
    }

    /**
     * Нормализует адрес.
     *
     * @param string|array $addr формат адреса в Yii
     * @return array нормализованный email => $name
     */
    private static function normalizeAddr($addr): array
    {
        $res = [];

        foreach ($addr as $key => $val) {
            if (is_numeric($key)) {
                $res[$val] = '';
            } else {
                $res[$key] = $val;
            }
        }

        return $res;
    }

    /**
     * Форматирует адрес PHPMailer в формат Yii.
     *
     * @param array $addr массив пар [$email, $name]
     * @return array адрес в формате Yii email => name или [email]
     */
    private static function formatAddr(array $addr): array
    {
        $res = [];

        foreach ($addr as $pair) {
            $email = (string)array_shift($pair);
            $name = (string)array_shift($pair);

            if (empty($name)) {
                $res[] = $email;
            } else {
                $res[$email] = $name;
            }
        }

        return $res;
    }
}
