<?php

namespace App\Services\Export;

use Illuminate\Database\Eloquent\Collection;

class CsvExporter implements EloquentCollectionExporter
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $exportableFields;

    /**
     * Sets eloquent collection from which will be exported data
     *
     * @param Collection $collection
     * @return EloquentCollectionExporter
     */
    public function setCollection(Collection $collection): EloquentCollectionExporter
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Sets fields which should be exported
     *
     * @param array $fields
     * @return EloquentCollectionExporter
     */
    public function setFields(array $fields): EloquentCollectionExporter
    {
        $this->exportableFields = $fields;

        return $this;
    }

    /**
     * Sets file name for downloading
     *
     * @param string $name
     * @return EloquentCollectionExporter
     */
    public function setFilename(string $name): EloquentCollectionExporter
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets file (exportable object)
     *
     * @return ExportableFile
     * @throws \League\Csv\CannotInsertRecord
     */
    public function getFile(): ExportableFile
    {
        $file = new CsvFile($this->name);
        $file->setHeader($this->exportableFields);

        $records = $this->collection->map->only($this->exportableFields)->toArray();

        $file->setRecords($records);

        return $file;
    }
}
