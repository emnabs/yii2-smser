<?php

namespace emhome\smser\src;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Json;

/**
 * 短信发送接口基类
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
abstract class BaseTransport extends \yii\base\Component {

    const E_ACCOUNT = 0;
    CONST STATE_SEND_FAIL = 10;

    /**
     * 用户名
     *
     * @var string
     */
    public $username;

    /**
     * 密码
     *
     * @var string
     */
    protected $password;

    /**
     * 请求地址
     *
     * @var string
     */
    protected $url;

    /**
     * 返回内容格式
     * 
     * @see json|xml
     * @var string
     */
    public $dataType = 'json';

    /**
     * 状态码
     *
     * @var string
     */
    protected $state;

    /**
     * 状态信息
     *
     * @var string
     */
    protected $message;

    /**
     * 是否启用文件模式
     * 
     * @var boolean
     */
    public $fileMode = true;

    /**
     * 发送短信
     */
    abstract public function send(BaseMessage $message = null);

    /**
     * 设置密码
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * 设置API地址
     *
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * 获取状态码
     *
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    /**
     * 获取状态信息
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * 获取发送接口返回信息
     *
     * @param string $data 返回内容
     * @param string $dataType
     * @return
     */
    public function parseResponse($data, $dataType = null) {
        if ($dataType === null) {
            $dataType = $this->dataType;
        }
        $func = 'parse' . strtoupper($dataType);
        if (method_exists($this, $func)) {
            return $this->$func($data);
        }
        return $data;
    }

    /**
     * 解析xml
     *
     * @param string $data 内容
     * @return array
     */
    private function parseXML($data) {
        $xml = simplexml_load_string($data);
        return (array) $xml;
    }

    /**
     * 解析json
     *
     * @param string $data 内容
     * @return array
     */
    private function parseJSON($data) {
        return Json::decode($data);
    }

    /**
     * 解析QUERY_STRING
     *
     * @param string $data 内容
     * @return array
     */
    private function parseVARS($data) {
        return parse_str($data);
    }

}
