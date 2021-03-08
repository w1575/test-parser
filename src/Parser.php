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
     * @var array массив с данными из файла source.csv
     */
    private array $sourceData = [];
    /**
     * @var array список имен файлов из директории external-ids
     */
    private array $externalIdsFileNames = [];

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

        $this->externalIdsPath = "{$this->folderPath}//external-ids";

        if (!is_dir($this->externalIdsPath)) {
            throw new \Exception('Отсутствует директория external-ids');
        }

        $this->sourceFilePath = "{$this->folderPath}//source.csv";

        if (!is_file($this->sourceFilePath)) {
            throw new \Exception('Отсутствует файл source.csv');
        }

        $this->resultPath = "{$this->folderPath}//result.csv" ;

        if (!is_file($this->resultPath)) {
            $old = umask(0);
            touch($this->resultPath);
            umask($old);
        }

        file_put_contents($this->resultPath, '');

        $this->logger = new ConsoleLogger('def');
    }

    /**
     * Запуск скрипта
     */
    public function start():bool
    {
        $logger = $this->logger;
        $logger->setStartTime();
        $logger->writeInfo("Скрипт начал свою работу");
        try {
            $this->loadSourceData();
        } catch (\Exception $exception) {
            $logger->writeError($exception->getMessage());
            return false;
        }

        try {
            $this->loadExternalIdsNames();
        } catch (\Exception $e) {
            $logger->writeError($e->getMessage());
            return false;
        }

        $this->parseExternalIds();

        $logger->setEndTime();
        $logger->writeInfo("Время выполнения скрипта: {$logger->getExecutionTime()}");
        return true;
    }

    /**
     * Загружаю все в память, в надежде, что размер файла не особо большой, ибо никаких особенностей, связанных с
     * размером, в задании нет.
     * @throws \Exception
     */
    private function loadSourceData():void
    {
        $row = 0;
        $parsedRows = 0;

        if (($handle = fopen($this->sourceFilePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 500, ";")) !== FALSE) {
                $row++;
                if (count($data) !== 2) {
                    $this->logger->writeWarning("Строка {$row} имеет неверный формат");
                } else {
                    $parsedRows++;
                    $this->sourceData[$data[0]] = $data[1];
                }

            }
            fclose($handle);
        } else {
            throw new \Exception('Не удалось прочитать файл source.csv');
        }

        if ($parsedRows === 0) {
            throw new \Exception('Похоже файл source.csv пуст, либо имеет неверный форма.');
        }
    }

    /**
     * Возвращает полный путь к файлу логов
     * @param $name
     * @return string
     */
    private function getFullPathToLogFile($name):string
    {
        return "{$this->externalIdsPath}//{$name}";
    }


    /**
     * Загружает список имен файлов в массив, в памяти.
     * @throws \Exception
     */
    private function loadExternalIdsNames():void
    {
        $handle = opendir($this->externalIdsPath);
        if ($handle !== false) {
            while (($filename = readdir($handle)) !== false ) {
                if (preg_match('/\.log$/u', $filename))
                    $this->externalIdsFileNames[] = $filename;
                    // в качестве значения буду хранить кол-во записей из массива coupones -1 значит что я не заглядывал
                    // еще в этот файл
            }
        } else {
            throw new \Exception("Не удалось открыть директорию.");
        }
    }

    /**
     *
     */
    private function parseExternalIds():void
    {

        foreach ($this->sourceData as $code => $orderNumber) {
            $this->logger->writeInfo($code);
            foreach ($this->externalIdsFileNames as $index => $fileName) {
                $fileContent = file_get_contents($this->getFullPathToLogFile($fileName));
                $fileContent = json_decode($fileContent, true);
                if ($fileContent === NULL) {
                    $this->logger->writeError("Файл {$fileName} имеет неверный формат!");
                    unset($this->externalIdsFileNames[$fileName]);
                    // чтобы больше по этому файлу не бегать
                } else {

                    if (!isset($fileContent['coupones'])) {
                        $this->logger->writeError("Файл {$fileName} имеет неверный формат");
                        unset($this->externalIdsFileNames[$index]);
                        continue;
                    }

                    foreach ($fileContent['coupones'] as $couponIndex => $coupone) {
                        // т.к у нас почему-то купоны - массив
                        if ($coupone['code'] == $code) {
                            $array = [$code, $orderNumber, $coupone['id']];
                            $this->writeInResultsFile($array);
                            unset($this->sourceData[$code]);
                            unset($this->externalIdsFileNames[$index]);
                        }
                    }
                }
            }
        }

//        pred($this->sourceData);

        foreach ($this->sourceData as $code => $orderNumber) {
            $this->writeInResultsFile([$code, $orderNumber, '-']);
        }

        foreach ($this->externalIdsFileNames as $fileName => $externalIdsFileName) {
            $fileContent = file_get_contents($this->getFullPathToLogFile($externalIdsFileName));
            $fileContent = json_decode($fileContent, true);
            if ($fileContent === NULL) {
                unset($this->externalIdsFileNames[$externalIdsFileName]);
                // чтобы больше по этому файлу не бегать
            } else {
                foreach ($fileContent['coupones'] as $index => $coupone) {
                    $this->writeInResultsFile([$coupone['code'], '-', $coupone['id']]);
                }
            }
        }


    }

    /**
     * @param array $lineArray
     */
    private function writeInResultsFile(array $lineArray):void
    {
        $line = implode(';', $lineArray) . PHP_EOL;
        file_put_contents($this->resultPath, $line, FILE_APPEND);

    }



}