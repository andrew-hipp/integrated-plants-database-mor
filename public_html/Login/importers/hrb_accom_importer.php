<?php

include_once 'import_dir_common.php';

class hrb_accom_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'HRB_ACCOM.txt';
	}
	
	public static function exportName() {
		return 'hrb_accom';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'accession_no', 'def'=>'INT(6)'),
			array('name'=>'herb_no', 'def'=>'INT(6)'),
			array('name'=>'seq_no', 'def'=>'INT(6)'),
			array('name'=>'acc_comment', 'def'=>'VARCHAR(2048)')
		);
	}
}
?>