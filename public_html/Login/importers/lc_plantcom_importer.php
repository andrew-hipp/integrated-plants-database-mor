<?php

include_once 'import_dir_common.php';

class lc_plantcom_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'LC_PLANTCOM.txt';
	}
		
	public static function exportName() {
		return 'lc_plantcom';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'plant_id'),
			array('name'=>'seq_no', 'def'=>'INT(6)'),
			array('name'=>'plant_comment', 'def'=>'VARCHAR(2048)'),
		);
	}
}
?>