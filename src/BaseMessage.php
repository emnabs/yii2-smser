<?php

namespace emhome\smser\src;

use Yii;
use yii\base\Object;

/**
 * BaseMessage serves as a base class that implements the [[send()]] method required by [[MessageInterface]].
 *
 * By default, [[send()]] will use the "sms" application component to send the current message.
 * The "sms" application component should be a smser instance implementing [[SmserInterface]].
 *
 * @see BaseSmser
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
abstract class BaseMessage extends Object implements MessageInterface {

    private $_mobile = [];
    private $_content = '';
    private $_signature = '';
    public $useSigned = true;
    private static $errorMobiles = [];

    /**
     * @var SmserInterface the smser instance that created this message.
     * For independently created messages this is `null`.
     */
    public $smser;

    /**
     * Sends this email message.
     * @param SmserInterface $smser the smser that should be used to send this message.
     * If no smser is given it will first check if [[smser]] is set and if not,
     * the "sms" application component will be used instead.
     * @return bool whether this message is sent successfully.
     */
    public function send(SmserInterface $smser = null) {
        if ($smser === null && $this->smser === null) {
            $smser = Yii::$app->getSmser();
        } elseif ($smser === null) {
            $smser = $this->smser;
        }
        return $smser->send($this);
    }

    /**
     * @inheritdoc
     */
    public function getMobile() {
        return $this->_mobile;
    }

    /**
     * @inheritdoc
     */
    public function setMobile($mobile) {
        $tempMobiles = [];
        if (is_array($mobile) && !empty($mobile)) {
            foreach ($mobile as $_m) {
                $tempMobiles[] = self::formatMobile($_m);
            }
        } elseif (is_string($mobile)) {
            $tempMobiles[] = self::formatMobile($mobile);
        }
        $this->_mobile = array_unique(array_filter($tempMobiles));
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContent() {
        $signature = '';
        if ($this->useSigned) {
            $signature = $this->getSignature();
        }
        return $this->_content . $signature;
    }

    /**
     * @inheritdoc
     */
    public function setContent($content) {
        $this->_content = $content;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSignature() {
        return $this->_signature;
    }

    /**
     * @inheritdoc
     */
    public function setSignature($signature) {
        if ($signature) {
            $this->_signature = '[' . str_replace(['[', ']'], '', $signature) . ']';
        }
        return $this;
    }

    /**
     * 正则检测已有类型数据格式
     * @param string|array $mobile 手机号码
     * @return boolean 检测状态
     */
    private static function formatMobile($mobile) {
        if (preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            return $mobile;
        }
        self::$errorMobiles[] = $mobile;
        return false;
    }

    /**
     * 获取错误手机号码
     * @return array 错误号码
     */
    public function getErrorMobiles() {
        return self::$errorMobiles;
    }

    /**
     * 用于存储短信内容
     * 
     * @param string $mobile
     * @param string $content
     * @throws \Exception
     * @return boolean
     */
    private function saveAsFile($mobile, $content) {
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
    }

}
