<?php


namespace App\logger;

/**
 * Просто развлекаюсь с интерфейсом особо логики в нем искать не стоит
 * @package App\logger
 */
interface LoggerInterface
{
    public const TYPE_INFO = 'ERROR';
    public const TYPE_WARNING = 'WARNING';
    public const TYPE_ERROR = 'ERROR';

    /**
     * @param string $message сообщение как есть, без предворительного форматирования
     */
    public function writeLog(string $message):void;

    /**
     * Записывает лог информационного сообщения в нужном формате
     * @param string $message
     */
    public function writeInfo(string $message):void;

    /**
     * Осуществялет запись предупреждений в нужном формате
     * @param string $message
     */
    public function writeWarning(string $message):void;

    /**
     * Осуществялет запись ошибок в нужном формате
     * @param string $message
     */
    public function writeError(string $message):void;


}