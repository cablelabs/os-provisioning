<?php

namespace Acme\log;

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Use channels to log into separate files
 *
 * @author Peter Feher
 */
class ChannelStreamHandler extends StreamHandler
{
    /**
     * Channel name
     *
     * @var string
     */
    protected $channel;

    /**
     * @param string $channel Channel name to write
     * @see parent __construct for params
     */
    public function __construct($channel, $stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false)
    {
        $this->channel = $channel;

        // dont add empty context array fields
        $formatter = new LineFormatter(null, null, false, true);
        $this->setFormatter($formatter);

        parent::__construct($stream, $level, $bubble);
    }

    /**
     * When to handle the log record.
     *
     * @param array $record
     * @return type
     */
    public function isHandling(array $record)
    {
        //Handle if Level high enough to be handled (default mechanism)
        //AND CHANNELS MATCHING!
        if (isset($record['channel'])) {
            return
                $record['level'] >= $this->level &&
                $record['channel'] == $this->channel;
        } else {
            return
                $record['level'] >= $this->level;
        }
    }
}
