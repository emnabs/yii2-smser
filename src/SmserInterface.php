<?php

namespace emhome\smser\src;

/**
 * SmserInterface is the interface that should be implemented by smser classes.
 * A smser should mainly support creating and sending [[MessageInterface|sms messages]]. It should
 * also support composition of the message body through the view rendering mechanism. For example,
 * @see MessageInterface
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
interface SmserInterface {

    /**
     * Sends the given sms message.
     * @param MessageInterface $message sms message instance to be sent
     * @return bool whether the message has been sent successfully
     */
    public function send(MessageInterface $message);
}
