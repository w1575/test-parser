<?php

namespace App;

use App\logger\ConsoleLogger;

class Parser
{
    /**
     * @var string путь к директории с всеми файлами
     */
    private string $folderPath;
    /**
     * @var string
     */
    private string $externalIdsPath;
    /**
     * @var string путь до файла с исходными данным
     */
    private string $sourceFilePath;
    /**
     * @var string путь до файла с результатами парсинга
     */
    private string $resultPath;
    /**
     * @var ConsoleLogger логер, в который будет использоваться для записи логов
     */
    private ConsoleLogger $logger;

    /**
     * Parser constructor.
     * @param string $folderPath
     * @throws \Exception
     */
    public function __construct(string $folderPath)
    {
        $this->folderPath = $folderPath;
        $this->checkFolderStructure();

    }

    /**
     * @throws \Exception
     */
    private function checkFolderStructure()
    {
        if (!is_dir($this->folderPath)) {
            throw new \Exception('Неверно указан путь к папке данных');
        }

        $this->externalIdsPath = "{$this->folderPath}\\external-ids";

        if (!is_dir($this->externalIdsPath)) {
            throw new \Exception('Отсутствует директория external-ids');
        }

        if (!is_file($this->sourceFilePath)) {
            throw new \Exception('Отсутствует файл source.csv');
        }

        $this->resultPath = "{$this->externalIdsPath}\\result.csv" ;
        if (!is_file($this->resultPath)) {
            $old = umask(0);
            touch($this->resultPath);
            umask($old);
        }

        $this->logger = new ConsoleLogger();

    }

    public function start()
    {
        $logger = $this->logger;
        $logger->setStartTime();
        $logger->writeInfo("Начало работы логгера");

    }


}