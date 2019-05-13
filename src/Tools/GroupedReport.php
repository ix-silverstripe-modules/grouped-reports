<?php

namespace Internetrix\GroupedReports;

use Internetrix\GroupedReport\CustomGridFieldExportButton;
use Internetrix\GroupedReport\CustomGridFieldPrintButton;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Reports\Report;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

class GroupedReport extends Report
{

    protected $searchFilters;

    public function setSearchFilters($searchFilters)
    {
        $this->searchFilters = $searchFilters;
        return $this->owner;
    }

    public function getSearchFilters()
    {
        return $this->searchFilters;
    }

    public function canView($member = null)
    {
        $member = Security::getCurrentUser();
        return Permission::checkMember($member, array('CMS_ACCESS_ReportAdmin'));
    }

    public function getReportField()
    {
        $grid = parent::getReportField();
        $grid->setTitle($this->title());
        $config = $grid->getConfig();
        $config->removeComponentsByType(GridFieldExportButton::class);
        $config->removeComponentsByType(GridFieldPrintButton::class);
        $config->addComponent($print = new CustomGridFieldPrintButton('buttons-before-left'));
        $config->addComponent($export = new CustomGridFieldExportButton('buttons-before-left'));
        $print->setPrintColumns($this->columns());
        $export->setExportColumns($this->columns());
        $export->setCustomFileName($grid->Title());
        return $grid;
    }

}
