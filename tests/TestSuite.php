<?php
 require_once('code/php_unit_test_framework/php_unit_test.php');
 require_once('code/php_unit_test_framework/xhtml_test_runner.php');
 require_once('../code/time_span.php');
 require_once('../code/date_overlap_calculator.php');

 class TimeSpanFlattenTestCases extends TestCase
 {
   public function SetUp()
   {
   }
   public function Run()
   {
	$this->TestLongSpanDisruptedByShortSpan();	
	$this->TestLongSpanDisruptedBy2ShortSpans();	
	$this->TestTimeGroupSubtract();
   }
   public function TestTimeGroupSubtract() {
	$time_calc = new date_overlap_calculator();
	$visualize_this_as = "
Each hyphen is 5 minutes
12am        1am         2am         3am         4am         5am         6am         7am
|           |           |           |           |           |           |           | 
-------------------------------------------------------------------------------------
----------          ----------------------           -----------                    |time_group_one   
    ---------     ------     -----------     -----                                  |time_group_two   
----                    -----           --           -----------                    |results           

In essence, results are equal to time_group_one minus time_group_two
";
	$d = "2012-08-13";
	$time_group_one = array(
			new time_span("$d 00:00:00", "$d 00:50:00"),
			new time_span("$d 01:40:00", "$d 03:30:00"),
			new time_span("$d 04:25:00", "$d 05:15:00"),
		);
	$time_group_two = array(
			new time_span("$d 00:20:00", "$d 01:05:00"),
			new time_span("$d 01:30:00", "$d 02:00:00"),
			new time_span("$d 02:25:00", "$d 03:20:00"),
			new time_span("$d 03:45:00", "$d 04:10:00"),
		);
	$expected_results = array(
			new time_span("$d 00:00:00", "$d 00:20:00"),
			new time_span("$d 02:00:00", "$d 02:25:00"),
			new time_span("$d 03:20:00", "$d 03:30:00"),
			new time_span("$d 04:25:00", "$d 05:15:00"),
		);
	$results = $time_calc->time_span_group_subtract($time_group_one, $time_group_two);
	$are_the_same = $time_calc->time_span_group_equal($results, $expected_results);
	$this->AssertEquals($results[0]->to_string(), $expected_results[0]->to_string());
	$this->AssertEquals($results[1]->to_string(), $expected_results[1]->to_string());
	$this->AssertEquals($results[2]->to_string(), $expected_results[2]->to_string());
	$this->AssertEquals(count($results), count($expected_results), "The results of subtracting should be the same size as expected results");
	$this->AssertEquals(true, $are_the_same, "The results of subtracting these time groups should make sense");
   }
   public function TestLongSpanDisruptedByShortSpan() {
	$time_calc = new date_overlap_calculator('2012-08-13 12:00:00', '2012-08-13 20:00:00' );
	$splitter = new time_span('2012-08-13 15:00:00', '2012-08-13 16:00:00' );
	
	$expected_result1 = new time_span('2012-08-13 12:00:00', '2012-08-13 15:00:00' );
	$expected_result2 = new time_span('2012-08-13 16:00:00', '2012-08-13 20:00:00' );
	$results = $time_calc->find_uncovered_spans(array($splitter));
	$this->AssertEquals(count($results), 2, "Should be split in 2");
	$this->AssertEquals($expected_result1->to_string(), $results[0]->to_string());
	$this->AssertEquals($expected_result2->to_string(), $results[1]->to_string());
   }
   public function TestLongSpanDisruptedBy2ShortSpans() {
	$time_calc = new date_overlap_calculator('2012-08-13 12:00:00', '2012-08-13 20:00:00' );
	$splitters =array( new time_span('2012-08-13 15:00:00', '2012-08-13 16:00:00' ),
				new time_span('2012-08-13 16:30:00', '2012-08-13 17:00:00' ) );
	
	$expected_result1 = new time_span('2012-08-13 12:00:00', '2012-08-13 15:00:00' );
	$expected_result2 = new time_span('2012-08-13 16:00:00', '2012-08-13 16:30:00' );
	$expected_result3 = new time_span('2012-08-13 17:00:00', '2012-08-13 20:00:00' );
	$results = $time_calc->find_uncovered_spans($splitters);

	$this->AssertEquals(count($results), 3, "Should be split in 2");
	$this->AssertEquals($expected_result1->to_string(), $results[0]->to_string());
	$this->AssertEquals($expected_result2->to_string(), $results[1]->to_string());
	$this->AssertEquals($expected_result3->to_string(), $results[2]->to_string());
   }
   public function TearDown()
   {
   }
 } 

 class TimeSpanMergeTestCases extends TestCase
 {
   public function SetUp()
   {
   }

   public function Run()
   {
	$this->TestDisparateSlotsDoNotMerge();	
	$this->TestMergeOfOneSlotThatIsCompletelyCoveredByAnotherSlot();
	$this->TestMergeOfOneSlotThatStartsExactlyWhenSecondOneEnds();
	$this->TestTimeSpanWithEarlyEdateHasException();
	$this->TestStaggeredTimeSlotsMergeToOne() ;
   }

   public function TestDisparateSlotsDoNotMerge() {
	$time_one = new time_span('2011-08-01 12:00:00', '2011-08-01 13:00:00');  
	$time_two = new time_span('2012-08-01 12:00:00', '2012-08-01 13:00:00'); 
	$time_calc = new date_overlap_calculator();
	list( $result_one, $result_two ) = $time_calc->merge_slots(array($time_one, $time_two));
	$this->AssertEquals($time_one->as_string(), $result_one->as_string(), 'Time one should be equal to result one');
	$this->AssertEquals($time_two->as_string(), $result_two->as_string(), 'Time two should be equal to result two');
   }
   public function TestTimeSpanWithEarlyEdateHasException() {
	try {
		$time_one = new time_span('2012-08-01 12:00:00', '2011-08-01 13:00:00');  
		$this->Fail('Bad, we got no exception for incorrect time_span');
	} catch (Exception $e) {
		$this->Pass('Good, we got an expected exception for incorrect time_span');
	}
   }
   public function TestMergeOfOneSlotThatIsCompletelyCoveredByAnotherSlot() {
	$time_one = new time_span('2012-08-01 12:00:00', '2012-08-01 13:00:00');  
	$time_two = new time_span('2012-08-01 12:30:00', '2012-08-01 13:00:00'); 
	$time_calc = new date_overlap_calculator();
	list( $result_one ) = $time_calc->merge_slots(array($time_one, $time_two));
	$this->AssertEquals($time_one->as_string(), $result_one->as_string(), 'Time one should be equal to result one');
   }
   public function TestStaggeredTimeSlotsMergeToOne() {
	$time_calc = new date_overlap_calculator();
	for ($i = 0; $i < 10; $i++) {
		$begin = date('Y-m-d H:i:s', time() + $i * 60 );
		$end = date('Y-m-d H:i:s', time() + ($i + 1) * 60 );
		$spans[] = new time_span($begin, $end);
	}
	$results = $time_calc->merge_slots($spans);
	$this->AssertEquals(count($results), 1, "Everything should merge to 1");
	
   }
   public function TestMergeOfOneSlotThatStartsExactlyWhenSecondOneEnds() {
	$time_one = new time_span('2012-08-01 12:00:00', '2012-08-01 12:30:00');  
	$time_two = new time_span('2012-08-01 12:30:00', '2012-08-01 13:00:00'); 
	$expected_result = new time_span('2012-08-01 12:00:00', '2012-08-01 13:00:00'); 
	$time_calc = new date_overlap_calculator();
	$results  = $time_calc->merge_slots(array($time_one, $time_two));
	$result_one = $results[0];
	$this->AssertEquals($expected_result->as_string(), $result_one->as_string(), '2 touching slots of one half hour should merge to 1 single hour long slot');
   }

   public function TearDown()
   {
   }
 }

$suite = new TestSuite;
$suite->AddTest('TimeSpanMergeTestCases');
$suite->AddTest('TimeSpanFlattenTestCases');
$runner = new XHTMLTestRunner;

$runner->Run($suite, 'test_results');
echo file_get_contents('test_results.html');
?>
