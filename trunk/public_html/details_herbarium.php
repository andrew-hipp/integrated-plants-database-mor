<?php
// added Google Analytics code before </head> - EAH 2012-04-18
$app_root = realpath('.');
require_once $app_root . '/modules/page_intro_common.php';
require_once $app_root . '/config.inc.php';

$resultData = array();
if (isset($_REQUEST['id']) && ($_REQUEST['id'] != '')) {
	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	
	if (mysqli_connect_error() == 0) {
		if ($stmt = $dbLink->prepare('SELECT hrb_accession.id, hrb_accession.herb_nbr, sciname.scientific_name, sciname.common_names,'
				. ' hrb_accession.country, hrb_accession.subctry1, hrb_accession.subctry2, hrb_accession.desig1,'
				. ' hrb_accession.habitat, hrb_accession.site, hrb_accession.lat, hrb_accession.lat_dir, hrb_accession.long,'
				. ' hrb_accession.long_dir, hrb_accession.elev_units, hrb_accession.associates,'
				. ' hrb_accession.collector_primary, hrb_accession.collector_primary_ln, hrb_accession.collector_addl, hrb_accession.project,'
				. ' hrb_accession.coll_date, hrb_accession.coll_date_acc, hrb_accession.image_thumb, hrb_accession.image_large,'
				. ' hrb_accession.image_addl, hrb_accession.range, hrb_accession.range_dir, hrb_accession.twp,'
				. ' hrb_accession.twp_dir, hrb_accession.sect, hrb_accession.sect_desc,'
				. ' hrb_accession.elev_low, hrb_accession.elev_upper, hrb_accession.elev_units, '
				. ' hrb_accession.collector_no, hrb_accession.lc_acc, hrb_accession.lc_plant,'
				. ' hrb_accession.orig_id, hrb_accession.annot_by, hrb_accession.addl_annot_by, '
				. ' hrb_accession.annot_dt, hrb_accession.annot_date_acc, hrb_accession.annot_comments, '
				. ' sciname.family_name, sciname.common_names, sciname.sort_scientific_name, sciname.usda_zone_lo,'
				. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
				. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
				. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark, sciname.range'
				. ' FROM hrb_accession, sciname WHERE hrb_accession.sciname_id = sciname.scientific_name_id AND hrb_accession.herb_nbr = ?')) {
			$stmt->bind_param('i', $_REQUEST['id']);
			
			$stmt->execute();
			
			$stmt->bind_result($resultData['herb_id'], $resultData['herb_herb_nbr'], $sciname['scientific_name'],
					$sciname['common_names'],
					$resultData['herb_country'], $resultData['herb_subctry1'], $resultData['herb_subctry2'], $resultData['desig1'],
					$resultData['herb_habitat'], $resultData['herb_site'], $resultData['herb_lat'], $resultData['herb_lat_dir'], $resultData['herb_long'],
					$resultData['herb_long_dir'], $resultData['herb_elev_units'], $resultData['herb_associates'],
					$resultData['herb_collector_primary'], $resultData['herb_collector_primary_ln'], $resultData['herb_collector_addl'], $resultData['herb_project'],
					$resultData['herb_coll_date'], $resultData['herb_coll_date_acc'], $resultData['image_thumb'], $resultData['image_large'],
					$resultData['image_addl'], $resultData['herb_range'], $resultData['herb_range_dir'],
					$resultData['herb_twp'], $resultData['herb_twp_dir'], $resultData['herb_sect'], $resultData['herb_sect_desc'],
					$resultData['herb_elev_low'], $resultData['herb_elev_upper'], $resultData['herb_elev_units'],
					$resultData['herb_collector_no'], $resultData['herb_lc_acc'], $resultData['herb_lc_plant'],
					$resultData['herb_orig_id'], $resultData['herb_annot_by'], $resultData['herb_addl_annot_by'],
					$resultData['herb_annot_dt'], $resultData['herb_annot_date_acc'], $resultData['herb_annot_comments'],
					$sciname['family_name'], $sciname['common_names'], $sciname['sort_scientific_name'], $sciname['usda_zone_lo'],
					$sciname['usda_zone_hi'], $sciname['genus_designator'], $sciname['genus_name'],
					$sciname['species_designator'], $sciname['species_name'], $sciname['subspecies_name'],
					$sciname['variety_name'], $sciname['forma_name'], $sciname['cultivar_name'],
					$sciname['author_name'], $sciname['plant_patent_no'], $sciname['family_common_name'],
					$sciname['trademark'], $sciname['range']);
			
			if ($stmt->fetch()) {
				$resultData['sciname_id'] = 0;
				$sciname['common_names'] = strtolower($sciname['common_names']);
				$resultData['collector'] = $resultData['herb_collector_primary'];
				if (!isNull($resultData['herb_collector_addl']) && ($resultData['herb_collector_addl'] != '')) {
					$resultData['collector'] .= ', ' . $resultData['herb_collector_addl'];
				}
				
				$coll_date_parts = explode('-', sprintf('%s', $resultData['herb_coll_date']));
				switch ($resultData['herb_coll_date_acc']) {
					case 'D':
						$resultData['herb_coll_date'] = date('d M Y', mktime(0, 0, 0, $coll_date_parts[1], $coll_date_parts[2], $coll_date_parts[0]));
						break;
						
					case 'M':
						$resultData['herb_coll_date'] = date('M Y', mktime(0, 0, 0, $coll_date_parts[1], $coll_date_parts[2], $coll_date_parts[0]));
						break;
						
					case 'Y':
						$resultData['herb_coll_date'] = date('Y', mktime(0, 0, 0, $coll_date_parts[1], $coll_date_parts[2], $coll_date_parts[0]));
						break;
				}
				
				if (!isNull($resultData['herb_range']) && !isNull($resultData['herb_range_dir'])
						&& !isNull($resultData['herb_twp']) && !isNull($resultData['herb_twp_dir'])
						&& !isNull($resultData['herb_sect']) && !isNull($resultData['herb_sect_desc'])) {
					$resultData['survey'] = 'R' . $resultData['herb_range'] . $resultData['herb_range_dir']
							. ' T' . $resultData['herb_twp'] . $resultData['herb_twp_dir']
							. ' Section' . $resultData['herb_sect'] . ', ' . $resultData['herb_sect_desc'];
				}
				
				if (!isNull($resultData['herb_elev_low']) && !isNull($resultData['herb_elev_upper'])) {
					$resultData['elevation'] = $resultData['herb_elev_low'] . ' - ' . $resultData['herb_elev_upper'] . ' ' . $resultData['herb_elev_units'];
				} elseif (!isNull($resultData['herb_elev_low'])) {
					$resultData['elevation'] = $resultData['herb_elev_low'] . ' ' . $resultData['herb_elev_units'];
				}

				if (!isNull($resultData['herb_lc_plant'])) {
					$resultData['assocLivingColl'] = $resultData['herb_lc_plant'];
				} elseif (!isNull($resultData['herb_lc_acc'])) {
					$resultData['assocLivingColl'] = $resultData['herb_lc_acc'];
				}
				
				if (!isNull($resultData['herb_lat_dir']) && !isNull($resultData['herb_lat']) && !isNull($resultData['herb_long_dir']) && !isNull($resultData['herb_long'])) {
					$resultData['coordinates'] = abs($resultData['herb_lat']) . '&deg; ' . $resultData['herb_lat_dir']
						. ', ' . abs($resultData['herb_long']) . '&deg; ' . $resultData['herb_long_dir'];
				}
				
				if (!isNull($resultData['herb_annot_by'])
					&& !isNull($resultData['herb_annot_dt'])
					&& !isNull($resultData['herb_annot_date_acc'])) {
					$resultData['last_annotation'] = $resultData['herb_annot_by'];
					if (!isNull($resultData['herb_addl_annot_by']) && ($resultData['herb_addl_annot_by'] != '')) {
						$resultData['last_annotation'] .= ', ' . $resultData['herb_addl_annot_by'];
					}
					
					$resultData['last_annotation'] .= ' on ';
					$dateParts = explode('-', $resultData['herb_annot_dt']);
					$theDate = mktime(0, 0, 0, $dateParts[1], $dateParts[2], $dateParts[0]);
					switch ($resultData['herb_annot_date_acc']) {
						case 'D':
							$resultData['last_annotation'] .= date('d M Y', $theDate);
							break;
							
						case 'M':
							$resultData['last_annotation'] .= date('M Y', $theDate);
							break;
							
						case 'Y':
							$resultData['last_annotation'] .= date('Y', $theDate);
							break;
					}
					
					if (!isNull($resultData['herb_annot_comments'])) {
						$resultData['last_annotation'] .= ': ' . $resultData['herb_annot_comments'];
					}
				}
								
				$stmt->close();
				
				if (!isNull($resultData['herb_lc_acc'])) {
					if ($stmt = $dbLink->prepare('SELECT count(accession_no)  FROM lc_accession WHERE accession_no = ?')) {
						$stmt->bind_param('s', $resultData['herb_lc_acc']);
						
						$stmt->execute();
						
						$stmt->bind_result($resultData['assocLivingCollCount']);
						
						if (!$stmt->fetch() || ($resultData['assocLivingCollCount'] == 0)) {
							unset($resultData['assocLivingColl']);
						}
						
						$stmt->close();
					}
				}
				
				if (!isNull($resultData['herb_orig_id'])) {
					if ($stmt = $dbLink->prepare('SELECT scientific_name FROM sciname WHERE scientific_name_id = ?')) {
						$stmt->bind_param('s', $resultData['herb_orig_id']);
						
						$stmt->execute();
						
						$stmt->bind_result($resultData['originalName']);
						
						if (!$stmt->fetch()) {
							unset($resultData['originalName']);
						}
						
						$stmt->close();
					}
				}
				
				// Comments come from hrb_accom where herb_no = id (separate with spaces)
				$resultData['comments'] = '';
				if ($stmt = $dbLink->prepare('SELECT seq_no, acc_comment FROM hrb_accom WHERE herb_no = ? ORDER BY seq_no')) {
					$stmt->bind_param('i', $_REQUEST['id']);
					
					$stmt->execute();
					
					$stmt->bind_result($seq_no, $comment);
					
					while ($stmt->fetch()) {
							$resultData['comments'] .= ' ' . $comment;
					}
					
					$resultData['comments'] = ltrim(trim($resultData['comments']));
					
					$stmt->close();
				}
				
				// Attributes come from hrb_accession.attributes where herb_nbr = id
				$resultData['attributes'] = '';
				if ($stmt = $dbLink->prepare('SELECT attributes FROM hrb_accession WHERE herb_nbr = ?')) {
					$stmt->bind_param('i', $_REQUEST['id']);
					
					$stmt->execute();
					
					$stmt->bind_result($attributes);
					
					while ($stmt->fetch()) {
						if(isset($resultData['attributes']))
						{
							$resultData['attributes'] .= ' ' . $attributes;
						}
					}
										
					$stmt->close();
				}				
				
				//$resultData['genetic_data'] = array('&lt;&lt;UNKNOWN&gt;&gt;');
			} else {
				$resultData = array();
				$stmt->close();
			}
		}
		
		$dbLink->close();
	}
}

