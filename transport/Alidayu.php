<?php

namespace emhome\smser\transport;

use yii\base\InvalidConfigException;
use emhome\smser\src\BaseMessage;
use emhome\smser\src\BaseTransport;

/**
 * 阿里大鱼
 * 
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
class Alidayu extends BaseTransport {

    /**
     * @inheritdoc
     */
    public $url = 'http://gw.api.taobao.com/router/rest';

    /**
     * @inheritdoc
     */
    public $dataType = 'json';

    /**
     * @inheritdoc
     * curl -X POST 'http://gw.api.taobao.com/router/rest' \
      -H 'Content-Type:application/x-www-form-urlencoded;charset=utf-8' \
      -d 'app_key=12129701' \
      -d 'format=json' \
      -d 'method=alibaba.aliqin.fc.sms.num.send' \
      -d 'partner_id=apidoc' \
      -d 'sign=DEE7FEBA7C5B9FD940F57E07271F7719' \
      -d 'sign_method=hmac' \
      -d 'timestamp=2017-05-31+18%3A02%3A53' \
      -d 'v=2.0' \
      -d 'extend=123456' \
      -d 'rec_num=13000000000' \
      -d 'sms_free_sign_name=%E9%98%BF%E9%87%8C%E5%A4%A7%E4%BA%8E' \
      -d 'sms_param=%7B%5C%22code%5C%22%3A%5C%221234%5C%22%2C%5C%22product%5C%22%3A%5C%22alidayu%5C%22%7D' \
      -d 'sms_template_code=SMS_585014' \
      -d 'sms_type=normal'
     */
    public function send(BaseMessage $message = null) {
        if ($message == null) {
            return false;
        }

        $params = [
            'app_key' => $this->username,
            'format' => $this->password,
            'method' => $this->password,
            'partner_id' => $this->password,
            'sign' => $this->password,
            'sign_method' => $this->password,
            'timestamp' => $this->password,
            'v' => $this->password,
            'extend' => $this->password,
            'rec_num' => $this->password,
            'sms_free_sign_name' => $this->password,
            'sms_param' => $this->password,
            'sms_template_code' => $this->password,
            'sms_type' => $this->password,
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
