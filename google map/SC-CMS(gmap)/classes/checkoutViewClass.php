<?php
include "classes/selectClass.php";

class CheckoutView extends View
{
	private $model;
	private $result;
	
	public function __construct($rs, $model)
	{
		$this->rs = $rs;
		$this->model = $model;
	}
	
	protected function displayContent()
	{
		$html = '<h2 class="pagehead">'.$this->rs['PageHeading'].'</h2>'."\n";
		$cart = new SCart;
		if ($_SESSION['cart']) {
			if ($_POST['process']) {
				//  validate entries and if valid process order
				$this->result = $this->model->validateCheckoutEntries();
				if ($this->result['ok']) {
					$status = $cart->doProcessOrder($this->model);
					$html .= '<p class="pageMsg">'.$status.'</p>'."\n";
				}
				else {
					$html .= $this->showCheckoutForm();
				}
			}
			else {
				$html .= $this->showCheckoutForm();
			}
		}
		else {
			$html .= '<p class="pageMsg">Cart is empty!</p>'."\n";
		}
		return $html;
	}

	private function showCheckoutForm() 
	{  
		if (is_array($this->result)) {
			extract($this->result);
		}
		if ($_POST['process']) {
			extract($_POST);
		} 
		$select = new Select;
		$countries = $select->fillCountryOptions();
		$html = '<form name="checkout" method="post" action="'.$_SERVER['REQUEST_URI'].'">'."\n";
		$html.= '<label for="name" class="col1">Name</label>';
		$html.= '<input type = "text" name="name" id = "name"  class="col2" value="'.$name.'" />'."\n";
		$html.= '<div class="col3">'.$nameMsg.'</div>'."\n";
		$html.= '<label for="email"  class="col1">Email </label>'."\n";
		$html.= '<input type = "text" name="email" id="email" class="col2" value="'.$email.'"/>'."\n";
		$html.= '<div class="col3"> '.$emailMsg.'</div>'."\n";
		$html.= '<label for="phone"  class="col1">Phone </label>'."\n";
		$html.= '<input type="text" name="phone" id="phone" class="col2" value="'.$phone.'"/>'."\n";
		$html.= '<div class="col3"> '.$phoneMsg.'</div>'."\n";
		$html.= '<label for="hno_street"  class="col1">H.No, Street</label>'."\n";
		$html.= '<textarea name="hno_street" id="hno_street" rows="2" cols="20" class="col2a">'.$hno_street.'</textarea>'."\n";
		$html.= '<div class="col3"> '.$streetMsg.'</div>'."\n";
		$html.= '<label for="suburb"  class="col1">Suburb</label>'."\n";
		$html.= '<input type="text" name="suburb" id="suburb"  class="col2" value="'.$suburb.'"/>'."\n";
		$html.= '<div class="col3"> '.$suburbMsg.'</div>'."\n";
		$html.= '<label for="city"  class="col1">City / Town</label>'."\n";
		$html.= '<input type="text" name="city" id="city" class="col2" value="'.$city.'"/>'."\n";
		$html.= '<div class="col3"> '.$cityMsg.'</div>'."\n";
		$html.= '<label for="country" class="col1">Country</label>'."\n";
//			$html .=  $this->createSelectCountryList("country","New Zealand");
		$country = ($country == "") ? "New Zealand" : $country;
		$html .=  $select->createSelectTag("country", $countries, $country, "col2");
		$html .= '<p class="col1a">Shipping Address: [If different from above]</p>'."\n";
		$html.= '<label for="ship_hno_street"  class="col1">H.No, Street</label>'."\n";
		$html.= '<textarea name="ship_hno_street"  id="ship_hno_street" rows="2" cols="20" class="col2a">'.$ship_hno_street.'</textarea>'."\n";
		$html.= '<div class="col3"> '.$streetMsg.'</div>'."\n";
		$html.= '<label for="ship_suburb"  class="col1" >Suburb</label>'."\n";
		$html.= '<input type="text" name="ship_suburb" id="ship_suburb" class="col2" value="'.$ship_suburb.'" />'."\n";
		$html.= '<div class="col3"> '.$suburbMsg.'</div>'."\n";
		$html.= '<div class="clear"></div>'."\n";
		$html.= '<label for="ship_city"  class="col1">City / Town</label>'."\n";
		$html.= '<input type="text" name="ship_city" id="ship_city" class="col2" value="'.$ship_city.'"/>'."\n";
		$html.= '<div class="col3"> '.$cityMsg.'</div>'."\n";
		$html.= '<label for="country" class="col1">Ship Country</label>'."\n";
		$ship_country = ($ship_country == "") ? "New Zealand" : $ship_country;
		$html .=  $select->createSelectTag("ship_country",$countries, $ship_country, "col2");
		$html.= '<input type = "submit" name="process" value="Submit" class="submitButton" />'."\n";
		return $html;
	}	

}

?>