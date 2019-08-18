<?php

namespace Acme\log;

use Monolog\Logger;

class ChannelWriter
{
    /**
     * The Log channels. with minimum log level to trigger the logger
     * NOTE: Add new Channels on demand!
     *
     * @var array
     */
    protected $channels = [
        'billing' => [
            'path' => 'logs/billing.log',
            'level' => Logger::DEBUG,
        ],
        'dunning' => [
            'path' => 'logs/bank-transactions.log',
            'level' => Logger::DEBUG,
        ],
    ];

    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug'     => Logger::DEBUG,
        'info'      => Logger::INFO,
        'notice'    => Logger::NOTICE,
        'warning'   => Logger::WARNING,
        'error'     => Logger::ERROR,
        'critical'  => Logger::CRITICAL,
        'alert'     => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    public function __construct()
    {
    }

    /**
     * Write to log based on the given channel and log level set
     *
     * @param type $channel
     * @param type $message
     * @param array $context
     * @throws InvalidArgumentException
     */
    public function writeLog($channel, $level, $message, array $context = [])
    {
        //check channel exist
        if (! in_array($channel, array_keys($this->channels))) {
            throw new \Exception('Invalid channel used.');
        }

        //lazy load logger
        if (! isset($this->channels[$channel]['_instance'])) {
            //create instance
            $this->channels[$channel]['_instance'] = new Logger($channel);
            //add custom handler
            $this->channels[$channel]['_instance']->pushHandler(
                new ChannelStreamHandler(
                    $channel,
                    storage_path().'/'.$this->channels[$channel]['path'],
                    $this->channels[$channel]['level']
                )
            );
        }

        //write out record
        $this->channels[$channel]['_instance']->{$level}($message, $context);
    }

    public function write($channel, $message, array $context = [])
    {
        //get method name for the associated level
        $level = array_flip($this->levels)[$this->channels[$channel]['level']];
        //write to log
        $this->writeLog($channel, $level, $message, $context);
    }

    //alert('event','Message');
    public function __call($func, $params)
    {
        if (in_array($func, array_keys($this->levels))) {
            return $this->writeLog($params[0], $func, $params[1], isset($params[2]) ? $params[2] : []);
        }
    }
}
