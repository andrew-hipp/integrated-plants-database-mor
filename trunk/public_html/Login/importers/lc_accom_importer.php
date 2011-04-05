<?php

include_once 'import_dir_common.php';

class lc_accom_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'LC_ACCOM.txt';
	}
		
	public static function exportName() {
		return 'lc_accom';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'accession_no'),
			array('name'=>'seq_no', 'def'=>'INT(6)'),
			array('name'=>'acc_comment', 'def'=>'VARCHAR(2048)')
		);
	}
}
?>