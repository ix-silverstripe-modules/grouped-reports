<?php

namespace Internetrix\GroupedReport;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;

class CustomGridFieldPrintButton extends GridFieldPrintButton
{

    public function generatePrintData(GridField $gridField)
    {
        $printColumns = $this->getPrintColumnsForGridField($gridField);

        $header = null;

        if ($this->printHasHeader) {
            $header = new ArrayList();

            foreach ($printColumns as $field => $label) { // need to catch arrays
                if (is_array($label)) {
                    if (isset($label['title'])) {
                        $label = $label['title'];
                    } else {
                        $label = $field;
                    }
                }
                $header->push(new ArrayData(array(
                    "CellString" => $label,
                )));
            }
        }

        $gridField->getConfig()->removeComponentsByType(GridFieldPaginator::class); // need to remove pagination
        $items = $gridField->getManipulatedList();
        $itemRows = new ArrayList();

        /** @var DataObject $item */
        foreach ($items->limit(null) as $item) {
            $itemRow = new ArrayList();
            foreach ($printColumns as $field => $label) {
                $value = $gridField->getDataFieldValue($item, $field);
                if (!is_array($label)) {
                    if ($item->escapeTypeForField($field) != 'xml') {
                        $value = Convert::raw2xml($value);
                    }
                } else { // need to apply formatting or casting
                    if (isset($label['casting'])) {
                        $value = $gridField->getCastedValue($value, $label['casting']);
                    }
                    if (isset($label['formatting'])) {
                        if ($label['formatting']) {
                            $value = call_user_func($label['formatting'], $value, $item);
                        } else {
                            $value = null;
                        }
                    }
                }
                $itemRow->push(new ArrayData(array(
                    "CellString" => $value,
                )));
            }

            $itemRows->push(new ArrayData(array(
                "ItemRow" => $itemRow
            )));

            if ($item->hasMethod('destroy')) {
                $item->destroy();
            }
        }

        $ret = new ArrayData(array(
            "Title" => $this->getTitle($gridField),
            "Header" => $header,
            "ItemRows" => $itemRows,
            "Datetime" => DBDatetime::now(),
            "Member" => Security::getCurrentUser(),
        ));

        return $ret;
    }

}
