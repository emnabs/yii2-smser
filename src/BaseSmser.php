<?php

namespace emhome\smser\src;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * BaseSmser serves as a base class that implements the basic functions required by [[SmserInterface]].
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
abstract class BaseSmser extends Component implements SmserInterface {

    /**
     * @event SmsEvent an event raised right before send.
     * You may set [[SmsEvent::isValid]] to be false to cancel the send.
     */
    const EVENT_BEFORE_SEND = 'beforeSend';

    /**
     * @event SmsEvent an event raised right after send.
     */
    const EVENT_AFTER_SEND = 'afterSend';

    /**
     * @var array the configuration that should be applied to any newly created
     */
    public $messageConfig = [];

    /**
     * @var string the default class name of the new message instances created by [[createMessage()]]
     */
    public $messageClass = 'emhome\smser\libs\BaseMessage';

    /**
     * @var bool whether to save email messages as files under [[fileTransportPath]] instead of sending them
     * to the actual recipients. This is usually used during development for debugging purpose.
     * @see fileTransportPath
     */
    public $useFileTransport = false;

    /**
     * @var string the directory where the email messages are saved when [[useFileTransport]] is true.
     */
    public $fileTransportPath = '@runtime/sms';

    /**
     * @var callable a PHP callback that will be called by [[send()]] when [[useFileTransport]] is true.
     * The callback should return a file name which will be used to save the email message.
     * If not set, the file name will be generated based on the current timestamp.
     */
    public $fileTransportCallback;

    /**
     * @var \BaseTransport|array BaseTransport transport instance or its array configuration.
     */
    private $_transport = [];

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array the behavior configurations.
     */
    public function behaviors() {
        return [
            FilterMessageBehavior::className(),
        ];
    }

    /**
     * Creates a new message instance and optionally composes its body content via view rendering.
     *
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return MessageInterface message instance.
     */
    public function compose() {
        return $this->createMessage();
    }
    

    /**
     * Creates a new message instance.
     * @return MessageInterface message instance.
     */
    protected function createMessage() {
        $config = $this->messageConfig;
        if (!array_key_exists('class', $config)) {
            $config['class'] = $this->messageClass;
        }
        $config['smser'] = $this;
        return Yii::createObject($config);
    }

    /**
     * @param array|\BaseTransport $transport
     * @throws InvalidConfigException on invalid argument.
     */
    public function setTransport($transport) {
        if (!is_array($transport) && !is_object($transport)) {
            throw new InvalidConfigException('"' . get_class($this) . '::transport" should be either object or array, "' . gettype($transport) . '" given.');
        }
        $this->_transport = $transport;
    }

    /**
     * @return array|\BaseTransport
     */
    public function getTransport() {
        if (!is_object($this->_transport)) {
            $this->_transport = $this->createTransport($this->_transport);
        }
        return $this->_transport;
    }

    /**
     * Creates Swift library object, from given array configuration.
     * @param array $config object configuration
     * @return Object created object
     * @throws \yii\base\InvalidConfigException on invalid configuration.
     */
    protected function createTransport(array $config) {
        if (!isset($config['class'])) {
            throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
        }
        return Yii::createObject($config);
    }

    /**
     * Sends the given email message.
     * This method will log a message about the email being sent.
     * 
     * @param MessageInterface $message sms message instance to be sent
     * @return bool whether the message has been sent successfully
     */
    public function send(MessageInterface $message) {
        if (!$this->beforeSend($message)) {
            return false;
        }

        if ($this->useFileTransport) {
            $isSuccessful = $this->saveMessage($message);
        } else {
            $isSuccessful = $this->sendMessage($message);
        }

        $this->afterSend($message, $isSuccessful);

        return $isSuccessful;
    }

    /**
     * Sends the specified message.
     * This method should be implemented by child classes with the actual email sending logic.
     * 
     * @param MessageInterface $message the message to be sent
     * @return bool whether the message is sent successfully
     */
    protected function sendMessage($message) {
        $address = $message->getMobile();
        if (is_array($address)) {
            $address = implode(', ', array_keys($address));
        }
        Yii::info('Sending sms "' . $message->getContent() . '" to "' . $address . '"', __METHOD__);

        return $this->getTransport()->send($message);
    }

    /**
     * Saves the message as a file under [[fileTransportPath]].
     * 
     * @param MessageInterface $message
     * @return bool whether the message is saved successfully
     */
    protected function saveMessage($message) {
        $path = Yii::getAlias($this->fileTransportPath);


        $dir = Yii::getAlias('@app/runtime/smser');

        try {
            if (!FileHelper::createDirectory($dir)) {
                throw new \Exception('无法创建目录：' . $dir);
            }

            $filename = $dir . DIRECTORY_SEPARATOR . time() . mt_rand(1000, 9999) . '.msg';
            if (!touch($filename)) {
                throw new \Exception('无法创建文件：' . $filename);
            }

            if (!file_put_contents($filename, "TO - $mobile" . PHP_EOL . "CONTENT - $content")) {
                throw new \Exception('短信发送失败！');
            }

            return true;
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            return false;
        }


        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if ($this->fileTransportCallback !== null) {
            $file = $path . '/' . call_user_func($this->fileTransportCallback, $this, $message);
        } else {
            $file = $path . '/' . $this->generateMessageFileName();
        }
        file_put_contents($file, $message->toString());

        return true;
    }

    /**
     * @return string the file name for saving the message when [[useFileTransport]] is true.
     */
    public function generateMessageFileName() {
        $time = microtime(true);

        return date('Ymd-His-', $time) . sprintf('%04d', (int) (($time - (int) $time) * 10000)) . '-' . sprintf('%04d', mt_rand(0, 10000)) . '.msg';
    }

    /**
     * This method is invoked right before sms send.
     * You may override this method to do last-minute preparation for the message.
     * If you override this method, please make sure you call the parent implementation first.
     * 
     * @param MessageInterface $message
     * @return bool whether to continue sending an email.
     */
    public function beforeSend($message) {
        $event = new SmsEvent(['message' => $message]);
        $this->trigger(self::EVENT_BEFORE_SEND, $event);
        return $event->isValid;
    }

    /**
     * This method is invoked right after sms was send.
     * You may override this method to do some postprocessing or logging based on sms send status.
     * If you override this method, please make sure you call the parent implementation first.
     * 
     * @param MessageInterface $message
     * @param bool $isSuccessful
     */
    public function afterSend($message, $isSuccessful) {
        $event = new SmsEvent(['message' => $message, 'isSuccessful' => $isSuccessful]);
        $this->trigger(self::EVENT_AFTER_SEND, $event);
    }

}
