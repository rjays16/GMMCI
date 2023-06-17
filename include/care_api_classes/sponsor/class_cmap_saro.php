<?php

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCmapSaro extends Core {
	
	private $saroTable = 'seg_cmap_saro';
	private $loggerName = 'sponsor.cmap';
	private $id;
	
	function SegCmapSaro($id=null) {
		global $db;
		$this->setTable($this->saroTable, $fetch_metadata=true);
		$this->setupLogger($this->loggerName);
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
	
	
	function save($data) {
		global $db;

		// trim data array
		$data = array_intersect_key($data, array_flip($this->getRefArray()));
		
		$result = $db->Replace(
			$this->saroTable,
			$data,
			'id',
			$autoquote=true
		);
		if ($result === 0 || $result==false) {
			@$this->logger->error( 'SQL error: '.$db->ErrorMsg() );
		}
		return ($result !== 0);
	}

}
