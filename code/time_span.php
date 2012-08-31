<?php
/**
*
* Simple storage class, almost a struct, with a bdate and edate
* 
* Brief example of use:
* <code>
* $my_span = new time_span('2012-05-06 12:30:00', '2012-05-06 14:30:00')
*
* print $my_span->bdate;
* //prints 1336329000
*
* print $my_span->bdate();
* //prints 2012-05-06 12:30:00
*
* </code>
*
*/
class time_span {
	public $bdate;
	public $edate;
	function __construct($b = '', $e = '') {
		$this->bdate = strtotime($b);
		$this->edate = strtotime($e);
		if ($this->edate < $this->bdate) {
			throw new Exception("bdate must be before edate");
		}
	} 
	public function bdate() {
		return date('Y-m-d H:i:s', $this->bdate);
	}
	public function edate() {
		return date('Y-m-d H:i:s', $this->edate);
	}

	public function to_string() {
		return $this->as_string();
	}
	public function as_string() {
		return "( " .$this->bdate() . ', ' . $this->edate() . ') ';
	}

	/**
	* A typical comparison function. Whichever timespan begins first is considered
	* less than the other.
	*
	* @return 0 if equal, -1 if this starts first, +1 if this starts last, if they
				start at the same time, the one that ends first is considered less
	*
	*/
	public function cmp($compareTo) {
		if (($this->bdate == $compareTo->bdate)
		   && ($this->edate == $compareTo->edate))  {
			return 0;
		}
		if ($this->bdate == $compareTo->bdate) {
			return ($this->edate > $compareTo->edate) ? +1 : -1; 
		}
		return ($this->bdate > $compareTo->bdate) ? +1 : -1; 
	}

	/**
	* returns true if argument is equal to this timespan
	*/
	public function equals($compareTo) {
		return $this->cmp($compareTo) == 0;
	}
}
?>
