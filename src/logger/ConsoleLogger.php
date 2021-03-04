<?php


namespace App\logger;

use w1575\ConsoleColor;

class ConsoleLogger implements LoggerInterface
{
    /**
     * @var ConsoleColor
     */
    private $console;

    /**
     * ConsoleLogger constructor.
     */
    public function __construct()
    {
        $this->console = new ConsoleColor('invert');
    }

    /**
     * @param string $message
     */
    public function writeLog(string $message): void
    {

    }

    public function writeEndTime(): void
    {
        // TODO: Implement writeEndTime() method.
    }

    /**
     * @see LoggerInterface
     */
    public function writeStartTime(): void
    {
        // TODO: Implement writeStartTime() method.
    }

    /**
     * @param string $message
     */
    public function writeError(string $message): void
    {
        // TODO: Implement writeError() method.
    }

    /**
     * @param string $message
     */
    public function writeInfo(string $message): void
    {
        // TODO: Implement writeInfo() method.
    }

    /**
     * @param string $message
     */
    public function writeWarning(string $message):void
    {

    }
}