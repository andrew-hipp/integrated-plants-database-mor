<?php

class ImportCommon {
	public static function dbExportDirPath() {
		global $app_root;
		include $app_root . '/config.inc.php';
		
		return $exportDataFilesPath;
	}

	public static function dbFileNames() {
		return array('HRB_ACCESSION.txt',
				'HRB_ACCOM.txt',
				'HRB_ANNOTATION.txt',
				'Lat_Long.txt',
				'LC_ACCESSION.txt',
				'LC_ACCOM.txt',
				'LC_PLANTCOM.txt',
				'LC_PLANTS.txt',
				'sciname_comments.txt',
				'SCINAME.txt');
	}

	public static function dbTableNameFromFileName($filename) {
		$parts = explode('.', $filename);
		array_pop($parts);
		return strtolower(implode('.', $parts));
	}

	protected function dbFieldInfo() {
		return array();
	}

	protected function abortImport($dbLink) {
		$filename = $this->getExportFileName();
		
		$delProg = 'DELETE FROM `longProcess` WHERE `key` = \'' . $filename . '\'';
		if ($delStmt = $dbLink->prepare($delProg)) {
			$delStmt->execute();
			$delStmt->close();
		}
		
		$dbLink->close();
	}
	
	public function startImport($new_table_name) {
		global $app_root;
		include $app_root . '/config.inc.php';

		$this->removeImportStatus();

		$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

		if (mysqli_connect_error()) {
			return false;
		}

		$filename = $this->getExportFileName();
		$fieldsInfo = $this->dbFieldInfo();

		$insProg = 'INSERT INTO `longProcess` (`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', \'importProgress\', \'0.0\')';
		if ($progressStmt = $dbLink->prepare($insProg)) {
			$progressStmt->execute();
			$progressStmt->close();
		}
		$insProg = 'INSERT INTO `longProcess` (`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', \'importPosition\', \'0\')';
		if ($progressStmt = $dbLink->prepare($insProg)) {
			$progressStmt->execute();
			$progressStmt->close();
		}
		$insProg = 'INSERT INTO `longProcess` (`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', \'new_table_name\', ?)';
		if ($progressStmt = $dbLink->prepare($insProg)) {
			$progressStmt->bind_param('s', $new_table_name);
			$progressStmt->execute();
			$progressStmt->close();
		}
		if ($progressStmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importComplete\'')) {
			$progressStmt->execute();
			$progressStmt->close();
		}

		if ($stmt = $dbLink->prepare('DROP TABLE IF EXISTS `' . $new_table_name . '`;')) {
			$stmt->execute();
			$stmt->close();
		}

		$import_dir_path = realpath($this->dbExportDirPath());

