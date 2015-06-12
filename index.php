<?php 

class Payment {

	private $tickets = array(
		array('id' => '1', 'cash' => 1), 
		array('id' => '2', 'cash' => 2), 
		array('id' => '3', 'cash' => 3), 
		array('id' => '4', 'cash' => 4), 
		array('id' => '5', 'cash' => 5) 
	); 

	public function getTickets($price = 15){

		$result =   $this->findTicket($price) 
				 	?: $this->findCombinations($price) 
				 	?: $this->sumWithSplit($price) 
				 	?: null;  		   

		if( ! $result ) 
			throw new Exception("Could not find tickets with such price or combine available tickets to match the sum of " . $price, 1);
			
		return $result; 
	}

	private function sumWithSplit($price){
		$sum 	 = 0;  
		$tickets = array(); 
		
		foreach ($this->tickets as $ticket) {
			$sum += $ticket['cash']; 

			if( $sum < $price){
				$tickets[] = $ticket; 
				continue;
			}

			if($diff = $sum - $price > 0){
				echo "diff: " . $diff; 
				$ticket = $this->splitTicket($ticket, $diff); 
			}

			$tickets[] = $ticket; 
			break;  
		}

		if($sum < $price) return false; 
		return $tickets; 

	}

	private function findCombinations($price){
		
		$matches = array(); 

		foreach ($this->tickets as $x) {
			foreach ($this->tickets as $y) {
				if($x == $y) continue; 
				
				$sum = $x['cash'] + $y['cash']; 

				if($sum == $price){
					$matches[] = array($x, $y);  
				}
			}
		}

		return $matches ? $matches[array_rand($matches)] : null;    

	}

	private function splitTicket($ticket, $diff){
		$this->removeTicket($ticket['id']); 
		
		$this->tickets[] = $this->createNewTicket($diff);  

		$ticket['cash'] -= $diff;  
		$this->tickets[] = $ticket; 

		return $ticket;  

	}

	private function createNewTicket($cash, $id = null){
		$id = $id ?: uniqid(); 
		return array('id' => $id, 'cash' => $cash); 
	}

	public function removeTicket($id){
		$filtered = array(); 
		foreach ($this->tickets as $ticket) {
			if( $ticket['id'] != $id)
				$filtered[] = $ticket; 
		}

		return $this->tickets = $filtered; 
	}

	private function findTicket($ammount){

		foreach ($this->tickets as $ticket) {
			if($ticket['cash'] == $ammount){
				return $ticket; 
			}
		}

		return null; 

	}

}

$payment = new Payment(); 
echo "<pre>"; 
print_r($payment->getTickets(14)); 
echo "</pre>"; 
