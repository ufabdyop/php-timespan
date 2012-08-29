<?php
 require_once('code/php_unit_test_framework/php_unit_test.php');
 require_once('code/php_unit_test_framework/xhtml_test_runner.php');
 require_once('../code/time_span.php');
 require_once('../code/date_overlap_calculator.php');

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

$runner = new XHTMLTestRunner;

$runner->Run($suite, 'test_results');
echo file_get_contents('test_results.html');
?>
