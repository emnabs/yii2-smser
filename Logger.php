<?php

namespace emhome\smser;

use Yii;

/**
 * Logger is a SwiftSmser plugin, which allows passing of the SwiftSmser internal logs to the
 * Yii logging mechanism. Each native SwiftSmser log message will be converted into Yii 'info' log entry.
 *
 * @author emhome <emhome@163.com>
 * @since 2.0
 */
class Logger {

    /**
     * @inheritdoc
     */
    public function add($entry) {
        $categoryPrefix = substr($entry, 0, 2);
        switch ($categoryPrefix) {
            case '++':
                $level = \yii\log\Logger::LEVEL_TRACE;
                break;
            case '>>':
            case '<<':
                $level = \yii\log\Logger::LEVEL_INFO;
                break;
            case '!!':
                $level = \yii\log\Logger::LEVEL_WARNING;
                break;
        }

        if (!isset($level)) {
            $level = \yii\log\Logger::LEVEL_INFO;
        }

        Yii::getLogger()->log($entry, $level, __METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function clear() {
        // do nothing
    }

    /**
     * @inheritdoc
     */
    public function dump() {
        return '';
    }

}
