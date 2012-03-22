<?php
$app_root = realpath('.');
require_once $app_root . '/modules/page_intro_common.php';
// parameters:
//		type={hrb|lc}
//	 	id={hrb_accession|lc_accession}
//		name=image name (required only for LC)
if (!isset($_REQUEST['type'])
	|| (($_REQUEST['type'] != 'hrb') && ($_REQUEST['type'] != 'lc') && ($_REQUEST['type'] != 'sci'))
	|| (($_REQUEST['type'] != 'sci') && !isset($_REQUEST['id']))
	|| (($_REQUEST['type'] == 'lc') && !isset($_REQUEST['name']))) {
	header('Location: ' . getRootUrl() . '/');
	return;
}

require_once $app_root . '/config.inc.php';

$image_url = '';

$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

if (mysqli_connect_error() == 0) {
	if ($_REQUEST['type'] == 'hrb') {
		if ($stmt=$dbLink->prepare('SELECT DISTINCT image_large,'
			. ' sciname.family_name, sciname.common_names, sciname.usda_zone_lo,'
			. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
			. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
			. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark'
			. ' FROM hrb_accession, sciname'
			. ' WHERE herb_nbr = ?'
			. ' AND hrb_accession.sciname_id = sciname.scientific_name_id')) {
			$stmt->bind_param('s', $_REQUEST['id']);
			
			$stmt->execute();
			
			$stmt->bind_result($image_url,
				$sciname['family_name'], $sciname['common_names'], $sciname['usda_zone_lo'],
				$sciname['usda_zone_hi'], $sciname['genus_designator'], $sciname['genus_name'], $sciname['species_designator'],
				$sciname['species_name'], $sciname['subspecies_name'], $sciname['variety_name'], $sciname['forma_name'], $sciname['cultivar_name'],
				$sciname['author_name'], $sciname['plant_patent_no'], $sciname['family_common_name'], $sciname['trademark']);
			
			if (!$stmt->fetch()) {
				$image_url = '';
			}
			
			$stmt->close();
		}
	} elseif ($_REQUEST['type'] == 'lc') {
		if ($stmt=$dbLink->prepare('SELECT DISTINCT lc_images.image_large,'
			. ' sciname.family_name, sciname.common_names, sciname.usda_zone_lo,'
			. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
			. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
			. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark'
			. ' FROM lc_accession, lc_images, sciname'
			. ' WHERE lc_accession.accession_no = ?'
			. ' AND lc_images.image_thumb LIKE ?'
			. ' AND lc_images.sciname_id = SUBSTR(lc_accession.sciname_id, 1, 14)'
			. ' AND lc_accession.sciname_id = sciname.scientific_name_id')) {
			$thumbName = '%/' . $_REQUEST['name'];
			$stmt->bind_param('ss', $_REQUEST['id'], $thumbName);
			
			$stmt->execute();
			
			$stmt->bind_result($image_path,
				$sciname['family_name'], $sciname['common_names'], $sciname['usda_zone_lo'],
				$sciname['usda_zone_hi'], $sciname['genus_designator'], $sciname['genus_name'], $sciname['species_designator'],
				$sciname['species_name'], $sciname['subspecies_name'], $sciname['variety_name'], $sciname['forma_name'], $sciname['cultivar_name'],
				$sciname['author_name'], $sciname['plant_patent_no'], $sciname['family_common_name'], $sciname['trademark']);
			
			if ($stmt->fetch()) {
				$image_url = getRootUrl() . $image_path;
			}
			
			$stmt->close();
		}
	} else {
		if ($stmt=$dbLink->prepare('SELECT DISTINCT lc_images.image_large,'
			. ' sciname.family_name, sciname.common_names, sciname.usda_zone_lo,'
			. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
			. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
			. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark'
			. ' FROM lc_images, sciname'
			. ' WHERE sciname.scientific_name_id = ?'
			. ' AND lc_images.image_thumb LIKE ?'
			. ' AND lc_images.sciname_id = SUBSTR(sciname.scientific_name_id, 1, 14)')) {
			$thumbName = '%/' . $_REQUEST['name'];
			$stmt->bind_param('ss', $_REQUEST['sci'], $thumbName);
			
			$stmt->execute();
			
			$stmt->bind_result($image_path,
				$sciname['family_name'], $sciname['common_names'], $sciname['usda_zone_lo'],
				$sciname['usda_zone_hi'], $sciname['genus_designator'], $sciname['genus_name'], $sciname['species_designator'],
				$sciname['species_name'], $sciname['subspecies_name'], $sciname['variety_name'], $sciname['forma_name'], $sciname['cultivar_name'],
				$sciname['author_name'], $sciname['plant_patent_no'], $sciname['family_common_name'], $sciname['trademark']);
			
			if ($stmt->fetch()) {
				$image_url = getRootUrl() . $image_path;
			}
			
			$stmt->close();
		}
	}
	$dbLink->close();
}

if (isNull($image_url) || ($image_url == '')) {
	header('Location: ' . getRootUrl() . '/');
}

require_once 'modules/page_tab_Intro_common.php';
require_once 'modules/collections_common.php';

function make_caption_from_filename($filename) {
	preg_match('/\/[\d-_]+(.*)\.jpg/i', $filename, $t);
	$f = preg_split('/_/', $t[1]);
	$c = array(
		'name'		=> preg_replace('/-/', ' ', $f[0]),
		'attrs'		=> preg_replace('/-/', ', ', $f[1]),
		'photographer'	=> $f[2],
		'date'		=> preg_replace('/no-date/', 'Unknown Date', $f[3])
	);
	
	return "<b><i>" . $c['name'] . "</i>, " . $c['attrs'] . ". Photographed by " . $c['photographer'] . ", " . $c['date'] . "</i></b>";
}

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
require_once $app_root . '/modules/header_common.php';
?>

<div id="body" class="body">

<div id="content" class="content">
<?php
showTabs($pages_info, null, 'image_detail');
require_once $app_root . '/modules/sciname_common.php';
?>
<b><?php sciname_long($resultData, $sciname); ?></b>
<hr />
<center>
<img src="<?php echo $image_url; ?>" />
</center>
</div>

<div id="caption" class="caption">
<b><?php echo make_caption_from_filename($image_url); ?></b>
</div>

</div>
<div class="image_footer">
<?php 
require_once $app_root . '/modules/footer_common.php';
?>
</div>
</body>
</html>
