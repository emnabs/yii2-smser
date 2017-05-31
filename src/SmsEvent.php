<?php

namespace emhome\smser\src;

use yii\base\Event;

/**
 * SmsEvent represents the event parameter used for events triggered by [[BaseSmser]].
 * By setting the [[isValid]] property, one may control whether to continue running the action.
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
class SmsEvent extends Event {

    /**
     * @var \emhome\smser\libs\MessageInterface the sms message being send.
     */
    public $message;

    /**
     * @var bool if message was sent successfully.
     */
    public $isSuccessful;

    /**
     * @var bool whether to continue sending an email. Event handlers of
     * [[\emhome\smser\libs\BaseSmser::EVENT_BEFORE_SEND]] may set this property to decide whether
     * to continue send or not.
     */
    public $isValid = true;

}
