<?php
//////////////////////////////////////////////////////////
///
/// $Author: edheal $
/// $Date: 2011-04-06 00:45:11 +0100 (Wed, 06 Apr 2011) $
/// $Id: test_unit_test.php 16 2011-04-05 23:45:11Z edheal $
///
/// \file
/// \brief Uses the PHP Unit Testing Framework to test itself.
///
/// \details
/// 
/// This file contains code that uses the PHP Unit Testing Framework
/// to verify that the Assert class functions correctly and the
/// framework is able to generate correct reports.
///
/// It generates XML, ASCII and XHTML reports.
///
/// \section License
///
/// A PHP Unit testing framework
///
/// Copyright (C) 2011 Ed Heal (ed.heal@yahoo.co.uk)
/// 
/// This program is free software: you can redistribute it and/or modify
/// it under the terms of the GNU General Public License as published by
/// the Free Software Foundation, either version 3 of the License, or
/// (at your option) any later version.
/// 
/// This program is distributed in the hope that it will be useful,
/// but WITHOUT ANY WARRANTY; without even the implied warranty of
/// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
/// GNU General Public License for more details.
/// 
/// You should have received a copy of the GNU General Public License
/// along with this program.  If not, see <http://www.gnu.org/licenses/>.
///
//////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
///
/// \mainpage
/// \section license License
///
/// A PHP Unit testing framework
///
/// Copyright (C) 2011 Ed Heal (ed.heal@yahoo.co.uk)
/// 
/// This program is free software: you can redistribute it and/or modify
/// it under the terms of the GNU General Public License as published by
/// the Free Software Foundation, either version 3 of the License, or
/// (at your option) any later version.
/// 
/// This program is distributed in the hope that it will be useful,
/// but WITHOUT ANY WARRANTY; without even the implied warranty of
/// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
/// GNU General Public License for more details.
/// 
/// You should have received a copy of the GNU General Public License
/// along with this program.  If not, see <http://www.gnu.org/licenses/>.
///
/// \section intro Test cases for PHP Unit Testing Framework
///
/// Tests to check that the Assert class functions correctly along with
/// the rest of the testing framework.
///
/// \section finally Finnally
///
/// If you have any comments, please contact me at 
/// <a href="mailto:ed.heal@yahoo.co.uk">ed.heal@yahoo.co.uk</a>.
///
/// I hope that this small library will help in the development
/// of good quality PHP software.
//////////////////////////////////////////////////////////

require_once '../php_unit_test_framework/xhtml_test_runner.php';
require_once '../php_unit_test_framework/text_test_runner.php';

//////////////////////////////////////////////////////////
/// \brief A simple class for testing.
//////////////////////////////////////////////////////////
class X
{
  public $a = 'This is class X';   ///< To verify the output functionality.
  private $b = 1;                  ///< To verify the output functionality.        
  protected $c = 2.0;              ///< To verify the output functionality.
  public $d = true;                ///< To verify the output functionality.
}

//////////////////////////////////////////////////////////
/// \brief A simple class for testing but with Equals defined.
//////////////////////////////////////////////////////////
class Y implements Equality
{
  public $a;       ///< To verify the output functionality.
  private $b;      ///< To verify the output functionality.
  protected $c;    ///< To verify the output functionality.
  public $d;       ///< To verify the output functionality.
  private $result; ///< To verify the output functionality.
  
  //////////////////////////////////////////////////////////
  /// \brief A simple class for testing but with Equals defined.
  /// \param[in] bool $result Sets if Equals returns true or false.
  //////////////////////////////////////////////////////////
  public function __construct($result)
  {
     $this->a = 'This is class Y';
     $this->b = 101;
     $this->c = 21.0;
     $this->d = false;  
     $this->result = $result;
  }
  
  //////////////////////////////////////////////////////////
  /// \brief The Equals function used in the PHP Unit test framework
  ///        for testing equality between objects.
  /// \param[in] object $obj Object to be compared.
  //////////////////////////////////////////////////////////   
  public function Equals($obj)
  {
    return $this->result;
  }
}

//////////////////////////////////////////////////////////
/// \brief Throws an exception in the SetUp phase.
///
/// This is to verify that if an exception is thrown that
/// it is handled correctly. I.e an error and Run is not
/// executed.
///
/// Also checks the TestCase constructor allows tests to be
/// named.
//////////////////////////////////////////////////////////
class SetUpThrowsException extends TestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Constucts the test case with a name.
  //////////////////////////////////////////////////////////
  public function __construct()
  {
    parent::__construct('Just name');
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case and throws an exception.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Throws an exception in the SetUp phase');
    throw new Exception('Set up should throw this exception and not catch it!');
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case. Should not be executed.
  //////////////////////////////////////////////////////////

  public function Run()
  {
    $this->AddMessage('This should not occur - Run!');
  }
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    $this->AddMessage('This should occur - TearDown!');
  }
}

