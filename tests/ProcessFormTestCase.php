<?php
 require_once('code/php_unit_test_framework/php_unit_test.php');
 require_once('code/php_unit_test_framework/xhtml_test_runner.php');
 require_once('../../library/parameter.php');
 require_once('../../library/process_form.php');
 require_once('../../library/runcard.php');
 require_once('../../library/process.php');

function create_test_process_form() {
	//create process form
	$process_id = Process::create('temp_cat', 'temp_tool', 'temp_proc', 'temp_mat');
	$process = Process::get_by_id($process_id);
	
	$parameter_id = Parameter::create('tmp_name', 'tmp_type', 'tmp_unit', '1', '1', '0', '0') ;
	$parameter = Parameter::get_by_id($parameter_id);
	$process_form = Process_form::create($process);
	$process_form->add_parameter($parameter);
	$process_form->save();

	return $process_form;
}

 class ParametersTestCases extends TestCase
 {
   public function SetUp()
   {
   }
   public function Run()
   {
     // Code to perform the test case.
     $parameter = Parameter::get_by_id(1);
     $this->AssertEquals($parameter == null, false, 'There should be a parameter with id of 1');
     $this->AssertEquals($parameter->id == 1, true, 'There should be a parameter with id of 1');

     //Code to check that you can get multiple parameters by process_id
     $parameters = Parameter::get_by_process_id(2);
     $this->AssertEquals(count($parameters) > 1, true, 'There should be more than one parameter for process 2');
	
	$this->TestCreateAndDelete();
   }

   public function TestCreateAndDelete() {
	$parameter_id = Parameter::create();
	$parameter = Parameter::get_by_id($parameter_id);
	if ($parameter) {
		$this->pass("parameter created");
	} else {
		$this->fail("No parameter created");
	}
	$parameter->remove();
	    try {
		$param2 = Parameter::get_by_id($parameter->id);
		$this->fail("Should have thrown an exception");
	     } catch (Exception $e) {
		$this->pass("Should have thrown an exception");
	     }
   }

   public function TearDown()
   {
   }
 }
 class ProcessTestCases extends TestCase
 {
   public function SetUp()
   {
   }
   public function Run()
   {
	$this->TestCreateAndDelete();
   }

   public function TestCreateAndDelete() {
	$process_id = Process::create();
	$process = Process::get_by_id($process_id);
	if ($process) {
		$this->pass("process created");
	} else {
		$this->fail("No process created");
	}
	$process->remove();
	    try {
		$proc2 = Process::get_by_id($process->id);
		$this->fail("Should have thrown an exception");
	     } catch (Exception $e) {
		$this->pass("Should have thrown an exception");
	     }
   }

   public function TearDown()
   {
   }
 }

 class ProcessFormTestCases extends TestCase
 {
   public function SetUp()
   {
   }
   public function Run()
   {
	$this->TestCreateAndDelete();
	$this->TestGetProcessFromProcessForm();
	$this->TestAddParametersToProcessForm();
	
     //Code to check that you can get multiple parameters by getting a process_form
     $procs = Process_form::get_by_id(2);
     $this->AssertEquals(count($procs->parameters) > 1, true, 'There should be more than one parameter for process 2');
	
   }

   public function TestGetProcessFromProcessForm() {
     $procf = Process_form::get_by_id(2);
     $proc = $procf->process;
     $this->AssertEquals(get_class($proc), 'Process', 'We should be able to get a process from a process_form');
	
   }

   public function TestAddParametersToProcessForm() {
	//create process form
	$process_id = Process::create('temp_cat', 'temp_tool', 'temp_proc', 'temp_mat');
	$process = Process::get_by_id($process_id);
	
	$parameter_id = Parameter::create('tmp_name', 'tmp_type', 'tmp_unit', '1', '0', '0', '0') ;
	$parameter = Parameter::get_by_id($parameter_id);
	$process_form = Process_form::create($process);
	$process_form->add_parameter($parameter);
	$process_form->save();

	//get process form
	$process_form = Process_form::get_by_id($process_id);
        $retrieved_process = $process_form->process;
        $retrieved_parameter = $process_form->parameters[$parameter->id];

	//compare with originals
	$this->AssertEquals($process->equals($retrieved_process), true);

	$process_form->remove_parameter_by_id($parameter->id);
	$process_form->save();

	//check that parameters were deleted from process_form
	$this->AssertEquals($process_form->get_parameter_by_id($parameter->id), null, "check that parameters were deleted from process_form");

	$parameter->remove();
	$process->remove();
   }

   public function TestCreateAndDelete() {
	$process = Process_form::create();
	if ($process) {
		$this->pass("process created");
	} else {
		$this->fail("No process created");
	}
   }

   public function TearDown()
   {
   }
 }

 class RunCardTestCases extends TestCase
 {
   public function SetUp()
   {
   }
   public function Run()
   {
     $this->testCreateAndDeleteRunCard();
     $this->testAddToRunCard();
     $this->testRunCardWithSpecialCharacters();
   }
   
   public function testAddToRunCard() {
     $runcard = Runcard::create();
     $process_form = create_test_process_form();
     $parameter_ids = array_keys($process_form->parameters);
     $parameter = $process_form->set_parameter_by_id($parameter_ids[0], 15);
     $runcard->add_process_form($process_form);
     $runcard->save();
     
     $retrieve_card = Runcard::get_by_id($runcard->id);
     $forms = $retrieve_card->get_process_forms();
     $this->AssertEquals(count($forms), 1, 'There should be 1 process form attached to this runcard, with ID: ' . $runcard->id);
     
     //add second process_form to runcard
     $runcard->add_process_form($process_form);
     $runcard->save();

     $retrieve_card = Runcard::get_by_id($runcard->id);
     $forms = $retrieve_card->get_process_forms();
     $this->AssertEquals(count($forms), 2, 'There should be 2 process forms attached to this runcard');
     
     $runcard->remove();
   }
   
   public function testCreateAndDeleteRunCard() {
     $runcard = Runcard::create();
     $this->AssertEquals(is_numeric($runcard->id), true, 'There now be a runcard in the db');
     $runcard->remove();
     try {
        Runcard::get_by_id($runcard->id);
        $this->fail("Should have thrown an exception");
     } catch (Exception $e) {
        $this->pass("Should have thrown an exception");
     }
   }
   
   public function testRunCardWithSpecialCharacters() {
     $username = "Bob's favorite card;";
     $name = "Hubert O'malley";
     $public = 1;
     $runcard = Runcard::create($username, $name, $public);
     $runcard->save();
     $retrieved_card = Runcard::get_by_id($runcard->id);
     $this->AssertEquals($runcard->username, $username, "Username should be the same as in original");
     $this->AssertEquals($runcard->name, $name, "Name should be the same as in original");
     $runcard->remove();
   }

   public function TearDown()
   {
   }
 }

$suite = new TestSuite;
$suite->AddTest('ProcessTestCases');
$suite->AddTest('ProcessFormTestCases');
$suite->AddTest('ParametersTestCases');
$suite->AddTest('RunCardTestCases');

$runner = new XHTMLTestRunner;
$runner->Run($suite, '/tmp/ProcessFormTestCase');
echo file_get_contents('/tmp/ProcessFormTestCase.html');
?>