if (count($resultData) == 0) {
	header('Location: ' . getRootUrl() . '/');
	return;
}

require_once 'modules/page_tab_Intro_common.php';
require_once 'modules/collections_common.php';
if (!isset($_REQUEST['download_file'])) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>Morton Arboretum</title>
  <link rel="stylesheet" type="text/css" href="libraries/main.php" />
  <script type="text/javascript" src="libraries/main.js"></script>
//Google Analytics code follows - EAH 2012-04-18
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30991297-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
	<form name="gotoPageForm" action="." method="post">
		<input type="hidden" name="page" value=""></input>
	</form>
<?php
$pageLocation = 'Search Results';
require_once 'modules/header_common.php';
?>	<div id="body" class="body">
		<div id="content" class="content">
<?php
showTabs($pages_info, null, 'results');
?>			<table border="0" width="100%">
				<tr>
					<td>
						<table border="0" width="100%">
							<tr>
								<td>
									<b>Herbarium accession <?php
echo $resultData['herb_herb_nbr'];
?> &mdash; <?php
require_once $app_root . '/modules/sciname_common.php';

sciname_long($resultData, $sciname);
?></b>
								</td>
								<td rowspan="2">
									<button name="download_file" type="button" onclick="javascript:window.location='<?php echo getRootUrl(); ?>/details_herbarium.php?download_file=&<?php echo $_SERVER['QUERY_STRING']; ?>';">Download file</button>
								</td>
							</tr>
							<tr>
								<td><hr /></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center">
						<table border="0" width="95%">
							<tr valign="top">
								<td>
									<table border="0" width="100%">
