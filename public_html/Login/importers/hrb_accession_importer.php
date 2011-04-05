<?php

include_once $app_root . '/Login/importers/import_dir_common.php';

class hrb_accession_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'HRB_ACCESSION.txt';
	}
	
	public static function exportName() {
		return 'hrb_accession';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'acc_nbr', 'def'=>'VARCHAR(8)'),
			array('name'=>'herb_nbr', 'def'=>'VARCHAR(8)'),
			array('name'=>'sciname_id', 'def'=>'VARCHAR(40)'),
			array('name'=>'sciname_qual', 'def'=>'VARCHAR(32)'),
			array('name'=>'spec_type', 'def'=>'VARCHAR(32)'),
			array('name'=>'non_LC_acc_nbr', 'def'=>'VARCHAR(32)'),
			array('name'=>'folder', 'def'=>'VARCHAR(256)'),
			array('name'=>'barcode', 'def'=>'VARCHAR(32)'),
			array('name'=>'associates', 'def'=>'VARCHAR(2048)'),
			array('name'=>'attributes', 'def'=>'VARCHAR(512)'),
			array('name'=>'fl', 'def'=>'CHAR(1)'),
			array('name'=>'fr', 'def'=>'CHAR(1)'),
			array('name'=>'veg', 'def'=>'CHAR(1)'),
			array('name'=>'bud', 'def'=>'CHAR(1)'),
			array('name'=>'lc_acc', 'def'=>'VARCHAR(16)'),
			array('name'=>'lc_plant', 'def'=>'VARCHAR(32)'),
			array('name'=>'orig_id', 'def'=>'VARCHAR(48)'),
			array('name'=>'annot_by', 'def'=>'VARCHAR(64)'),
			array('name'=>'annot_dt', 'def'=>'DATE NULL'),
			array('name'=>'addl_annot_by', 'def'=>'VARCHAR(32)'),
			array('name'=>'annot_comments', 'def'=>'VARCHAR(512)'),
			array('name'=>'annot_date_acc', 'def'=>'VARCHAR(8)'),
			array('name'=>'collector_no', 'def'=>'VARCHAR(32)'),
			array('name'=>'collector_primary', 'def'=>'VARCHAR(128)'),		// Basically a combination of the next two fields.
			array('name'=>'collector_primary_fn', 'def'=>'VARCHAR(32)'),	// First name
			array('name'=>'collector_primary_ln', 'def'=>'VARCHAR(64)'),	// Last name
			array('name'=>'collector_addl', 'def'=>'VARCHAR(256)'),
			array('name'=>'coll_date', 'def'=>'DATE NULL'),
			array('name'=>'coll_date_acc', 'def'=>'VARCHAR(8)'),
			array('name'=>'habitat', 'def'=>'VARCHAR(256)'),
			array('name'=>'site', 'def'=>'VARCHAR(512)'),
			array('name'=>'twp', 'def'=>'VARCHAR(16) NULL'),// Look for (null) in exported data!
			array('name'=>'twp_dir', 'def'=>'VARCHAR(8)'),
			array('name'=>'range', 'def'=>'INT(3) NULL'),	// Look for (null) in exported data!
			array('name'=>'range_dir', 'def'=>'VARCHAR(8)'),// Look for (null) in exported data!
			array('name'=>'sect', 'def'=>'VARCHAR(16)'),	// Look for (null) in exported data!
			array('name'=>'sect_desc', 'def'=>'VARCHAR(128)'),// Look for (null) in exported data!
			array('name'=>'lat', 'def'=>'REAL(10,6) NULL'),
			array('name'=>'lat_dir', 'def'=>'CHAR(1) NULL'),
			array('name'=>'long', 'def'=>'REAL(11,6) NULL'),
			array('name'=>'long_dir', 'def'=>'CHAR(1) NULL'),
			array('name'=>'utm_zone', 'def'=>'VARCHAR(8)'),
			array('name'=>'utm_lat_band', 'def'=>'VARCHAR(8)'),
			array('name'=>'utm_easting', 'def'=>'VARCHAR(8)'),
			array('name'=>'utm_northing', 'def'=>'VARCHAR(8)'),
			array('name'=>'utm_hemisphere', 'def'=>'VARCHAR(8)'),
			array('name'=>'country', 'def'=>'VARCHAR(32)'),
			array('name'=>'subctry1', 'def'=>'VARCHAR(32)'),
			array('name'=>'subctry2', 'def'=>'VARCHAR(32)'),
			array('name'=>'desig1', 'def'=>'VARCHAR(32)'),
			array('name'=>'desig2', 'def'=>'VARCHAR(32)'),
			array('name'=>'site_sensitive', 'def'=>'CHAR(1)'),
			array('name'=>'project', 'def'=>'VARCHAR(256)'),
			array('name'=>'elev_low', 'def'=>'INT(5)'),
			array('name'=>'elev_upper', 'def'=>'INT(5)'),
			array('name'=>'elev_units', 'def'=>'CHAR(2)'),
			array('name'=>'image_thumb', 'def'=>'VARCHAR(192)'),
			array('name'=>'image_large', 'def'=>'VARCHAR(128)'),
			array('name'=>'image_addl', 'def'=>'VARCHAR(128)')
		);
	}
	
	protected function columnDataFilter($idx, $field) {
		switch ($idx) {
			case 30:
			case 32:
			case 33:
			case 34:
			case 35:
				if ($field == '(null)') {
					$field = '';
				}
				break;
		}
		
		return $field;
	}
	
	protected function getAddlIndexesArray() {
		// return an array of array('name'=>'index_name', 'field'=>'field_to_index')
		return array(array('name'=>'sciname_id_index', 'field'=>'sciname_id'),
				array('name'=>'herb_index', 'field'=>'herb_nbr'));
	}
}
?>