		$retVal = false;
		$fhand = @fopen($import_dir_path . '/' . $filename, 'r');
		if ($fhand) {
			fseek($fhand, 0, SEEK_END);
			$fileSize = ftell($fhand);
			fseek($fhand, 0, SEEK_SET);

			$insProg = 'INSERT INTO `longProcess` (`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', \'fileSize\', ?)';
			if ($progressStmt = $dbLink->prepare($insProg)) {
				$progressStmt->bind_param('s', $fileSize);
				$progressStmt->execute();
				$progressStmt->close();
			}
				
			// Validate the beginning of the export file contents.
			$line = chop(iconv($import_file_encoding, 'UTF-8', fgets($fhand, 16384)));
			$insProg = 'UPDATE `longProcess` SET `value`=? WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importPosition\'';
			if ($progressStmt = $dbLink->prepare($insProg)) {
				$curPos = ftell($fhand);
				$progressStmt->bind_param('s', $curPos);
				$progressStmt->execute();
				$progressStmt->close();
			}
			$lineNumber = 1;
			if (count(explode('|', $line)) == count($fieldsInfo)) {
				$prefixLine='';
				foreach ($fieldsInfo as $fieldInfo) {
					if ($prefixLine != '')
						$prefixLine .= '|';
					$prefixLine .= $fieldInfo['name'];
				}
				if ($line == $prefixLine) {
					$crResults = $this->createDBFromFields($dbLink, $new_table_name, $fieldsInfo);

					if (!$crResults['result'])
						return false;

					$insProg = 'INSERT INTO `longProcess` (`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', ?, ?)';
					if ($progressStmt = $dbLink->prepare($insProg)) {
						$c = 'insRowSQL';
						$progressStmt->bind_param('ss', $c, $crResults['insRowSQL']);
						$progressStmt->execute();
						
						$c = 'varTypes';
						$progressStmt->bind_param('ss', $c, $crResults['varTypes']);
						$progressStmt->execute();

						$s = join('|', $crResults['dateCols']);
						$c = 'dateCols';
						$progressStmt->bind_param('ss', $c, $s);
						$progressStmt->execute();

						$s = join('|', $crResults['intCols']);
						$c = 'intCols';
						$progressStmt->bind_param('ss', $c, $s);
						$progressStmt->execute();
						
						$s = join('|', $crResults['dblCols']);
						$c = 'dblCols';
						$progressStmt->bind_param('ss', $c, $s);
						$progressStmt->execute();
						
						$c = 'lineNumber';
						$progressStmt->bind_param('ss', $c, $lineNumber);
						$progressStmt->execute();
						
						$progressStmt->close();
						$retVal = true;
					}
				} else {
					doLogInfo('Export field line is not what is expected for ' . $filename);
				}
			}
			
