<?php

namespace pff\modules;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use pff\Abs\AModule;
use pff\Core\ServiceContainer;
use pff\Iface\IConfigurableModule;
use Psr\Log\LogLevel;

class Pff2Monolog extends AModule implements IConfigurableModule{

    private $monolog;

    private $defaultChannelName, $enableFile, $loggerFileName, $enableFirePHP, $addFileInfo;

    public function __construct($confFile = 'pff2-monolog/module.conf.local.yaml') {
        $this->loadConfig($confFile);

        $this->monolog = new Logger($this->defaultChannelName);

        if($this->enableFile) {
            $this->monolog->pushHandler(new StreamHandler(ROOT.'/app/logs/'.date("Y-m-d").'-'.$this->loggerFileName.'.txt'), Logger::INFO);
        }

        if($this->enableFirePHP && ServiceContainer::get('config')->getConfigData('development_environment')) {
            $this->monolog->pushHandler(new FirePHPHandler());
        }

        if($this->addFileInfo) {
            $this->monolog->pushProcessor(new IntrospectionProcessor());
        }
    }

    /**
     * @param array $parsedConfig
     * @return mixed
     */
    public function loadConfig($parsedConfig) {
        $conf = $this->readConfig($parsedConfig);
            $this->defaultChannelName = $conf['moduleConf']['defaultChannelName'];

            $this->enableFile     = $conf['moduleConf']['fileLoggerEnabled'];
            $this->loggerFileName = $conf['moduleConf']['fileLoggerName'];

            $this->enableFirePHP = $conf['moduleConf']['firePhPLoggerEnabled'];

            $this->addFileInfo = $conf['moduleConf']['addFileInfo'];
    }

    /**
     * @return Logger
     */
    public function getMonolog() {
        return $this->monolog;
    }

    /**
     * @param Logger $monolog
     */
    public function setMonolog($monolog) {
        $this->monolog = $monolog;
    }

    /**
     * @param string $message Message to log
     * @param int $level Level to log
     * @param array $context
     */
    public function log($message, $level, array $context = array()) {
        if($level<100) {
            if($level == 0) {
                $level = Logger::INFO;
            }
            elseif($level == 1) {
                $level = Logger::ERROR;
            }
            elseif($level == 2){
                $level == Logger::CRITICAL;
            }
            else {
                $level = Logger::INFO;
            }
        }
        $this->monolog->log($level, $message, $context);
    }

    public function debug($message, array $context = array()) {
        $this->monolog->debug($message,$context);
    }

    public function info($message, array $context = array()) {
        $this->monolog->info($message, $context);
    }

    public function notice($message, array $context = array()) {
        $this->monolg->notice($message, $context);
    }

    public function warning($message, array $context = array()) {
        $this->monolog->warning($message, $context);
    }

    public function error($message, array $context = array()) {
        $this->monolog->error($message, $context);
    }

    public function critical($message, array $context = array()) {
        $this->monolog->critical($message, $context);
    }

    public function alert($message, array $context = array()) {
        $this->monolog->alert($message, $context);
    }

    public function emergency($message, array $context = array()) {
        $this->monolog->emergency($message, $context);
    }
}
