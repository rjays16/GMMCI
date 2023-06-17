<?php

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCmapNca extends Core {
	
	private $_ncaTable = 'seg_cmap_nca';
	private $_loggerName = 'sponsor.cmap';
	private $id;
	
	function SegCmapNca($id=null) {
		global $db;
		$this->setTable($this->_ncaTable, $fetch_metadata=true);
		$this->setupLogger($this->_loggerName);
		if ($id) {
			$this->id = $id;
		}
	}
	
	
	/**
	* put your comment there...
	* 
	* @param string $id
	*/
	function fetch($id=null) {
		if (!$id) {
			$id=$this->id;
		}
		return parent::fetch(array('id'=>$id));
	}

	/**
	* put your comment there...
	* 
	* @param array $data
	*/
	function save($data) {
		global $db;
		
		// trim data array
		$data = array_intersect_key($data, array_flip($this->getRefArray()));
		
		$result = $db->Replace(
			$this->_ncaTable,
			$data,
			'id',
			$autoquote=true
		);
		$saveok = ($result !== 0 && $result!==false);
		if (!$saveok) {
			@$this->logger->error( 'SQL error: '.$db->ErrorMsg() );
		}
		return $saveok;
	}

	
	
}
