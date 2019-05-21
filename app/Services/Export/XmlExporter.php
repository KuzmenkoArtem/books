<?php

namespace App\Services\Export;

use Illuminate\Database\Eloquent\Collection;
use ReflectionClass;

class XmlExporter implements EloquentCollectionExporter
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
     */
    public function getFile(): ExportableFile
    {
        $records = $this->getRecords();

        return new XmlFile($this->name, $records);
    }

    /**
     * Parses and returns collection data in needed format
     * Connects data with the fields (exportableFields)
     *
     * @return array
     */
    protected function getRecords()
    {
        $exportableFields = $this->exportableFields;

        $data = $this->collection->map(function ($model) use ($exportableFields) {
            $name = (new ReflectionClass($model))->getShortName();
            $name = strtolower($name);

            return [$name => $model->only($exportableFields)];
        })->all();

        $records = [];
        foreach ($data as $item) {
            $records[key($item)] = isset($records[key($item)]) ? $records[key($item)] : [];

            array_push($records[key($item)], $item[key($item)]);
        }

        return $records;
    }
}