<?php
$fields = array();
$fields[] = array('desc'=>'Country', 'idx'=>'herb_country');
$fields[] = array('desc'=>'State', 'idx'=>'herb_subctry1');
$fields[] = array('desc'=>'County', 'idx'=>'herb_subctry2');
$fields[] = array('desc'=>'Locality', 'idx'=>'herb_site');
$fields[] = array('desc'=>'Survey', 'idx'=>'survey');
$fields[] = array('desc'=>'Coordinates', 'idx'=>'coordinates');
$fields[] = array('desc'=>'Elevation', 'idx'=>'elevation');
$fields[] = array('desc'=>'Original&nbsp;Name', 'idx'=>'originalName');
$fields[] = array('desc'=>'Habitat', 'idx'=>'herb_habitat');
$fields[] = array('desc'=>'Associates', 'idx'=>'herb_associates');
$fields[] = array('desc'=>'Comments', 'idx'=>'comments');
$fields[] = array('desc'=>'Attributes', 'idx'=>'attributes');
$fields[] = array('desc'=>'Collected&nbsp;by', 'idx'=>'collector');
$fields[] = array('desc'=>'Project', 'idx'=>'herb_project');
$fields[] = array('desc'=>'Coll.&nbsp;#', 'idx'=>'herb_collector_no');
$fields[] = array('desc'=>'Coll.&nbsp;date', 'idx'=>'herb_coll_date');
$fields[] = array('desc'=>'Accession&nbsp;#', 'idx'=>'herb_herb_nbr');
$fields[] = array('desc'=>'Last&nbsp;Annotation', 'idx'=>'last_annotation');
// $fields[] = array('desc'=>'Range','idx'=>'herb_range');

