<?php
// parameters:
//		id=lc_accession
// or
//		sciname=urlencode(scientific_name_id)
$app_root = realpath('.');
require_once $app_root . '/modules/page_intro_common.php';
// parameters:
//	 	id=lc_accession
if (!isset($_REQUEST['id'])
	&& !isset($_REQUEST['sciname'])) {
	header('Location: ' . getRootUrl() . '/');
	return;
}

require_once $app_root . '/config.inc.php';

$image_url = '';

$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

$thumbnails = array();

$id_type = 'id';
$id_value = '';
if (mysqli_connect_error() == 0) {
	if (isset($_REQUEST['id'])) {
		if ($stmt=$dbLink->prepare('SELECT DISTINCT lc_accession.sciname_id,'
			. ' sciname.family_name, sciname.common_names, sciname.usda_zone_lo,'
			. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
			. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
			. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark, sciname.range'
			. ' FROM lc_accession, sciname'
			. ' WHERE lc_accession.accession_no = ?'
			. ' AND lc_accession.sciname_id = sciname.scientific_name_id')) {
			$stmt->bind_param('s', $_REQUEST['id']);
			
			$stmt->execute();
			
			$stmt->bind_result($sciname_id,
				$sciname['family_name'], $sciname['common_names'], $sciname['usda_zone_lo'],
				$sciname['usda_zone_hi'], $sciname['genus_designator'], $sciname['genus_name'], $sciname['species_designator'],
				$sciname['species_name'], $sciname['subspecies_name'], $sciname['variety_name'], $sciname['forma_name'], $sciname['cultivar_name'],
				$sciname['author_name'], $sciname['plant_patent_no'], $sciname['family_common_name'], $sciname['trademark'], $sciname['range']);
			
			if ($stmt->fetch()) {
				$stmt->close();
				
				if ($stmt=$dbLink->prepare('SELECT DISTINCT image_thumb FROM lc_images WHERE sciname_id = ?')) {
					$sciname_id = substr($sciname_id, 0, 14);
					$stmt->bind_param('s', $sciname_id);
					
					$stmt->execute();
					
					$stmt->bind_result($image_thumb);
					
					while ($stmt->fetch()) {
						$thumbnails[] = $image_thumb;
					}
					
					$stmt->close();
					
					$id_type = 'id';
					$id_value = $_REQUEST['id'];
				}
			} else {
				$stmt->close();
			}
		} else {
			echo $dbLink->error;
		}
	} elseif (isset($_REQUEST['sciname'])) {
		$sciname_in=urldecode($_REQUEST['sciname']);
		if ($stmt=$dbLink->prepare('SELECT DISTINCT sciname.scientific_name_id,'
			. ' sciname.family_name, sciname.common_names, sciname.usda_zone_lo,'
			. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
			. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
			. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark, sciname.range'
			. ' FROM lc_accession, sciname'
			. ' WHERE sciname.scientific_name_id = ?')) {
			$stmt->bind_param('s', $sciname_in);
			
			$stmt->execute();
			
			$stmt->bind_result($sciname_id,
				$sciname['family_name'], $sciname['common_names'], $sciname['usda_zone_lo'],
				$sciname['usda_zone_hi'], $sciname['genus_designator'], $sciname['genus_name'], $sciname['species_designator'],
				$sciname['species_name'], $sciname['subspecies_name'], $sciname['variety_name'], $sciname['forma_name'], $sciname['cultivar_name'],
				$sciname['author_name'], $sciname['plant_patent_no'], $sciname['family_common_name'], $sciname['trademark'], $sciname['range']);
			
			if ($stmt->fetch()) {
				$stmt->close();
				
				if ($stmt=$dbLink->prepare('SELECT DISTINCT image_thumb FROM lc_images WHERE sciname_id = ?')) {
					$sciname_id = substr($sciname_id, 0, 14);
					$stmt->bind_param('s', $sciname_id);
					
					$stmt->execute();
					
					$stmt->bind_result($image_thumb);
					
					while ($stmt->fetch()) {
						$thumbnails[] = $image_thumb;
					}
					
					$stmt->close();
					
					$id_type = 'sci';
					$id_value = $sciname_in;
				}
			} else {
				$stmt->close();
			}
		} else {
			echo $dbLink->error;
		}
	}
	
	$dbLink->close();
}

if (count($thumbnails) == 0) {
	header('Location: ' . getRootUrl() . '/');
}

require_once 'modules/page_tab_Intro_common.php';
require_once 'modules/collections_common.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>Morton Arboretum</title>
  <link rel="stylesheet" type="text/css" href="libraries/main.php" />
  <script type="text/javascript" src="libraries/main.js"></script>
</head>
<body>

<form name="gotoPageForm" action="." method="post">
<input type="hidden" name="page" value=""/>
</form>

<?php
$pageLocation = 'Search Results';
require_once 'modules/header_common.php';
?>	<div id="body" class="body">
		<div id="content" class="content">
<?php
showTabs($pages_info, null, 'image_browser');

require_once $app_root . '/modules/sciname_common.php';
$resultData = array();
?>
<b><?php sciname_long($resultData, $sciname); ?></b>
			<hr />
			<p>
<?php
			foreach ($thumbnails as $thumbnail) {
				echo '				<a href="';
				echo getRootUrl() . '/image_detail.php?type=' . (($id_type == 'id') ? 'lc' : 'sci') . '&' . $id_type . '=' . $id_value . '&name=' . basename($thumbnail) . '">';
				echo '<img border="2" src="' . getRootUrl() . '/' . $thumbnail . '" /></a>' . "\n";
			}
?>			</p>
		</div>
	</div>
<div class="image_footer">
<?php 
require_once $app_root . '/modules/footer_common.php';
?>
</div>
</body>
</html>
