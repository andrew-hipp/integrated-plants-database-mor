<?php
$localizationTable = array();

{
	$localizationContent = explode("\n", file_get_contents($app_root . '/pages/Strings.txt'));
	
	foreach ($localizationContent as $localizationLine) {
		$localizationLine = trim($localizationLine);
		
		if (($localizationLine == '') || (substr($localizationLine, 0, 1) == '#')) {
			continue;
		}
		
		$localizationParts = explode('=', $localizationLine);
		$localizationKey = trim(trim($localizationParts[0]), '"');
		array_shift($localizationParts);
		$localizationString = trim(trim(implode('=', $localizationParts)), '"');
		$localizationTable[$localizationKey] = $localizationString;
	}
}

function localize($s) {
	global $localizationTable;
	$retVal = $s;
	
	if (isset($localizationTable[$s])) {
		$retVal = $localizationTable[$s];
	}
	
	return $retVal;
}
?>
	<div id="heading" class="heading">
		<div id="headingLeft" class="headingLeft">
			<div id="tree" class="locationTree">You are here: <?php echo (isset($pageLocation) ? $pageLocation : $curPage['title']); ?></div>
			<table>
				<tr valign="middle">
					<td><img height="58px" width="85px" src="<?php echo getRootUrl(); ?>/images/mortonlogo.png" /></td>
					<td>&nbsp;</td>
					<td>
						<div style="font-size: 22px; font-weight: bold; font-style: italic;"><?php echo localize('searchMOR'); ?></div>
						<div style="font-size: 14px;"><?php echo localize('The plants database of The Morton Arboretum'); ?></div>
					</td>
				</tr>
			</table>
		</div>
<?php
if (!isset($noSearch) || !$noSearch) {
?>		<div id="headingRight" class="headingRight">
			<form name="search" action="<?php echo getRootUrl(); ?>/search.php" method="get">
				<fieldset style="padding: 2px; padding-left: 8px; padding-right: 8px;">
					<div style="height: 15px;">
						<div class="floatLeft"><b><em>QUICK SEARCH</em></b>&nbsp;</div><div class="floatLeft" onMouseover="javascript:window.status='<?php echo localize('Show Quick Search help'); ?>'; return true;" onMouseout="javascript:window.status=''; return true;" onClick="javascript:alert('search help');"><img src="images/quickHelpImage.png"></img></div>
						<div class="floatRight"><div class="floatLeft" onMouseover="javascript:window.status='<?php echo localize('Start Quick Search'); ?>'; return true;" onMouseout="javascript:window.status=''; return true;" onClick="javascript:document.search.submit();"><img src="images/searchGo.png"></img></div></div>
					</div>
					<div style="height: 3px; padding-top: 1px; font-size: 1px;"><img src="images/black1x1.png" height="1px" width="100%"></img></div>
					<table border="0px" width="100%">
						<tr>
							<td align="right"><label for="plantName" class="searchLabel">Plant name</label></td>
							<td align="left"><input type="text" name="plantName" id="plantName" style="font-family: Arial; font-size: 10px; width: 172px;" onKeyPress="return submitOnEnter(this, event);"></input></td>
						</tr>
						<tr>
							<td align="right"><label for="collectionCombo" class="searchLabel">Living Collection</label></td>
							<td align="left">
								<select name="collectionCombo" id="collectionCombo" style="font-family: Arial; font-size: 10px; width: 180px;" onKeyPress="return submitOnEnter(this, event);">
<?php
buildCollectionsOptionsList("\t\t\t\t\t\t\t\t\t");
?>								</select>
							</td>
						</tr>
						<tr>
							<td align="right"><label for="collectionSite" class="searchLabel">Collection site</label></td>
							<td align="left"><input type="text" name="collectionSite" id="collectionSite" style="font-family: Arial; font-size: 10px; width: 172px;" onKeyPress="return submitOnEnter(this, event);"></input></td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
<?php
}
?>	</div>
