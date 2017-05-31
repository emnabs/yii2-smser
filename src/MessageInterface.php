<?php

namespace emhome\smser\src;

/**
 * MessageInterface is the interface that should be implemented by sms message classes.
 *
 * A message represents the settings and content of an email, such as the sender, recipient,
 * subject, body, etc.
 * @see SmserInterface
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
interface MessageInterface {

    /**
     * Returns the message recipient(s).
     * @return array the message recipients
     */
    public function getMobile();

    /**
     * Sets the message recipient(s).
     * @param string $mobile message receiver mobile number.
     * @return $this self reference.
     */
    public function setMobile($mobile);

    /**
     * Returns the message signature.
     * @return string the message signature
     */
    public function getSignature();

    /**
     * Sets the message signature.
     * @param string $signature message signature
     * @return $this self reference.
     */
    public function setSignature($signature);

    /**
     * Returns the message content.
     * @return string the message content
     */
    public function getContent();

    /**
     * Sets the message plain text content.
     * @param string $content message plain text content.
     * @return $this self reference.
     */
    public function setContent($content);

    /**
     * Sends this sms message.
     * @param SmserInterface $smser the smser that should be used to send this message.
     */
    public function send(SmserInterface $smser = null);
}
