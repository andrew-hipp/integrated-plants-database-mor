<?php

include_once 'import_dir_common.php';

class hrb_annotation_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'HRB_ANNOTATION.txt';
	}
		
	public static function exportName() {
		return 'hrb_annotation';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'annot_nbr', 'def'=>'INT(6)'),
			array('name'=>'annot_by', 'def'=>'VARCHAR(64)'),
			array('name'=>'annot_date', 'def'=>'DATE NULL'),
			array('name'=>'annot_type', 'def'=>'VARCHAR(8)'),
			array('name'=>'addl_annot_by', 'def'=>'VARCHAR(16)'),
			array('name'=>'date_acc', 'def'=>'VARCHAR(8)'),
			array('name'=>'comments_', 'def'=>'VARCHAR(2048)'),
			array('name'=>'litref', 'def'=>'VARCHAR(256)'),
			array('name'=>'qual', 'def'=>'VARCHAR(8)'),
			array('name'=>'acc_nbr', 'def'=>'INT(6)'),
			array('name'=>'herb_nbr', 'def'=>'INT(6)'),
			array('name'=>'orig_id', 'def'=>'VARCHAR(48)'),
			array('name'=>'new_id', 'def'=>'VARCHAR(48)'),
			array('name'=>'annot_code', 'def'=>'CHAR(2)')
		);
	}
}
?>