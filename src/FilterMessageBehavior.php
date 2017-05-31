<?php

namespace emhome\smser\src;

use Yii;

class FilterMessageBehavior extends \yii\base\Behavior {

    const EVENT_BEFORE_FILTER = 'beforeFilter';

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            BaseSmser::EVENT_BEFORE_SEND => self::EVENT_BEFORE_FILTER,
        ];
    }

    /**
     * @param ActionEvent $event
     */
    public function beforeFilter($event) {
        $message = $event->message;
        if ($message == null) {
            $event->isValid = false;
        } elseif ($message instanceof BaseMessage) {
            $address = $message->getMobile();
            if (empty($address)) {
                $event->isValid = false;
            }
            $address = implode(', ', array_keys($address));
            Yii::info('Sending sms "' . $message->getContent() . '" to "' . $address . '"', __METHOD__);
        }
    }

}
