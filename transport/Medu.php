<?php

namespace emhome\smser\transport;

use yii\base\InvalidConfigException;
use emhome\smser\src\BaseMessage;
use emhome\smser\src\BaseTransport;

/**
 * 漫道科技
 * 
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
class Medu extends BaseTransport {

    /**
     * @inheritdoc
     */
    public $url = 'http://sdk.entinfo.cn/webservice.asmx/mdSmsSend_u';

    /**
     * @inheritdoc
     */
    public $port = 8060;

    /**
     * @inheritdoc
     */
    public $dataType = 'xml';

    /**
     * @inheritdoc
     */
    public function send(BaseMessage $message = null) {
        if ($message == null) {
            return false;
        }

        /**
         *  sn 序列号
         *  pwd 密码加密
         *  mobile 手机号，多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
         *  content 短信内容
         *  ext 
         *  rrid 如果空返回系统生成的标识串，如果传值保证值唯一，默认空。成功则返回传入的值
         *  stime 定时时间，格式为2011-6-29 11:09:21
         */
        $params = [
            'sn' => $this->username,
            'pwd' => $this->password,
            'mobile' => implode(',', $message->getMobile()),
            'content' => urlencode($message->getContent()),
            'ext' => '',
            'rrid' => '',
            'stime' => ''
        ];

        $ch = curl_init();
        if (stripos($this->url, "https://") !== FALSE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $result = curl_exec($ch);
        curl_close($ch);

        $data = $this->parseResponse($result);
        return explode("-", $data[0]);
    }

    /**
     * 设置密码
     * 按文档描述需要加密
     * 
     * @param string $password
     * @throws InvalidConfigException
     */
    public function setPassword($password) {
        if ($this->username === null) {
            throw new InvalidConfigException('账户序列号不能为空!', 500);
        }
        $this->password = strtoupper(md5($this->username . $password));
    }

}