foreach ($fields as $field) {
	if (isset($resultData[$field['idx']]) && 
	    !isNull($resultData[$field['idx']]) && 
	    ($resultData[$field['idx']] != '') &&
	    !($resultData[$field['idx']] === NULL) &&
	    $resultData[$field['idx']] != ' NULL') {
?>										<tr valign="top">
											<td width="1px" 
align="right"><b><?php echo $field['desc']; ?>:</b></td>
											<td>&nbsp;</td>
											<td align="left"><?php echo 
$resultData[$field['idx']]; ?></td>
										</tr>
<?php
	}
}
if (isset($resultData['genetic_data'])) {
?>										<tr valign="top">
											<td><b>Genetic&nbsp;data:</b></td>
											<td>
<?php
	foreach ($resultData['genetic_data'] as $gen_data) {
		echo '												' . $gen_data . '<br />' . "\n";
	}
?>											</td>
										</tr>
<?php
}
?>									</table>
								</td>
<?php
if (($resultData['image_thumb'] != NULL)
		&& ($resultData['image_thumb'] != 'NULL')
		&& ($resultData['image_large'] != NULL)
		&& ($resultData['image_large'] != 'NULL')) {
?>								<td>
									<a href="<?php echo $resultData['image_large']; ?>"><img src="<?php echo $resultData['image_thumb']; ?>" caption="Click for full-size image"></img></a>
								</td>
<?php
}
?>							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr /></a>
				</tr>
<?php
if (isset($resultData['assocLivingColl'])) {
?>				<tr>
					<table border="0" width="100%">
						<tr>
							<td colspan="2">
								<b>Associated Living Collections specimens</b>
							</td>
						</tr>
						<tr>
							<td width="10px"></td>
							<td>
								<a href="<?php echo getRootUrl(); ?>/details_living_coll.php?id=<?php echo $resultData['herb_lc_acc']; ?>"><b><font color="red"><?php echo $resultData['assocLivingColl'] ?></font></b></a> &mdash; <?php echo $resultData['assocLivingCollCount']; ?> plant<?php if ($resultData['assocLivingCollCount'] != 1) { echo 's'; } ?>
							</td>
						</tr>
					</table>
				</tr>
<?php
}
?>			</table>
		</div>
	</div>
</body>
</html>
<?php
} else {
	require_once $app_root . '/modules/downloadEntryRow.php';
	
	$resultData['db'] = 'hrb';
	
	dump_results('morton_details.csv', 'hrb', array($resultData), array('0'=>$sciname));
}
?>
