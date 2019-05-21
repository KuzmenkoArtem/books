<?php

namespace App\Services\Export;

use Illuminate\Database\Eloquent\Collection;

interface EloquentCollectionExporter
{
    /**
     * Sets eloquent collection from which will be exported data
     *
     * @param Collection $collection
     * @return EloquentCollectionExporter
     */
    public function setCollection(Collection $collection): self;

    /**
     * Sets fields which should be exported
     *
     * @param array $fields
     * @return EloquentCollectionExporter
     */
    public function setFields(array $fields): self;

    /**
     * Sets file name for downloading
     *
     * @param string $name
     * @return EloquentCollectionExporter
     */
    public function setFilename(string $name): self;

    /**
     * Gets file (exportable object)
     *
     * @return ExportableFile
     */
    public function getFile(): ExportableFile;
}
