<?php

namespace emhome\smser\transport;

use yii\base\InvalidConfigException;
use emhome\smser\src\BaseMessage;
use emhome\smser\src\BaseTransport;

/**
 * 中国云信
 * 
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
class Cloud extends BaseTransport {

    /**
     * @inheritdoc
     */
    public $url = 'http://api.sms.cn/mtutf8/';

    /**
     * @inheritdoc
     */
    public $dataType = 'vars';

    /**
     * @inheritdoc
     */
    public function send(BaseMessage $message = null) {
        if ($message == null) {
            return false;
        }

        $params = [
            'uid' => $this->username,
            'pwd' => $this->password,
            'mobile' => $message->getMobile(),
            'content' => $message->getContent()
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $result = curl_exec($ch);
        curl_close($ch);

        $data = $this->parseResponse($result);

        $this->state = isset($data['stat']) ? (string) $data['stat'] : null;
        $this->message = isset($data['message']) ? (string) $data['message'] : null;

        return $this->state === '100';
    }

    /**
     * 设置密码
     * 
     * @param string $password
     * @throws InvalidConfigException
     */
    public function setPassword($password) {
        if ($this->username === null) {
            throw new InvalidConfigException('账户用户名不能为空!', 500);
        }
        $this->password = md5($password . $this->username);
    }

    /**
     * @inheritdoc
     */
    public function sendByTemplate($mobile, $data, $id) {
        throw new NotSupportedException('中国云信不支持发送模板短信！');
    }

}
