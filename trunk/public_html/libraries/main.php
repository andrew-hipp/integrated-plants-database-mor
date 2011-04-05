<?php header("Content-type: text/css");
$bgColor = '#568a1d';
$hdrHeight = 104;
$bgColorMain = '#FFFFCC';
if (strpos($_SERVER['HTTP_USER_AGENT'], 'KHTML, like Gecko') !== false &&
	strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') == false) {
 	$hdrHeight = 92;
}
$fontFamily = 'Verdana, san-serif, Arial';
$headerFontSize = '12px';
$regFontSize = '14px';
$bigFontSize = '15px';
$tabWidth = '130px';
?>
html, body {
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $regFontSize; ?>;
	color: #000000;
	background: <?php echo $bgColorMain; ?>;
}

/* THIS IS ALL NEW, RIPPED FROM SYSTEMATICS.MORTONARB.ORG STYLE SHEET */
/* AH 2 MARCH 2011 */

div.bodycontent {
	width: 1000px;
	height: 850px;
	margin: 0 auto;
	padding: 20px 0 0 0;
}

div.colOne {
	float: left;
	width: 600px;
}

div.colTwo {
	font-size: 13px;
	float: right;
	width: 320px;
}

colTwo ul {
	margin: 0;
	padding: 10px 0 0 0;
	list-style: none;
}

colTwo li {
	margin-bottom: 20px;
}

colTwo li li {
	margin-bottom: auto;
}

colTwo li ul {
	padding-left: 10px;
	list-style: square inside;
}

/* END OF ADDED STYLE ELEMENTS */

a.redlink {
	color: red;
	font-weight: bold;
	text-decoration: none;
}
div.heading {
	background: <?php echo $bgColor; ?>;
	position: absolute;
	top: 0px;
	left: 0px;
	right: 0px;
	height: <?php echo $hdrHeight; ?>px;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
	overflow: hidden;
}
div.headingLeft {
	width: 450px;
	height: <?php echo $hdrHeight; ?>px;
	float: left;
	top: 0px;
	margin-left: 8px;
	margin-top: 4px;
}
div.headingRight {
	width: 300px;
	height: <?php echo $hdrHeight; ?>px;
	float: right;
	top: 0px;
	font-family: <?php echo $fontFamily; ?>;
	font-size: 10px;
}
div.locationTree {
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.body {
	background: <?php echo $bgColorMain; ?>;
	position: relative;
	top: <?php echo $hdrHeight; ?>px;
	left: 0px;
	right: 0px;
	bottom: 0px;
	height: auto;
	padding: 8px;
}
div.content {
	background: <?php echo $bgColorMain; ?>;
	top: 0px;
	left: 0px;
	bottom: 0px;
	right: 0px;
	width: auto;
	height: auto;
	min-width: 200px
}
div.login_body {
	background: #FFFFD0;
	position: absolute;
	top: <?php echo $hdrHeight; ?>px;
	bottom: 0px;
	left: 0px;
	right: 0px;
	min-height: 100px;
	padding: 8px;
	font-family: <?php echo $fontFamily; ?>;
}
div.footer {
	background: #ff0000;
	position: relative;
	text-align: center;
	font-family: <?php echo $fontFamily; ?>;
	margin-top: 4px;
	font-size: 1px;
	float: clear;
}
div.footer_left {
	background: <?php echo $bgColor; ?>;
	position: absolute;
	left: 0px;
	width: auto;
	padding-top: 4px;
	padding-bottom: 4px;
	padding-left: 10%;
	text-align: center;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.footer_center {
	background: <?php echo $bgColor; ?>;
	position: absolute;
	left: 0px;
	right: 0px;
	width: auto;
	text-align: center;
	padding-top: 4px;
	padding-bottom: 4px;
	text-align: center;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.footer_right {
	background: <?php echo $bgColor; ?>;
	position: absolute;
	right: 0px;
	width: auto;
	padding-top: 4px;
	padding-bottom: 4px;
	padding-right: 10%;
	text-align: center;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.login_background {
	position: absolute;
	background: <?php echo $bgColor; ?>;
	width: 448px;
	height: 218px;
	left: 50%;
	top: 33%;
	margin-left: -224px;
	margin-top: -109px;
}
div.login_form_box {
	margin: 50px 50px 50px 50px;
}
div.full_width {
	width: 100%;
}
div.floatLeft {
	float: left;
}
div.floatRight {
	float: right;
}
div.floatClear {
	float: clear;
}
div.leftSideTab {
	float: left;
}
div.rightSideTab {
	float: right;
}
div.preTab {
	width: 10px;
	padding-top: 6px;
	padding-bottom: 8px;
	border-right: 2px <?php echo $bgColor; ?> solid;
	float: left;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
	background: <?php echo $bgColorMain; ?>;
}
div.postTab {
	background: <?php echo $bgColorMain; ?>;
	width: 10px;
	padding-top: 6px;
	padding-bottom: 6px;
	float: left;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.otherTab {
	background: #E0E0E0;
	width: <?php echo $tabWidth; ?>;
	padding-top: 6px;
	padding-bottom: 6px;
	border-top: 2px <?php echo $bgColor; ?> solid;
	border-right: 2px <?php echo $bgColor; ?> solid;
	text-align: center;
	color: <?php echo $bgColor; ?>;
	font-weight: bold;
	float: left;
	cursor: pointer;
	cursor: hand;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.currentTab {
	background: <?php echo $bgColor; ?>;
	width: <?php echo $tabWidth; ?>;
	padding-top: 6px;
	padding-bottom: 6px;
	border-top: 2px <?php echo $bgColor; ?> solid;
	border-right: 2px <?php echo $bgColor; ?> solid;
	text-align: center;
	color: white;
	font-weight: bold;
	float: left;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.blankTab {
	width: 40px;
	padding-top: 6px;
	padding-bottom: 6px;
	border-right: 2px <?php echo $bgColor; ?> solid;
	float: left;
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $headerFontSize; ?>;
}
div.underTabs {
	clear: both;
	background: <?php echo $bgColor; ?>;
	width: 100%;
	padding-top: 3px;
	padding-bottom: 3px;
	font-family: <?php echo $fontFamily; ?>;
	font-size: 1px;
}
div.adv_search_text {
	clear: both;
	margin: 10px;
	padding: 10px;
/*	background: #FBFBFB; */
	background: <?php echo $bgColorMain; ?>;

}
div.search_form {
	clear: both;
	position: relative;
/*	background: #FBFBFB; */
	background: <?php echo $bgColorMain; ?>;
}
div.search_results {
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $bigFontSize; ?>;
}
div.search_taxa {
	font-family: <?php echo $fontFamily; ?>;
	font-size: <?php echo $bigFontSize; ?>;
	font-weight: bold;
}

div.image_footer {
	padding-top: 100px;
}

.em { font-family: Times; font-size: <?php echo $bigFontSize; ?>; font-weight: bold; font-style: italic;
 }
/*columns */
div.column {float:left;margin-right:13px;}
.last, div.last {margin-right:0px;}
.column {width:49%;}
.clear {clear:both;}

.result_column_left {float:left;margin-top:15px;margin-left:5px;margin-right:5px; width:77%;}
.result_column_left img {margin-right:5px;}
.result_column_right{float:left;margin-top:18px;}
