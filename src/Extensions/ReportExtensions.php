<?php

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\GroupedList;

class ReportsExtension extends DataExtension {
	
	public function updateEditForm(&$form){
		if( $gridfield = $form->Fields()->dataFieldByName('Reports') ){
			$cols = $gridfield->getConfig()->getComponentByType('SilverStripe\\Forms\\GridField\\GridFieldDataColumns');
			$newCols = $cols->getDisplayFields($gridfield);
			$newCols['description'] = 'Summary';
			$cols->setDisplayFields( $newCols );
		}
		
	}
	
	public function getGroupedReports() {
		$output = $this->owner->Reports();
		foreach( $output as $k => $v ){
			$v->Grouping = $v->hasMethod('group') ? $v->group() : '';
		}
		return GroupedList::create($output);
	}
}

class ReportsExtraExtension extends DataExtension {
	public function group(){
		return _t('SideReport.BrokenLinksGroupTitle', "Broken links reports");
	} 
}
