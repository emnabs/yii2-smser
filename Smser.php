<?php

namespace emhome\smser;

use emhome\smser\src\BaseSmser;

/**
 * Smser implements a mailer based on SwiftSmser.
 * To use Smser, you should configure it in the application configuration like the following,
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
class Smser extends BaseSmser {

    /**
     * @var string message default class name.
     */
    public $messageClass = 'emhome\smser\Message';

    /**
     * @var bool whether to enable writing of the SwiftSmser internal logs using Yii log mechanism.
     * If enabled [[Logger]] plugin will be attached to the [[transport]] for this purpose.
     * @see Logger
     */
    public $enableSwiftSmserLogging = false;

}