//////////////////////////////////////////////////////////
/// \brief Throws an exception in the Run phase.
///
/// This is to verify that the exception is reported
/// and also tear down is executed.
///
/// Also checks that test cases can be constructed
/// with a name and a description.
//////////////////////////////////////////////////////////
class RunThrowsException extends TestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Constucts the test case with a name and a description.
  //////////////////////////////////////////////////////////
  public function __construct()
  {
    parent::__construct('Name & Description', 'Here is the description');
  }

  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Throws an exception in the Run phase');
  }
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    throw new Exception('Run is throwing this exception and not catching it!');
  }
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    $this->AddMessage('This should occur - TearDown!');
  }
}

//////////////////////////////////////////////////////////
/// \brief Throws an exception in the TearDown phase.
//////////////////////////////////////////////////////////
class TearDownThrowsException extends TestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Constucts the test case with a name,description and a ID.
  //////////////////////////////////////////////////////////
  public function __construct()
  {
    parent::__construct('Name, Description and ID', 'Here is the description', 'ID');

  }

  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Throws an exception in the tear down phase');
  }
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    $this->AddMessage('This should occur - Run!');
  }
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case but throws an exception
  ///        that should be caught and reported.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    throw new Exception('Tear down thowing this exception and not catching it.');
  }
}

//////////////////////////////////////////////////////////
/// \brief A test case that should pass.
//////////////////////////////////////////////////////////
class Pass extends TestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Runs without any exceptions or assertions - so should pass');
  }
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    $this->AddMessage('Should run Run');
  }
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    $this->AddMessage('Should run TearDown');
  }
}

//////////////////////////////////////////////////////////
/// \brief Just fails with one assertion.
//////////////////////////////////////////////////////////
class Fail extends TestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Calls an assertion that fails');
  }
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    $this->AddMessage('Should run Run');
    $this->AssertEquals(true, false, 'Fail here!');
    $this->AddMessage('Should get here');
  }
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    $this->AddMessage('Should run TearDown');
  }
}

//////////////////////////////////////////////////////////
/// \brief Tests the user pass/fail messages work.
//////////////////////////////////////////////////////////
class PassFail extends TestCase
{
  public function __construct()
  {
    parent::__construct();
  }
  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Verifies that the pass/fail user messages work');
  }
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    $this->Fail('Fail here');
    $this->Pass('Pass here');
  }
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    $this->AddMessage('Should run TearDown');
  }
}

//////////////////////////////////////////////////////////
/// \brief Sets up two arrays to check against one another
///        by either AssertEquals or AssertNotEquals.
///
/// The two arrays consist of all the possible combinations
/// that AssertEquals and AssertNotEquals should be able
/// to handle. Each item of the first array is checked 
/// against each of the items in the second array. The appropriate
/// assertion function is used and is implemented in the
/// abstract function PerformAssert.
//////////////////////////////////////////////////////////
abstract class AssertTestCase extends TestCase
{
  private $arrayOne;
  private $arrayTwo;
  
  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case by creating the arrays.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $resourceOne = tmpfile();
    $resourceTwo = tmpfile();
    $resourceThree = tmpfile();
    
    $this->arrayOne = array();
    
    // Fill with NULL, string, integer, double, boolean, object, resource, array
    
    $this->arrayOne[] = null;
    $this->arrayOne[] = 'A string';
    $this->arrayOne[] = 123;
    $this->arrayOne[] = 2.5;
    $this->arrayOne[] = true;
    $this->arrayOne[] = new X;
    $this->arrayOne[] = array(null, 'A second string', 125, 7.5, (new Y(true)),
                              (new Y(false)), array(1, 'hello world!', 9.8, (new X)),
                        $resourceOne);
    $this->arrayOne[] = $resourceTwo;

    $this->arrayTwo = array();
    $this->arrayTwo[] = null;
    $this->arrayTwo[] = 'Another string';
    $this->arrayTwo[] = 321;
    $this->arrayTwo[] = 5.2;
    $this->arrayTwo[] = false;
    $this->arrayTwo[] = new X;
    $this->arrayTwo[] = new Y(true);
    $this->arrayTwo[] = new Y(false);
    $this->arrayTwo[] = $resourceThree;
    $this->arrayTwo[] = $this->arrayOne;
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    // Nothing needs to be done as GC will do the work!
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Implement with either AssertEquals or AssertNotEquals..
  ////////////////////////////////////////////////////////// 
  abstract protected function PerformAssert($x, $y, $msg);
  
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case by performing the assertions.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    $counter = 1;
    foreach ($this->arrayOne as $x)
    {
      $this->PerformAssert($x, $x, 'Need to verify');
      foreach ($this->arrayTwo as $y)
      {
          $this->PerformAssert($x, $y, 'Need to verify');
      }
    }
  }
}

