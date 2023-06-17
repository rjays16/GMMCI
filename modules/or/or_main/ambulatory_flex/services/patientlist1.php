<?php

/*
	README for sample service

	This generated sample service contains functions that illustrate typical service operations.
	Use these functions as a starting point for creating your own service implementation. Modify the function signatures, 
	references to the database, and implementation according to your needs. Delete the functions that you do not use.

	Save your changes and return to Flash Builder. In Flash Builder Data/Services View, refresh the service. 
	Then drag service operations onto user interface components in Design View. For example, drag the getAllItems() operation onto a DataGrid.
*/

class patientlist1 {

	private function connect() {
		// TODO Establish the database connection
		
		// Sample code
		
			 $connection = mysql_connect("192.168.2.237",  "omick",  "omick") or die(mysql_error());
			 mysql_select_db("hisdb_dmc", $connection) or die(mysql_error());
	    
	}

	public function getAllItems() {
		// TODO Auto-generated method stub
		// Retrieve a array of records from the database and return that

		// Sample code
		
			  $this->connect();
			  $sql = "SELECT * FROM care_person LIMIT 16";
			  $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
			  return $result;
	    	
	}

	public function getItem($itemID) {
		// TODO Auto-generated method stub
		// Return a single record from the database and return the item
		
		// Sample code
		/*
			  $this->connect();
			  $itemID = mysql_real_escape_string($itemID);
			  $sql = "SELECT * FROM TABLENAME where itemID=$itemID";

			  $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
			  return $result;
	    */ 	
	}

	public function createItem($item) {
		// TODO Auto-generated method stub
		// Insert a new record in the database using the parameter and return the item
		
		// Sample code
		/*
			  $this->connect();
			  $sql = "INSERT INTO TABLENAME (FIELD1, FIELD2, FIELD3) 
			  VALUES ('$item->FIELD1','$item->FIELD2','$item->FIELD3')";  

			  $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
			  return mysql_insert_id();
	    */ 	
	}

	public function updateItem($item) {
		// TODO Auto-generated method stub
		// Update an existing record in the database and return the item
		
		// Sample code
		/*
			  $this->connect();
			  $sql = "UPDATE TABLENAME SET FIELD1 = '$item->FIELD1', FIELD2 = '$item->FIELD2', FIELD3 = '$item->FIELD3' 
	    	  WHERE  itemID = $item->itemID";

			  $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
	    */ 	
	}

	public function deleteItem($itemID) {
		// TODO Auto-generated method stub
		// Delete a record in the database
		
		// Sample code
		/*
			  $this->connect();
			  $itemID = mysql_real_escape_string($itemID); 
			  $sql = "DELETE FROM TABLENAME WHERE itemID = $itemID";
			  $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
	    */ 	
	}

	public function count() {
		// TODO Auto-generated method stub
		// Return the number of items in your array of records
		
		// Sample code
		/*
			  $this->connect();
			  $sql = "SELECT * FROM TABLENAME";
			  $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
			  $rec_count = mysql_num_rows($result);
			  mysql_free_result($result);
			  return $rec_count;
	    */ 	
	}
	
	public function getItems_paged($startIndex, $numItems) {
		// TODO Auto-generated method stub
		// Return a page of records as an array from the database for this startIndex
		
		// Sample code
		/*
			  $this->connect();
  			  $startIndex = mysql_real_escape_string($startIndex); 
  			  $numItems = mysql_real_escape_string($numItems); 
		  	  $sql = "SELECT * FROM TABLENAME LIMIT $startIndex, $numItems";
			  $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
			  return $result;
	    */ 	
	}

}

?>