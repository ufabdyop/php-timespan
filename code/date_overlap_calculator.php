<?php
/**
 *
 * A utility class:
 * create an instance of date_overlap_calculator with initial bdates and edates,
 * then call find_uncovered_slots with an array of time_spans to get an array of 
 * time_spans that represent the time left uncovered.
*/
require_once('time_span.php');
class date_overlap_calculator {
	private $timespan;

	/**
	* @param start optionally include start and stop to set the member variable timespan
	* @param stop optionally include start and stop to set the member variable timespan
	*/
	function __construct($start = null, $stop = null) {
		if ($start == null || $stop == null) {
			$this->timespan = null;
		}
		$this->timespan = new time_span($start, $stop);	
	}

	/*
	This function merges the given array of time_spans into the smallest number of time_spans with no overlaps
	*/
	public function merge_slots($slot_ary) {
		$return_array = array();

		//order the timespans by beginning
		usort($slot_ary, array("date_overlap_calculator", "time_span_cmp"));

		//loop over timespans, if they overlap, create new time_span with bdate = min(bdate1, bdate2), edate = max(bdate1, bdate2)
		$current_slot = new time_span($slot_ary[0]->bdate(), $slot_ary[0]->edate());
		foreach($slot_ary as $slot) {
			if($this->slots_overlap($slot, $current_slot)) {
				$current_slot->bdate = min($slot->bdate, $current_slot->bdate);
				$current_slot->edate = max($slot->edate, $current_slot->edate);
			} else if ($current_slot->edate == $slot->bdate) {
				echo "FOO";
			} else {
				$return_array[] = $current_slot;
				$current_slot = $slot;
			}
		}
		if ($slot_ary) {
			$return_array[] = $current_slot;
		}
		return $return_array;
	}

	/**
	* checks if 2 passed timeslots overlap
	* @return true if they overlap, otherwise false
	*
	*/
	public function slots_overlap($slot1, $slot2) {

		if (
			(($slot1->bdate >= $slot2->bdate) && ($slot1->bdate <= $slot2->edate)) ||
			(($slot2->bdate >= $slot1->bdate) && ($slot2->bdate <= $slot1->edate)) 
			) {
			return true;
		}
		return false;
	}

	/**
	* A typical comparison function. Whichever timespan begins first is considered
	* less than the other.
	*
	* @return 0 if equal, -1 if $a starts first, +1 if $a starts last
	*
	*/
	static function time_span_cmp($a, $b) {
		return $a->cmp($b);
	}

	/*
	*/
	public function find_uncovered_slots($slot_ary) {
            
            //first pass, remove slots that don't overlap with this timespan
            for ($i = count($slot_ary) - 1; $i >= 0; $i-- ) {
                $slot = $slot_ary[$i];
                if ($slot->edate <= $this->timespan->bdate) {
                    unset($slot_ary[$i]);
                } else if ($slot->bdate >= $this->timespan->edate) {
                    unset($slot_ary[$i]);
                }
            }
          
		$empty_activity = false;
		$slice_points = array(); //array of timestamps for where to slice up the activity
		$current_slice = new time_span();
		$current_slice = array('bdate' => $this->timespan->bdate, 'edate' => $this->timespan->edate); 
		//echo "Activity: " . $this->timespan->bdate() . " to " . $this->timespan->edate() . "\n";
		foreach($slot_ary as $tspan ) {
			$add_current_slice = false;

		   //4 possibilities: 
			//1) reservation slot covers entirety of the activity slot 
			if ($tspan->bdate <= $current_slice['bdate'] && $tspan->edate >= $current_slice['edate']) {
				$empty_activity = true;
			}

			//2) activity slot covers entirety of reservation slot (split the activity slot)
			else if ($tspan->bdate > $current_slice['bdate'] && $tspan->edate < $current_slice['edate'] ) {
				$slice_points[] = array('bdate' => $current_slice['bdate'],
							'edate' => $tspan->bdate);
				$current_slice = array('bdate' => $tspan->edate,
							'edate' => $current_slice['edate']);
				$add_current_slice = true;
			}

			//3) reservation slot covers beginning of activity slot to some midpoint
			else if ($tspan->bdate <= $current_slice['bdate'] ) {
				$current_slice = array('bdate' =>  $tspan->edate,
							'edate' => $current_slice['edate']);
			}

			//4) reservation slot covers from midpoint of activity slot to end
			else if ($tspan->edate >= $current_slice['edate'] ) {
				$current_slice = array('bdate' =>  $current_slice['bdate'],
							'edate' => $tspan->bdate);
				$slice_points[] = $current_slice;
			}
		}
		if ($empty_activity) {
			return null;
		} 
		if (sizeof($slice_points) == 0) {
			$slice_points[] = $current_slice;
		}	
		if (isset($add_current_slice) && $add_current_slice) {
			$slice_points[] = $current_slice;
		}	
		return $slice_points;
	}

/**
Each hyphen is 5 minutes
12am        1am         2am         3am         4am         5am         6am         7am
|           |           |           |           |           |           |           | 
-------------------------------------------------------------------------------------
                    ----------------------                                          |this->timespan   
    ---------     ------     -----------     -----                                  |slot_ary
                        -----           --                                          |return value

In essence, the return value is an array of time_spans equal to this->timespan minus slot_ary
*/
	public function find_uncovered_spans($slot_ary) {
		$spans = array();
		$slots = $this->find_uncovered_slots($slot_ary) ;
                if ($slots) {
                    foreach($slots as $s) {
                            $ts = new time_span();
                            $ts->bdate = $s['bdate'];
                            $ts->edate = $s['edate'];
                            $spans[] = $ts;
                    }
                }
		return $spans;
	}
/**
Each hyphen is 5 minutes
12am        1am         2am         3am         4am         5am         6am         7am
|           |           |           |           |           |           |           | 
-------------------------------------------------------------------------------------
----------          ----------------------           -----------                    |time_group_one   
    ---------     ------     -----------     -----                                  |time_group_two   
----                    -----           --           -----------                    |results           

In essence, results are equal to time_group_one minus time_group_two
*/
	public function time_span_group_subtract($time_group_one, $time_group_two) {
		$merged_one = $this->merge_slots($time_group_one);
		$merged_two = $this->merge_slots($time_group_two);
		$results = array();
		foreach($merged_one as $slot ) {
			$temp_calc = new date_overlap_calculator($slot->bdate(), $slot->edate());
			$results = array_merge( $temp_calc->find_uncovered_spans($time_group_two), $results);	
		}
		return $this->merge_slots($results);
	}

/**
* Compares 2 arrays of timespans.  If they are exactly the same, returns true.
*
*/
	public function time_span_group_equal($time_group_one, $time_group_two) {
		if (count($time_group_one) != count($time_group_two)) {
			return false;
		}
		for ($i = 0; $i < count($time_group_one); $i++) {
			if (!$time_group_one[$i]->equals($time_group_two[$i])) {
				return false;
			}
		}
		return true;
	}

	public function dump_slices($ary) {
		echo "Slice Points: ";
		foreach($ary as $span) {
			$ts = new time_span($span['bdate'] , $span['edate']) ;
			$ts->bdate = $span['bdate'] ;
			$ts->edate = $span['edate'] ;
			echo '(' . $ts->bdate() . ', ' . $ts->edate() . '), ';
		}
		echo "\n";
	}

        public function dump_spans($spans) {
            foreach ($spans as $ts) {
                echo '(' . $ts->bdate() . ', ' . $ts->edate() . ')' . "\n";
            }
        }

        public function set_timespan($ts) {
            $this->timespan = $ts;
        }

}
?>