//////////////////////////////////////////////////////////
/// \brief Tests the AssertEquals function.
//////////////////////////////////////////////////////////
class AssertEqualsTestCase extends AssertTestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Sets up for the AssertEqualsTestCase
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Test to check AssertEquals works');
    parent::SetUp();
  }

  //////////////////////////////////////////////////////////
  /// \brief Performs the AssertEquals function
  /// \param[in] mixed $x First value.
  /// \param[in] mixed $y Second value value.
  /// \param[in] string $msg User defined message.
  //////////////////////////////////////////////////////////
  protected function PerformAssert($x, $y, $msg)
  {
    $this->AssertEquals($x, $y, $msg);
  }
}

//////////////////////////////////////////////////////////
/// \brief Tests the AssertNotEquals function.
//////////////////////////////////////////////////////////
class AssertNotEqualsTestCase extends AssertTestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Sets up for the AssertNotEqualsTestCase
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Test to check AssertNotEquals works');
    parent::SetUp();
  }

  //////////////////////////////////////////////////////////
  /// \brief Performs the AssertNotEquals function
  /// \param[in] mixed $x First value.
  /// \param[in] mixed $y Second value value.
  /// \param[in] string $msg User defined message.
  //////////////////////////////////////////////////////////
  protected function PerformAssert($x, $y, $msg)
  {
    $this->AssertNotEquals($x, $y, $msg);
  }
}
 
//////////////////////////////////////////////////////////
/// \brief Tests the AssertMatches function.
//////////////////////////////////////////////////////////
class AssertMatchesTestCase extends TestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Verifies Assert[Not]Matches works');
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    $this->AssertMatches(0, '/o.*[0-4]d$/', 'Error');
    $this->AssertMatches('Hello world', 0, 'Error');
    $this->AssertNotMatches(0, '/o.*[0-4]d$/', 'Error');
    $this->AssertNotMatches('Hello world', 0, 'Error');
    
    $this->AssertMatches(' hello wor3d', '/o.*[0-4]d$/', 'Pass');
    $this->AssertNotMatches(' hello wor3d', '/o.*[0-4]d$/', 'Fail');
    $this->AssertMatches(' hello wor5d', '/o.*[0-4]d$/', 'Fail');
    $this->AssertNotMatches(' hello wor5d', '/o.*[0-4]d$/', 'Pass');
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    // Blank
  }
}

//////////////////////////////////////////////////////////
/// \brief Tests the AssertOutputEquals, AssertOutputDiffers,
///        AssertOutputMatches and AssertOutputNotMatches functions.
//////////////////////////////////////////////////////////
class OutputTestCase extends TestCase
{
  //////////////////////////////////////////////////////////
  /// \brief Sets up the test case.
  //////////////////////////////////////////////////////////
  public function SetUp()
  {
    $this->AddMessage('Verifies AssertOutput* works');
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Performs the test case.
  //////////////////////////////////////////////////////////
  public function Run()
  {
    $this->AssertOutputEquals(5, 'Error');
    $this->AssertOutputDiffers(5, 'Error');
    $this->AssertOutputMatches(5, 'Error');
    $this->AssertOutputNotMatches(5, 'Error');
    
    $msg = 'Hello world';
    echo $msg;
    $this->AssertOutputEquals($msg, 'Pass');
    $this->AssertOutputDiffers($msg, 'Fail');

    $this->ReStartOutputChecking();
    
    $msg = 'Hello world';
    echo 'Something else';
    echo $msg;
    $this->AssertOutputEquals($msg, 'Fail');
    $this->AssertOutputDiffers($msg, 'Pass');
    
    $this->ReStartOutputChecking();
    
    echo 'Hell0 wor1d';
    
    $this->AssertOutputMatches('/[0-3].*r1/', 'Pass');
    $this->AssertOutputNotMatches('/[0-3].*r1/', 'Fail');  
    $this->AssertOutputMatches('/[0-3].*r2/', 'Fail');
    $this->AssertOutputNotMatches('/[0-3].*r2/', 'Pass');  
  }
  
  //////////////////////////////////////////////////////////
  /// \brief Tidy up after the test case.
  //////////////////////////////////////////////////////////
  public function TearDown()
  {
    echo "\n----------------------------------------------\n";
  }
}

//////////////////////////////////////////////////////////
/// \brief Run the tests and generate:
///        - ASCII;
///        - XML;
///        - XHTML.
///
//////////////////////////////////////////////////////////

$suite = new TestSuite;  ///< The suite of test cases to be executed. 

$suite->AddTest('SetUpThrowsException');
$suite->AddTest('RunThrowsException');
$suite->AddTest('TearDownThrowsException');
$suite->AddTest('Pass');
$suite->AddTest('Fail');
$suite->AddTest('PassFail');
$suite->AddTest('AssertEqualsTestCase');
$suite->AddTest('AssertNotEqualsTestCase');
$suite->AddTest('AssertMatchesTestCase');
$suite->AddTest('OutputTestCase');

$runner = new XMLTestRunner; ///< The test case runner to execute the tests.
$runner->Run($suite, 'report');

$runner = new TextTestRunner;
$runner->Run($suite, 'report');

$runner = new XHTMLTestRunner;
$runner->Run($suite, 'report');