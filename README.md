# Yii2 Smser Component

Smser is a component based sms solution for Yii2.It is released under the BSD 3-Clause license.

[![Latest Stable Version](https://poser.pugx.org/emnabs/yii2-smser/v/stable.png)](https://packagist.org/packages/emnabs/yii2-smser)
[![Total Downloads](https://poser.pugx.org/emnabs/yii2-smser/downloads.png)](https://packagist.org/packages/emnabs/yii2-smser)
[![License](https://poser.pugx.org/emnabs/yii2-smser/license.png)](https://packagist.org/packages/emnabs/yii2-smser)


## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist emnabs/yii2-smser "*"
```

or add

```json
"emnabs/yii2-smser": "*"
```

to the require section of your composer.json.

## Usage

To use this extension, you have to configure the Connection class in your application configuration:

```php
//configure component:
return [
    //....
    'components' => [
	//....
        'smser' => [
            'class' => 'emhome\smser\Smser',
        ],
	//....
    ]
];
```

Usage example

```php
Yii::$app->smser->compose()
	->setMobile(['手机号码'])
        ->setContent('短信内容')
        ->send();
```

## 包含接口

* [漫道科技](http://www.zucp.net/)
* [中国云信](http://www.sms.cn/)
* [中国网建](http://www.smschinese.cn/)
* [商信通](http://www.sxtsms.com/)
* [云片网络](http://www.yunpian.com/)
* [云通讯](http://www.yuntongxun.com/)
* [螺丝帽](http://www.luosimao.com/)


## License

**yii2-smser** is released under the `BSD 3-Clause` License. See the bundled `LICENSE.md` for details.