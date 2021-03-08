<?php


namespace App\logger;

use w1575\ConsoleColor;

class ConsoleLogger implements LoggerInterface
{
    /**
     * @var ConsoleColor объект вспомогательного класса для вывода сообщений
     */
    private ConsoleColor $console;

    /**
     * @var int время начала выполнения
     */
    private int $startTime;

    /**
     * @var int дата завершения
     */
    private int $endTime;

    /**
     * Формат даты
     * @var string
     */
    private string $timeFormat = 'h:i:s';

    /**
     * ConsoleLogger constructor.
     * @param mixed ...$params
     */
    public function __construct(...$params)
    {
        $consoleTheme = $params['consoleTheme'] ?? 'invert';
        $this->console = new ConsoleColor($consoleTheme);
        $this->timeFormat = $params['timeFormat'] ?? $this->timeFormat;

    }

    /**
     * Устанавливает время старта логирования
     */
    public function setStartTime():void
    {
        $this->startTime = time();
    }

    /**
     * устанавливает время завершения логирования
     */
    public function setEndTime():void
    {
        $this->endTime = time();
    }

    /**
     * Возвращает отформатированное время старта скрипта
     * @return string
     */
    public function getStartTime():string
    {
        return date($this->timeFormat, $this->startTime);
    }

    /**
     * Возвращает отформатированное время завершения
     * @return string
     */
    public function getEndTime():string
    {
        return date($this->timeFormat, $this->endTime);
    }

    /**
     * Возвращает отформатированное время выполнения
     * @return string
     */
    public function getExecutionTime():string
    {
        $seconds = $this->endTime - $this->startTime;
        return sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
    }

    /**
     * @param string $message
     */
    public function writeLog(string $message): void
    {
        echo "{$message} \n";
    }


    /**
     * @param string $message
     */
    public function writeError(string $message): void
    {
        $this->writeLogString($message, 'danger');
    }

    /**
     * @param string $message
     */
    public function writeInfo(string $message): void
    {
        $this->writeLogString($message, 'info');
    }

    /**
     * @param string $message
     */
    public function writeWarning(string $message):void
    {
        $this->writeLogString($message, 'warning');
    }

    /**
     * @param $message
     * @param $type
     */
    private function writeLogString($message, $type):void
    {
        $time = date($this->timeFormat, time());
        $formattedMessage = "{$time}: {$message}";
        $this->console->{$type}($formattedMessage);
    }

}