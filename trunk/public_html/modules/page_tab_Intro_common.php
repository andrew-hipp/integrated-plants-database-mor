<?php
$pages_dir = 'pages';

$file_names = array();
// Open the pages directory and find what files are in there.  Any HTML files
// will be presented as a tab-like object and we'll grab the 'current' file
// to use as the main page content.  If there is no 'current' file, then the
// first HTML file found will be shown as the content.
if (is_dir($pages_dir)) {
    if ($dh = opendir($pages_dir)) {
        while (($file = readdir($dh)) !== false) {
        	if (!is_dir($pages_dir . '/' . $file)
        		&& ($file != 'advanced_search_text.html')) {
				$file_names[] = $file;
			}
		}
	}
}
				
$pages_info = array();

// Given the file names from the pages_dir, sort the names in case-insensitive
// alphabetical order, then look for any HTML files.  Any HTML files
// will be presented as a tab-like object and we'll grab the 'current' file
// to use as the main page content.  If there is no 'current' file, then the
// first HTML file found will be shown as the content.
sort($file_names);
foreach ($file_names as $file) {
	if ((substr_compare($file, '.', 0, 1) != 0)
		&& (substr_compare($file, '.html', strlen($file) - 5, 5, true) == 0)
		&& is_readable($pages_dir . '/' . $file)) {
		$fileContents = file_get_contents($pages_dir . '/' . $file);
		$bodyStartPos = stripos($fileContents, '<body>');
		$bodyEndPos = stripos($fileContents, '</body>');
		$titleStartPos = stripos($fileContents, '<title>');
		if (($titleStartPos !== false)
			&& ($bodyStartPos !== false)
			&& ($bodyEndPos !== false)) {
			$titleEndPos = stripos($fileContents, '</title>', $titleStartPos);
			if ($titleEndPos !== false) {
				$title = substr($fileContents,
								$titleStartPos + 7,
								$titleEndPos - ($titleStartPos + 7));
				$pages_info[] = array('name'=>$file, 'title'=>$title);
			}
		}
	}
}

// Verify that we're still showing a good page.
if (isset($_SESSION['curPage'])) {
	$curPageOK = false;
	foreach ($pages_info as $page_info) {
		if ($page_info['name'] == $_SESSION['curPage']) {
			$curPageOK = true;
			$curPage = $pageInfo;
			break;
		}
	}
	if (!$curPageOK) {
		// Nope, reset $curPage!
		unset($_SESSION['curPage']);
	}
}

if (!isset($_SESSION['curPage'])) {
	$curPage = array('name'=>'', 'title'=>'N/A');
	if (count($pages_info) > 0) {
		$curPage = $pages_info[0];
	}
}

// Support going directly to the page if called as "index.php?pagetitle"
$requestKeys = array_map('strtolower', array_keys($_REQUEST));
foreach ($pages_info as $page_info) {
	if (in_array(str_replace(" ", "_", strtolower($page_info['title'])), $requestKeys)) {
		$curPage = $page_info;
		break;
	}
}

if (isset($_POST['page'])) {
	$pageName = $_POST['page'];
	foreach ($pages_info as $page_info) {
		if ($page_info['name'] == $pageName) {
			$curPage = $page_info;
			break;
		}
	}
}

function doTab($tabName, $tabOperation, $isCurrent) {
	if ($isCurrent) {
		echo '					<div class="currentTab">' . $tabName . '</div>' . "\n";
	} else {
		if (($tabOperation != null) && isset($tabOperation['pagename'])) {
			echo '					<div class="otherTab" onMouseover="javascript:window.status=\'Go to ' . $tabName . '\'; return true;" onMouseout="javascript:window.status=\'\'; return true;" onClick="javascript:gotoPage(document.gotoPageForm, \'' . $tabOperation['pagename'] . '\');">' . $tabName . '</div>' . "\n";
		} else if (($tabOperation != null) && isset($tabOperation['url'])) {
			echo '					<div class="otherTab" onMouseover="javascript:window.status=\'Go to ' . $tabName . '\'; return true;" onMouseout="javascript:window.status=\'\'; return true;" onClick="javascript:location.href=\'' . $tabOperation['url'] . '\';">' . $tabName . '</div>' . "\n";
		} else {
			echo '					<div class="otherTab">' . $tabName . '</div>' . "\n";
		}
	}
}

function showTabs($pages_info, $curPage, $curPageTitle='') {
?>			<div class="full_width">
				<div class="leftSideTab">
					<div class="preTab">&nbsp;</div>
<?php
	foreach ($pages_info as $page_info) {
		doTab($page_info['title'], array('pagename'=>$page_info['name']),
				(($curPageTitle != '') && ($page_info['name'] == $curPageTitle))
				|| (($curPage != null) && ($page_info == $curPage)));
	}
	doTab('Advanced Search', array('url'=>getRootUrl() . '/advanced_search.php'), $curPageTitle == 'advanced_search');
	if (($curPage == null) && ($curPageTitle == 'results')) {
		doTab('Results', null, true);
	} elseif (($curPage == null) && ($curPageTitle == 'image_detail')) {
		doTab('Image', null, true);
	} elseif (($curPage == null) && ($curPageTitle == 'image_browser')) {
		doTab('Images', null, true);
	}
?>				</div>
				<div class="underTabs">&nbsp;</div>
			</div>
<?php
}
?>
