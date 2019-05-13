<?php

namespace Internetrix\GroupedReport;

use League\Csv\Writer;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;


class CustomGridFieldExportButton extends GridFieldExportButton
{


    protected $customFileName;

    /**
     * Handle the export, for both the action button and the URL
     *
     * @param GridField $gridField
     * @param HTTPRequest $request
     *
     * @return HTTPResponse
     */
    public function handleExport($gridField, $request = null)
    {
        $now = date("Y-m-d_H-i");
        $customFileName = $this->getCustomFileName();
        $fileName = $customFileName ? preg_replace('/\W+/', '_', $customFileName) : "export";
        $fileName = "$fileName_$now.csv";

        if ($fileData = $this->generateExportFileData($gridField)) {
            return HTTPRequest::send_file($fileData, $fileName, 'text/csv');
        }
        return null;
    }

    public function setCustomFileName($name)
    {
        $this->customFileName = $name;
        return $this;
    }

    public function getCustomFileName()
    {
        return $this->customFileName;
    }

    public function generateExportFileData($gridField)
    {
        $csvColumns = $this->getExportColumnsForGridField($gridField);

        $csvWriter = Writer::createFromFileObject(new \SplTempFileObject());
        $csvWriter->setDelimiter($this->getCsvSeparator());
        $csvWriter->setEnclosure($this->getCsvEnclosure());
        $csvWriter->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
        $csvWriter->setOutputBOM(Writer::BOM_UTF8);

        if (!Config::inst()->get(get_class($this), 'xls_export_disabled')) {
            $csvWriter->addFormatter(function (array $row) {
                foreach ($row as &$item) {
                    // [SS-2017-007] Sanitise XLS executable column values with a leading tab
                    if (preg_match('/^[-@=+].*/', $item)) {
                        $item = "\t" . $item;
                    }
                }
                return $row;
            });
        }

        if ($this->csvHasHeader) {
            $headers = [];

            // determine the CSV headers. If a field is callable (e.g. anonymous function) then use the
            // source name as the header instead
            foreach ($csvColumns as $columnSource => $columnHeader) {
                if (is_array($columnHeader) && array_key_exists('title', $columnHeader)) {
                    $headers[] = $columnHeader['title'];
                } else {
                    $headers[] = (!is_string($columnHeader) && is_callable($columnHeader)) ? $columnSource : $columnHeader;
                }
            }

            $csvWriter->insertOne($headers);
            unset($headers);
        }

        //Remove GridFieldPaginator as we're going to export the entire list.
        $gridField->getConfig()->removeComponentsByType(GridFieldPaginator::class);

        $items = $gridField->getManipulatedList();

        // @todo should GridFieldComponents change behaviour based on whether others are available in the config?
        foreach ($gridField->getConfig()->getComponents() as $component) {
            if ($component instanceof GridFieldFilterHeader || $component instanceof GridFieldSortableHeader) {
                $items = $component->getManipulatedData($gridField, $items);
            }
        }

        /** @var DataObject $item */
        foreach ($items->limit(null) as $item) {
            $columnData = [];
            foreach ($csvColumns as $columnSource => $columnHeader) {
                if (!is_string($columnHeader) && is_callable($columnHeader)) {
                    if ($item->hasMethod($columnSource)) {
                        $relObj = $item->{$columnSource}();
                    } else {
                        $relObj = $item->relObject($columnSource);
                    }

                    $value = $columnHeader($relObj);
                } else {
                    $value = $gridField->getDataFieldValue($item, $columnSource);

                    if (isset($columnHeader['casting'])) {
                        $value = $gridField->getCastedValue($value, $columnHeader['casting']);
                    }
                    if (isset($columnHeader['formatting'])) {
                        if ($columnHeader['formatting']) {
                            $value = call_user_func($columnHeader['formatting'], $value, $item);
                        } else {
                            $value = null;
                        }
                    }

                }

                $columnData[] = $value;
            }

            $csvWriter->insertOne($columnData);

            if ($item->hasMethod('destroy')) {
                $item->destroy();
            }
        }

        return (string)$csvWriter;
    }

}
