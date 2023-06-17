<?php
/**
* @package SegHIS_api
*/

/******
*
*	GUI for creating multiple tabs or tab container.
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*	@date created:	May 25, 2007
*
*****/	

class GuiTabView {
	var $sTabViewWinTitle  = 'Tab View';
	var $sTabViewName      = 'tabviewid';
	var $sTabViewTitle     = 'Tab Control';
	var $sTabViewSubtitle  = '';
	var $sTabGUIRoot       = '../../';
	
	function setTabViewWinTitle($sTitle = '') {
		if ($sTitle == '') {
			$sTitle = "Tab View";
		}
		$this->sTabViewWinTitle = $sTitle;
	}
	
	function setTabViewTitle($sTitle = '') {
		if ($sTitle == '') {
			$sTitle = "Tab Control";
		}
		$this->sTabViewTitle = $sTitle;
	}	

	function setTabViewSubtitle($sTitle = '') {
		$this->sTabViewSubtitle = $sTitle;
	}	
	
	function setTabViewName($sName = '') {
		if ($sName == '') {
			$sName = 'tabviewid';
		}
		$this->sTabViewName = $sName;
	}
	
	function setTabViewRoot($sRoot = '') {
		if ($sRoot == '') {
			$sRoot = '../../';
		}
		$this->sTabGUIRoot = $sRoot;	
	}

	function getTabViewHeader() {	
		$sHdr = "<head>".
				"<title>".$this->sTabViewWinTitle."</title>".
				"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></meta>";	
											
		return ($sHdr);	
	}	
		
	function getJSSource() {
		$sSrc = "<script type=\"text/javascript\">".
				"	var djConfig = { isDebug: true };".
				"</script>".
				"<script type=\"text/javascript\" src=\"".$this->sTabGUIRoot."js/dojo/dojo.js\"></script>".
				"<script type=\"text/javascript\">".
				"	dojo.require(\"dojo.widget.TabContainer\");".
				"	dojo.require(\"dojo.widget.LinkPane\");".
				"	dojo.require(\"dojo.widget.ContentPane\");".
				"	dojo.require(\"dojo.widget.LayoutContainer\");".
				"</script>".
				"<style type=\"text/css\">".
				"body {".
				"	font-family : sans-serif;".
				"}".
				".dojoTabPaneWrapper {".
				"  	padding : 10px 10px 10px 10px;".
				"}".
				"</style>".
				"</head>";
			
		return ($sSrc);
	}

	function getConstructedTab($tabArray) {		
		$sTabBody = "<body>".
					"<h1><b>".$this->sTabViewTitle."</b></h1>".
					"<p>".$this->sTabViewSubtitle."</p>".
					"<div id=\"".$this->sTabViewName."\" dojoType=\"TabContainer\" style=\"width: 100%; height: 67.5%\" selectedTab=\"".$tabArray[$i][0]."\" >";
					
		for ($i = 0; $i < count($tabArray); $i++) {
			$sTabBody .= "<div id=\"".$tabArray[$i][0]."\" dojoType=\"ContentPane\" label=\"".$tabArray[$i][1]."\" style=\"display: none\">".
						 $tabArray[$i][2].
						 "</div>";
		}
						 
		$sTabBody .= "</div>".
					 "</body>";		
					 														
		return ($sTabBody);
	}
	
	function getTabContainer($tabArray){
		$sTabBody =	"<div id=\"".$this->sTabViewName."\" dojoType=\"TabContainer\" style=\"width: 115%; height: 15em\" selectedTab=\"".$tabArray[$i][0]."\" >";
					
		for ($i = 0; $i < count($tabArray); $i++) {
			$sTabBody .= "<div id=\"".$tabArray[$i][0]."\" dojoType=\"ContentPane\" label=\"".$tabArray[$i][1]."\" style=\"display: none\" >".
						 $tabArray[$i][2].
						 "</div>";
		}
						 
		$sTabBody .= "</div>";
		
		return ($sTabBody);
	}
}
?>