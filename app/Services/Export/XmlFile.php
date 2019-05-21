<?php

namespace App\Services\Export;

use Spatie\ArrayToXml\ArrayToXml;

class XmlFile implements ExportableFile
{
    /**
     * @var string
     */
    protected $fileName;

    protected $xmlService;

    const EXTENSION = 'xml';

    protected $fileContent;

    public function __construct(string $baseFilename, array $records)
    {
        $this->fileName = $baseFilename . '.' . self::EXTENSION;
        $this->fileContent = ArrayToXml::convert($records);
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Get file content
     *
     * @return string
     */
    public function getFileContent(): string
    {
        return $this->fileContent;
    }
}
