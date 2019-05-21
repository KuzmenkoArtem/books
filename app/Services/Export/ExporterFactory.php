<?php

namespace App\Services\Export;

use App\Exceptions\WrongExportingFormat;

class ExporterFactory
{
    /**
     * Creates exporter
     *
     * @param string $type
     * @return EloquentCollectionExporter
     * @throws WrongExportingFormat
     */
    public static function getExporter(string $type): EloquentCollectionExporter
    {
        switch ($type) {
            case 'csv':
                return new CsvExporter;
                break;
            case 'xml':
                return new XmlExporter;
                break;
        }

        throw new WrongExportingFormat("{$type} is not accessible");
    }
}
