<?php

namespace App\Services\Export;

use League\Csv\Writer;

class CsvFile implements ExportableFile
{
    /**
     * @var string
     */
    protected $filename;

    protected $csvService;

    const EXTENSION = 'csv';

    public function __construct(string $baseFilename)
    {
        $this->filename = $baseFilename . '.' . self::EXTENSION;
        $this->csvService = Writer::createFromString('');
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->filename;
    }

    /**
     * Set header
     *
     * @param array $header
     * @return int
     * @throws \League\Csv\CannotInsertRecord
     */
    public function setHeader(array $header)
    {
        return $this->csvService->insertOne($header);
    }

    /**
     * Set records
     *
     * @param array $records
     * @return int
     */
    public function setRecords(array $records)
    {
        return $this->csvService->insertAll($records);
    }

    /**
     * Get file content
     *
     * @return string
     */
    public function getFileContent(): string
    {
        return $this->csvService->getContent();
    }
}
