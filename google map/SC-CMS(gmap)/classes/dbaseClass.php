<?php

include_once '../../conf-sc.php';

class Dbase
{
/*  This class contains the method for connecting to the database and all the other mehtods containing database queries  */
	private $db;		// holds database connection
	
	public function __construct()
	{
		try {
			$this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
			if (mysqli_connect_errno()) {
				throw new exception("Unable to connect to database");
			}
			/* else {
				return $this->db;
			} */
		}
		catch(exception $e) {
			echo $e->getMessage();
		}
	}
	
	public function getPage($pageID)
	{
		$qry = "SELECT PageID, PageTitle, PageHeading, PageKeywords, PageDescription, PageContent FROM pages WHERE PageID = '$pageID'"; 
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($rs->num_rows > 0) {
				$page = $rs->fetch_assoc();
				return $page;
			}
			else {
				die('Page not found');
			}
		}
		else {
			die ('Error executing query: '.$qry);
		}
	}
	
	public function updatePage($post)
	{
		extract($post);
		if (!get_magic_quotes_gpc()) {
			$PageTitle = $this->db->real_escape_string($PageTitle);
			$PageHeading = $this->db->real_escape_string($PageHeading);
			$PageKeywords = $this->db->real_escape_string($PageKeywords);
			$PageDescription = $this->db->real_escape_string($PageDescription);
			$PageContent = $this->db->real_escape_string($PageContent);
		}
		$qry = "UPDATE pages SET PageTitle = '$PageTitle', PageHeading = '$PageHeading', PageKeywords = '$PageKeywords', PageDescription = '$PageDescription', PageContent = '$PageContent' WHERE PageID = '$PageID'";
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($this->db->affected_rows > 0) {
				$msg = 'Page successfully updated';
			}
			else {
				$msg = 'Page not found';
			}
		}
		else {
			$msg = 'Unable to update page '. mysqli_error($this->db);
		}
		return $msg;
	}
	
	public function getAdmin($userName)
	{
		$qry = "SELECT UserName, UserPass FROM users WHERE UserName = '$userName'";
		$rso = $this->db->query($qry);
		if ($rso) {
			return $rso;
		}
		else {
			echo 'Error executing query: '.$qry;
		}
	}
	
	public function getProducts($PID="")
	{
	//  This method is used get record(s) from cartproducts table
		if ($PID) {
			$qry = "SELECT PID, PTitle, PName, PDesc, PImage, PPrice FROM cartproducts WHERE PID = $PID";
			$rs = $this->db->query($qry);
			if ($rs) {
				if ($rs->num_rows > 0) {
					$product = $rs->fetch_assoc();
					return $product;
				}
				else {
					$msg = 'Product not found on table';
					return $msg;
				}
			}
			else {
				echo "Error executing query: ".$qry;
			}
		}
		else {
			$qry = "SELECT PID, PTitle, PName, PDesc, PImage, PPrice FROM cartproducts";
			$rs = $this->db->query($qry);
			if ($rs) {
				if ($rs->num_rows > 0) {
					$products = array();
					while ($product = $rs->fetch_assoc()) {
						$products[] = $product;
					}
					return $products;
				}
				else {
					$msg = 'The Product table is empty';
					return $msg;
				}
			}
			else {
				echo "Error executing query: ".$qry;
			}
		}
	}
	
	public function insertProduct($product)
	{
		if ($product) {
			extract($product);
		}	
		if (!get_magic_quotes_gpc()) {
			$PName = $this->db->real_escape_string($PName);
			$PTitle = $this->db->real_escape_string($PTitle);
			$PDesc = $this->db->real_escape_string($PDesc);
		}
		$qry = "INSERT INTO cartproducts VALUES ('', '$PTitle', '$PName', '$PDesc', '', $PPrice)";
		$rs = $this->db->query($qry);
		if ($rs) {
			$result['msg'] = 'Product record created.';
			$result['PID'] = $this->db->insert_id;
		}
		else {
			echo 'Error inserting product: '.$qry;
			return false;
		}
		return $result;
	}
	
	public function updateProductImagePath($PID, $PImage)
	{
		$qry = "UPDATE cartproducts SET PImage = '$PImage' WHERE PID = $PID";
		$rs = $this->db->query($qry);
		if ($rs) {
			return true;
		}
		else {
			echo 'Error updating product image path: '.$qry;
			return false;
		}
	}
	
	public function updateProduct($product)
	{
		extract($product);
		if (!get_magic_quotes_gpc()) {
			$PName = $this->db->real_escape_string($PName);
			$PTitle = $this->db->real_escape_string($PTitle);
			$PDesc = $this->db->real_escape_string($PDesc);
		}
		$qry = "UPDATE cartproducts SET PName='$PName', PTitle='$PTitle', PDesc='$PDesc', PPrice=$PPrice, PImage='$PImage' WHERE PID=$PID";
		$rs = $this->db->query($qry);
		if ($rs) {
			$msg = "Product updated successfully";
			return $msg;
		}
		else {
			echo "Error executing query ".$qry;
			return false;
		}		
	}
	
	public function deleteProduct($PID)
	{
		$qry = "DELETE FROM cartproducts WHERE PID = $PID";
		$rs = $this->db->query($qry);
		if ($rs) {
			$result['ok'] = true;
			$result['msg'] = 'Product successfully deleted!';
		}
		else {
			$result['ok'] = false;
			$result['msg'] = 'Error deleting product: '.$qry;
		}
		return $result;
	}
	
	public function createOrder($result, $products, $totalSale)
	{
		extract($_POST);
		$receiptNumber = $result[1];
		$qry = "INSERT INTO orders VALUES('', '$name', '$email', '$phone', 
		'$hno_street', '$suburb', '$city', '$country', '$ship_hno_street', 
		'$ship_suburb', '$ship_city', '$ship_country', '$receiptNumber', $totalSale, '". date('Y-m-d H:i:s')."', 0, '')";
		$rs = $this->db->query($qry);
		if ($rs) {
			$orderID = $this->db->insert_id;
			foreach ($products as $product) {
				$qry1 = "INSERT INTO orderedproducts VALUES ('', $orderID, ".
						$product['PID'].", ".$product['PPrice'].", ".
						$product['Qty'].")";
				$rs1 = $this->db->query($qry1);
				if (!$rs1) {
					echo "Error creating orderedproducts record! ".$qry1;
				}
			}	
		}
		else {
			echo "Error creating orders record! ".$qry;
		}
	}
}	
?>