			fclose($fhand);
		}
		
		$dbLink->close();
		
		return $retVal;
	}

	// True means don't call us again!
	public function continueImport($runSeconds) {
		global $app_root;
		include $app_root . '/config.inc.php';

		$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

		if (mysqli_connect_error()) {
			return false;
		}

		$filename = $this->getExportFileName();
		$fieldsInfo = $this->dbFieldInfo();

		$resProg = 'SELECT `value` FROM `longProcess` WHERE `key` = ? AND `subkey` = ?';
		if ($progressStmt = $dbLink->prepare($resProg)) {
			$progressStmt->bind_param('ss', $filename, $s);
			$progressStmt->bind_result($t);
			
			$s = 'new_table_name';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			
			$s = 'insRowSQL';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			$insRowSQL = $t;
			
			$s = 'varTypes';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			$varTypes = $t;
			
			$s = 'dateCols';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			if ($t != '') {
				$dateCols = explode('|', $t);
			} else {
				$dateCols = array();
			}
			
			$s = 'intCols';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			if ($t != '') {
				$intCols = explode('|', $t);
			} else {
				$intCols = array();
			}
			
			$s = 'dblCols';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			if ($t != '') {
				$dblCols = explode('|', $t);
			} else {
				$dblCols = array();
			}
			
			$s = 'fileSize';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			$fileSize = $t;
			
			$s = 'importPosition';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			$importPosition = $t;
			
			$s = 'lineNumber';
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			$lineNumber = $t;
			
			$progressStmt->close();
		}

		if (($stmt = $dbLink->prepare($insRowSQL)) === false) {
			$this->abortImport($dbLink);
			return true;
		}
		
		$import_dir_path = realpath($this->dbExportDirPath());

		$retVal = false;
		$fhand = @fopen($import_dir_path . '/' . $filename, 'r');
		if ($fhand) {
			fseek($fhand, $importPosition, SEEK_SET);
			
			$runEndTime = $runSeconds + time();	// Timeout time
			
			while ((time() < $runEndTime)
				&& !feof($fhand)
				&& (($line = chop(iconv($import_file_encoding, 'UTF-8', fgets($fhand, 16384)))) !== false)) {
				$importPosition = ftell($fhand);
				if ($progressStmt = $dbLink->prepare('UPDATE `longProcess` SET `value`=\''
					. sprintf('%.1f', 100.0 * $importPosition / $fileSize) . '\''
					. ' WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importProgress\'')) {
					$progressStmt->execute();
					$progressStmt->close();
				}
				++$lineNumber;
				// See if we can insert this row into the new table
				$fields = explode('|', $line);
				$fields = $this->preColCountFilter($fields);
				if (count($fields) > count($fieldsInfo)) {
					$this->abortImport($dbLink);
					return true;
				}
				$notEmpty = false;
				foreach ($fields as $field) {
					if ($field !== '') {
						$notEmpty = true;
						break;
					}
				}
				if ($notEmpty) {
					while (count($fields) < count($fieldsInfo))
						$fields[] = '';
					for ($idx = 0; $idx < count($fields); ++$idx) {
						$fields[$idx] = $this->columnDataFilter($idx, $fields[$idx]);
						if (in_array($idx, $dateCols)) {
							// Convert dd-mmm-yy format to 'yyyy-mm-dd'
							// If entry is empty, replace with NULL.
							if ($fields[$idx] == '') {
								$fields[$idx] = NULL;
							} else {
								$dateArray = date_parse_from_format('d M Y', $fields[$idx]);
								$fields[$idx] = sprintf('%04d-%02d-%02d', $dateArray['year'], $dateArray['month'], $dateArray['day']);
							}
						} else if (in_array($idx, $intCols)) {
							if ($fields[$idx] == '') {
								$fields[$idx] = NULL;
							} else {
								$fields[$idx] = intval($fields[$idx]);
							}
						} else if (in_array($idx, $dblCols)) {
							if ($fields[$idx] == '') {
								$fields[$idx] = NULL;
							} else {
								$fields[$idx] = doubleval($fields[$idx]);
							}
						} else if ($fields[$idx] == '') {
							$fields[$idx] = 'NULL';
						}
					}
					$bind_params=array($varTypes);
					for ($idx = 0; $idx < count($fields); ++$idx) {
						$bind_params[] = &$fields[$idx];
					}
					call_user_func_array(array($stmt,'bind_param'), $bind_params);
					$stmt->execute();
					if ($stmt->errno != 0) {
						doLogInfo('Error ' . $stmt->error . '(' . $stmt->errno . ') creating SQL on line ' . $lineNumber . ' of ' . $filename);
						$this->abortImport($dbLink);
						return true;
					}
				}
			}

			if (feof($fhand)) {
				$delProg = 'DELETE FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=?';
				if ($delProg = $dbLink->prepare($delProg)) {
					foreach (array('importPosition',
						'insRowSQL',
						'varTypes',
						'dateCols',
						'intCols',
						'dblCols',
						'fileSize',
						'lineNumber') as $colName) {
						$delProg->bind_param('s', $colName);
						$delProg->execute();
					}
				}
				$retVal = true;
			} else {
				$insProg = 'UPDATE `longProcess` SET `value`=? WHERE `key`=\'' . $filename . '\' AND `subkey`=?';
				if ($progressStmt = $dbLink->prepare($insProg)) {
					$s = 'importPosition';
					$progressStmt->bind_param('ss', $importPosition, $s);
					$progressStmt->execute();
					
					$s = 'lineNumber';
					$progressStmt->bind_param('ss', $lineNumber, $s);
					$progressStmt->execute();
					
					$progressStmt->close();
				}
			}
			
			fclose($fhand);
		}
		
		$dbLink->close();
		
		return $retVal;
	}
	
	public function finishImport() {
		global $app_root;
		include $app_root . '/config.inc.php';

		$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

		if (mysqli_connect_error()) {
			return false;
		}

		$filename = $this->getExportFileName();

		$resProg = 'SELECT `value` FROM `longProcess` WHERE `key` = ? AND `subkey` = ?';
		if ($progressStmt = $dbLink->prepare($resProg)) {
			$s = 'new_table_name';
			$progressStmt->bind_param('ss', $filename, $s);
			$progressStmt->bind_result($t);
			$progressStmt->execute();
			if (!$progressStmt->fetch()) {
				$this->abortImport($dbLink);
				return true;
			}
			$new_table_name = $t;
			
			$progressStmt->close();
		}
		
		$indexes = $this->getAddlIndexesArray();
			
		foreach ($indexes as $index) {
			$retVal = false;
			$newIndexSQL = 'ALTER TABLE `' . $new_table_name . '` ADD INDEX `' . $index['name'] . '` (`' . $index['field'] . '`)';
			if (($stmt = $dbLink->prepare($newIndexSQL)) !== false) {
				$stmt->execute();
				$stmt->close();
				$retVal = true;
			} else {
				break;
			}
		}

		$retVal = $this->postImportFilter($dbLink, $new_table_name, true);

		if ($progressStmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importProgress\'')) {
			$progressStmt->execute();
			$progressStmt->close();
		}

		if ($progressStmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=\'new_table_name\'')) {
			$progressStmt->execute();
			$progressStmt->close();
		}

		if ($progressStmt = $dbLink->prepare('INSERT INTO `longProcess`(`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', \'importComplete\', \'' . ($retVal ? '1' : '0') . '\');')) {
			$progressStmt->execute();
			$progressStmt->close();
		}
		
		$filename = $this->getExportFileName();
		$tableName = $this->dbTableNameFromFileName($filename);
		
		if ($stmt = $dbLink->prepare('DROP TABLE `' . $tableName . '_old`')) {
			$stmt->execute();
			$stmt->close();
			
		}
		
		if ($stmt = $dbLink->prepare('RENAME TABLE `' . $tableName . '` TO `' . $tableName . '_old`,'
				. '`' . $new_table_name . '` TO `' . $tableName . '`')) {
			$stmt->execute();
			$stmt->close();
		}
		
		if ($stmt = $dbLink->prepare('RENAME TABLE `' . $new_table_name . '` TO `' . $tableName . '`')) {
			$stmt->execute();
			$stmt->close();
		}
		
		if ($stmt = $dbLink->prepare('DROP TABLE `' . $tableName . '_old`')) {
			$stmt->execute();
			$stmt->close();
			
		}
		
		$dbLink->close();

		return $retVal;
	}

	public function removeImportStatus() {
		global $app_root;
		include $app_root . '/config.inc.php';

		$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

		if (mysqli_connect_error()) {
			return false;
		}

		$filename = $this->getExportFileName();

		if ($progressStmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importComplete\'')) {
			$progressStmt->execute();
			$progressStmt->close();
		}

		$dbLink->close();
	}

	public function isImportComplete() {
		$retVal = false;

		global $app_root;
		include $app_root . '/config.inc.php';

		$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

		if (mysqli_connect_error()) {
			return false;
		}

		$filename = $this->getExportFileName();

		if ($progressStmt = $dbLink->prepare('SELECT value FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importComplete\'')) {
			$progressStmt->execute();

			$progressStmt->bind_result($value);

			if ($progressStmt->fetch()) {
				$retVal = ($value == '1');
			}

			$progressStmt->close();
		}

		$dbLink->close();

		return $retVal;
	}

	public function importNow($new_table_name) {
		global $app_root;
		include $app_root . '/config.inc.php';

		$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

		if (mysqli_connect_error()) {
			return false;
		}

		$filename = $this->getExportFileName();
		$fieldsInfo = $this->dbFieldInfo();

		$insProg = 'INSERT INTO `longProcess` (`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', \'importProgress\', \'0.0\')';
		if ($progressStmt = $dbLink->prepare($insProg)) {
			$progressStmt->execute();
			$progressStmt->close();
		}
		if ($progressStmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importComplete\'')) {
			$progressStmt->execute();
			$progressStmt->close();
		}

		if ($stmt = $dbLink->prepare('DROP TABLE IF EXISTS `' . $new_table_name . '`;')) {
			$stmt->execute();
			$stmt->close();
		}

		$import_dir_path = realpath($this->dbExportDirPath());

		$retVal = false;
		$fhand = @fopen($import_dir_path . '/' . $filename, 'r');
		if ($fhand) {
			fseek($fhand, 0, SEEK_END);
			$fileSize = ftell($fhand);
			fseek($fhand, 0, SEEK_SET);

			// Validate the beginning of the export file contents.
			$line = chop(iconv($import_file_encoding, 'UTF-8', fgets($fhand, 16384)));
			$lineNumber = 1;
			if (count(explode('|', $line)) == count($fieldsInfo)) {
				$prefixLine='';
				foreach ($fieldsInfo as $fieldInfo) {
					if ($prefixLine != '')
					$prefixLine .= '|';
					$prefixLine .= $fieldInfo['name'];
				}
				if ($line == $prefixLine) {
					$crResults = $this->createDBFromFields($dbLink, $new_table_name, $fieldsInfo);

					if (!$crResults['result'])
					return false;

					$insRowSQL = $crResults['insRowSQL'];
					$varTypes = $crResults['varTypes'];
					$dateCols = $crResults['dateCols'];
					$intCols = $crResults['intCols'];
					$dblCols = $crResults['dblCols'];

					if (($stmt = $dbLink->prepare($insRowSQL)) !== false) {
						while (!feof($fhand)
						&& (($line = chop(fgets($fhand, 16384))) !== false)) {
							if ($progressStmt = $dbLink->prepare('UPDATE `longProcess` SET `value`=\''
							. sprintf('%.1f', 100.0 * ftell($fhand) / $fileSize) . '\''
							. ' WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importProgress\'')) {
								$progressStmt->execute();
								$progressStmt->close();
							}
							++$lineNumber;
							// See if we can insert this row into the new table
							$fields = explode('|', $line);
							$fields = $this->preColCountFilter($fields);
							if (count($fields) > count($fieldsInfo)) {
								break;
							}
							$notEmpty = false;
							foreach ($fields as $field) {
								if ($field !== '') {
									$notEmpty = true;
									break;
								}
							}
							if ($notEmpty) {
								while (count($fields) < count($fieldsInfo))
								$fields[] = '';
								for ($idx = 0; $idx < count($fields); ++$idx) {
									$fields[$idx] = $this->columnDataFilter($idx, $fields[$idx]);
									if (in_array($idx, $dateCols)) {
										// Convert dd-mmm-yy format to 'yyyy-mm-dd'
										// If entry is empty, replace with NULL.
										if ($fields[$idx] == '') {
											$fields[$idx] = NULL;
										} else {
											$dateArray = date_parse_from_format('d M Y', $fields[$idx]);
											$fields[$idx] = sprintf('%04d-%02d-%02d', $dateArray['year'], $dateArray['month'], $dateArray['day']);
										}
									} else if (in_array($idx, $intCols)) {
										if ($fields[$idx] == '') {
											$fields[$idx] = NULL;
										} else {
											$fields[$idx] = intval($fields[$idx]);
										}
									} else if (in_array($idx, $dblCols)) {
										if ($fields[$idx] == '') {
											$fields[$idx] = NULL;
										} else {
											$fields[$idx] = doubleval($fields[$idx]);
										}
									} else if ($fields[$idx] == '') {
										$fields[$idx] = 'NULL';
									}
								}
								$bind_params=array($varTypes);
								for ($idx = 0; $idx < count($fields); ++$idx) {
									$bind_params[] = &$fields[$idx];
								}
								call_user_func_array(array($stmt,'bind_param'), $bind_params);
								$stmt->execute();
								if ($stmt->errno != 0) {
									doLogInfo('Error ' . $stmt->error . '(' . $stmt->errno . ') creating SQL on line ' . $lineNumber . ' of ' . $filename);
									break;
								}
							}
						}

						$stmt->close();
					}

					if (!feof($fhand)) {
						// Drop table, it wasn't properly loaded!
						if ($stmt = $dbLink->prepare('DROP TABLE `' . $new_table_name . '`')) {
							$stmt->execute();
							$stmt->close();
						}
					} else {
						$retVal = true;
					}
				}
			}

			fclose($fhand);
		}

		if ($retVal) {
			$indexes = $this->getAddlIndexesArray();
				
			foreach ($indexes as $index) {
				$retVal = false;
				$newIndexSQL = 'ALTER TABLE `' . $new_table_name . '` ADD INDEX `' . $index['name'] . '` (`' . $index['field'] . '`)';
				if (($stmt = $dbLink->prepare($newIndexSQL)) !== false) {
					$stmt->execute();
					$stmt->close();
					$retVal = true;
				} else {
					break;
				}
			}
		}

		$retVal = $this->postImportFilter($dbLink, $new_table_name, $retVal);

		if ($progressStmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'' . $filename . '\' AND `subkey`=\'importProgress\'')) {
			$progressStmt->execute();
			$progressStmt->close();
		}

		if ($progressStmt = $dbLink->prepare('INSERT INTO `longProcess`(`key`,`subkey`,`value`) VALUES (\'' . $filename . '\', \'importComplete\', \'' . ($retVal ? '1' : '0') . '\');')) {
			$progressStmt->execute();
			$progressStmt->close();
		}

		$dbLink->close();

		return $retVal;
	}

	protected function preColCountFilter($fields) {
		return $fields;
	}

	protected function columnDataFilter($idx, $field) {
		return $field;
	}

	protected function getAddlIndexesArray() {
		// return an array of array('name'=>'index_name', 'field'=>'field_to_index')
		return array();
	}

	protected function postImportFilter($dbLink, $new_table_name, $importResult) {
		return $importResult;
	}

	private static function doLogInfo($message) {
		echo $message . "\n";
	}

	protected function createDBFromFields($dbLink, $new_table_name, $fieldsInfo) {
		// OK, the beginning of the file matches what we expect, so
		// create the new table with specified name in the database.
		$intCols = array();
		$dblCols = array();
		$dateCols = array();
		$crTableSQL = 'CREATE TABLE `' . $new_table_name . '` ('
		. '`id` INT(11) NOT NULL AUTO_INCREMENT, ';
		$insRowSQL = 'INSERT INTO `' . $new_table_name . '` (';
		$varTypes = '';
		$insRowParams = '';
		for ($idx = 0; $idx < count($fieldsInfo); ++$idx) {
			$fieldInfo = $fieldsInfo[$idx];
			$crTableSQL .= '`' . $fieldInfo['name'] . '` ';
			if ($idx != 0) {
				$insRowSQL .= ', ';
				$insRowParams .= ',';
			}
			$insRowSQL .= '`' . $fieldInfo['name'] . '`';
			$insRowParams .= '?';
			if (array_key_exists('def', $fieldInfo)) {
				$crTableSQL .= $fieldInfo['def'];
				switch (strtoupper(substr($fieldInfo['def'], 0, 3))) {
					case 'INT':
						$intCols[] = $idx;
						$varTypes .= 'i';
						break;

					case 'REA':
						$dblCols[] = $idx;
						$varTypes .= 'd';
						break;

					case 'DAT':
						$dateCols[] = $idx;
						$varTypes .= 's';
						break;

					default:
						$varTypes .= 's';
				}
			} else {
				$crTableSQL .= 'VARCHAR(256)';
				$varTypes .= 's';
			}
			$crTableSQL .= ', ';
		}
		$crTableSQL .= 'PRIMARY KEY ( `id` ) ,'
		. 'UNIQUE (`id`)'
		. ') CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
		$insRowSQL .= ') VALUES (' . $insRowParams . ');';

		// Create the table in the database.
		if (($stmt = $dbLink->prepare($crTableSQL)) === false) {
			return array('result'=>false, 'insRowSQL'=>'', 'varTypes'=>'');
		}
		$stmt->execute();
		$stmt->close();

		return array('result'=>true,
				'insRowSQL'=>$insRowSQL,
				'varTypes'=>$varTypes,
				'dateCols'=>$dateCols,
				'intCols'=>$intCols,
				'dblCols'=>$dblCols);
	}
}

?>
