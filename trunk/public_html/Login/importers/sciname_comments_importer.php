<?php

include_once 'import_dir_common.php';

class sciname_comments_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'sciname_comments.txt';
	}
		
	public static function exportName() {
		return 'sciname_comments';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'sciname_id'),
			array('name'=>'seq_no', 'def'=>'INT(6)'),
			array('name'=>'taxo_comment', 'def'=>'VARCHAR(2048)')
		);
	}
}
?>