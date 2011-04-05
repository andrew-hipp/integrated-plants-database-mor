<?php

include_once 'import_dir_common.php';

class lc_accession_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'LC_ACCESSION.txt';
	}
		
	public static function exportName() {
		return 'lc_accession';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'accession_no', 'def'=>'VARCHAR(16)'),
			array('name'=>'how_received', 'def'=>'VARCHAR(16)'),
			array('name'=>'no_received', 'def'=>'INT(5) NULL'),
			array('name'=>'orig_cultivar', 'def'=>'CHAR(1) NULL'),
			array('name'=>'original_source', 'def'=>'VARCHAR(64)'),
			array('name'=>'provenance', 'def'=>'CHAR(1)'),
			array('name'=>'provenance_history', 'def'=>'CHAR(2) NULL'),
			array('name'=>'received_sciname_id', 'def'=>'VARCHAR(32)'),
			array('name'=>'received_sciname', 'def'=>'VARCHAR(96)'),
			array('name'=>'sciname_id', 'def'=>'VARCHAR(32)'),
			array('name'=>'source_name', 'def'=>'VARCHAR(192)'),
			array('name'=>'source_no', 'def'=>'VARCHAR(32)'),
			array('name'=>'source_collection', 'def'=>'VARCHAR(32)'),
			array('name'=>'source_location', 'def'=>'VARCHAR(48)'),
			array('name'=>'source_annotation', 'def'=>'VARCHAR(8)'),
			array('name'=>'understock', 'def'=>'VARCHAR(64)'),
			array('name'=>'collector_protected', 'def'=>'CHAR(1) NULL'),
			array('name'=>'collector_assoc_avail', 'def'=>'CHAR(1) NULL'),
			array('name'=>'collector_source_descr', 'def'=>'CHAR(1) NULL'),
			array('name'=>'collector_date', 'def'=>'DATE NULL'),
			array('name'=>'collector_name', 'def'=>'VARCHAR(64)'),
			array('name'=>'collector_no', 'def'=>'VARCHAR(32)'),
			array('name'=>'collector_habitat', 'def'=>'VARCHAR(256)'),
			array('name'=>'collector_site', 'def'=>'VARCHAR(256)'),
			array('name'=>'collector_township_range', 'def'=>'VARCHAR(64)'),
			array('name'=>'collector_subcountry_2', 'def'=>'VARCHAR(32)'),
			array('name'=>'collector_subcountry_2_dsg', 'def'=>'VARCHAR(32)'),
			array('name'=>'collector_subcountry_1', 'def'=>'VARCHAR(32)'),
			array('name'=>'collector_subcountry_1_dsg', 'def'=>'VARCHAR(32)'),
			array('name'=>'collector_country', 'def'=>'VARCHAR(64)'),
			array('name'=>'collector_lat', 'def'=>'REAL(10,6)'),
			array('name'=>'collector_lat_dsg', 'def'=>'VARCHAR(8)'),
			array('name'=>'collector_long', 'def'=>'REAL(11,6)'),
			array('name'=>'collector_long_dsg', 'def'=>'VARCHAR(8)'),
			array('name'=>'collector_elevation_low', 'def'=>'INT(5)'),
			array('name'=>'collector_elevation_high', 'def'=>'INT(5)'),
			array('name'=>'collector_elevation_units', 'def'=>'VARCHAR(8)'),
		);
	}
	
	protected function getAddlIndexesArray() {
		// return an array of array('name'=>'index_name', 'field'=>'field_to_index')
		return array(array('name'=>'accession_index', 'field'=>'accession_no'),
				array('name'=>'sciname_index', 'field'=>'sciname_id'));
	}
	
	public function lc_images($dbLink, $new_table_name) {
		global $app_root;
		$fieldsInfo = array(
				array('name'=>'sciname_id', 'def'=>'VARCHAR(16)'),
				array('name'=>'image_thumb', 'def'=>'VARCHAR(128)'),
				array('name'=>'image_large', 'def'=>'VARCHAR(128)')
			);
		$photoBasePath = $app_root;
		$photoDirPath = realpath($app_root . '/photos');
		$retVal = false;
		
		$pictures = array();
	
		if (($dthand = opendir($photoDirPath . '/thumbnails')) !== false) {
			while (false !== ($file = readdir($dthand))) {
				if (($file == '.') || ($file == '..')) {
					continue;
				}
				$path = $photoDirPath . '/thumbnails/' . $file;
				if (is_dir($path)) {
					continue;
				}
				$parts = explode('.', $file);
				array_pop($parts);
				$sciNameKey = str_replace('-', '*', substr(implode('.', $parts), 0, 14));
				$pictures[strtolower($file)] = array('thumb'=>substr($path, strlen($photoBasePath)), 'large'=>'', 'sciname'=>$sciNameKey);
			}
			
			closedir($dthand);
		}
	
		if (($dhand = opendir($photoDirPath)) !== false) {
			while (false !== ($file = readdir($dhand))) {
				if (($file == '.') || ($file == '..')) {
					continue;
				}
				$path = $photoDirPath . '/' . $file;
				if (is_dir($path)) {
					continue;
				}
				if (isset($pictures[strtolower($file)]['large'])) {
					$pictures[strtolower($file)]['large'] = substr($path, strlen($photoBasePath));
				}
			}
			
			closedir($dhand);
		}
		
		# Now I have the array of info for the database, create and populate
		# the database.
		
		$crResults = $this->createDBFromFields($dbLink, $new_table_name, $fieldsInfo);
		
		if (!$crResults['result']) {
			return false;
		}
			
		$insRowSQL = $crResults['insRowSQL'];
		$varTypes = $crResults['varTypes'];
		
		if (($stmt = $dbLink->prepare($insRowSQL)) !== false) {
			foreach ($pictures as $picture) {
				if (($picture['thumb'] != '') && ($picture['large'] != '')) {
					$stmt->bind_param($varTypes, $picture['sciname'], $picture['thumb'], $picture['large']);
					$stmt->execute();
				}
			}
			
			$stmt->close();
		}
		
		$newIndexSQL = 'ALTER TABLE `' . $new_table_name . '` ADD INDEX `sciname_index` (`sciname_id`)';
		if (($stmt = $dbLink->prepare($newIndexSQL)) !== false) {
			$stmt->execute();
			$stmt->close();
		}
		
		return true;
	}
	
	protected function postImportFilter($dbLink, $new_table_name, $importResult) {
		if ($importResult) {
			$importResult = $this->lc_images($dbLink, 'lc_images_import');
			
			if ($importResult) {
				if ($stmt = $dbLink->prepare('DROP TABLE lc_images_old')) {
					$stmt->execute();
					$stmt->close();
				}
				
				if ($stmt = $dbLink->prepare('RENAME TABLE lc_images TO lc_images_old, '
						. ' lc_images_import TO lc_images')) {
					$stmt->execute();
					$stmt->close();
					
					// Just in case the old table didn't exist, just do the second RENAME TABLE.
					if ($stmt = $dbLink->prepare('RENAME TABLE lc_images_import TO lc_images')) {
						$stmt->execute();
						$stmt->close();
					}
					
					if ($stmt = $dbLink->prepare('DROP TABLE lc_images_old')) {
						$stmt->execute();
						$stmt->close();
					}
				}
			}
		}
		
		return $importResult;
	}
}

function recreate_lc_images($dbLink) {
	$importer = new lc_accession_importer();
	
	$importResult = $importer->lc_images($dbLink, 'lc_images_import');
	
	if ($importResult) {
		if ($stmt = $dbLink->prepare('DROP TABLE lc_images_old')) {
			$stmt->execute();
			$stmt->close();
		}
		
		if ($stmt = $dbLink->prepare('RENAME TABLE lc_images TO lc_images_old, '
				. ' lc_images_import TO lc_images')) {
			$stmt->execute();
			$stmt->close();
			
			// Just in case the old table didn't exist, just do the second RENAME TABLE.
			if ($stmt = $dbLink->prepare('RENAME TABLE lc_images_import TO lc_images')) {
				$stmt->execute();
				$stmt->close();
			}
			
			if ($stmt = $dbLink->prepare('DROP TABLE lc_images_old')) {
				$stmt->execute();
				$stmt->close();
			}
		}
	}
	
	return $importResult;
}
?>