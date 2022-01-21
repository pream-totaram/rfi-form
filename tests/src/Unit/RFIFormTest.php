<?php
namespace Drupal\Tests\rfi_form\Unit;

use Drupal\rfi_form\Form\RFIForm;
use Drupal\Core\Form\FormState;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RFIFormTest extends UnitTestCase {

  private RFIForm $form;

  private FormState $formState;

  private array $formFields;

  public function setUp() : void {
    parent::setUp();
    \Drupal::setContainer($this->prophesize(ContainerInterface::class)->reveal());
    $container = \Drupal::getContainer();
    $db = new \stdClass();
    $db->insert = function ($table) {

    };
    $container->setParameter('database', $db);
    $state = array (
      'first_name' => 'FirstName',
      'last_name'=> 'LastName',
      'email' => 'somebody@example.com',
      'phone_number' => '5555555555',
      'submit' => 'Submit',
    );
    $this->formState = (new FormState())->setValues($state);
    $this->formState->setSubmitted();
    $this->form = RFIForm::create($container);
    $this->formFields = $this->form->buildForm([],$this->formState);
  }

  public function testGetFormId() {
    $this->assertEquals('rfi_form', $this->form->getFormId());
  }

  public function testValidateForm() {
    $this->form->validateForm($this->formFields, $this->formState);
    $this->assertCount(0, $this->formState->getErrors());
  }

  public function testInvalidFirstName() {
    $this->formState->setValue('first_name', 'FirstName0');
    $this->form->validateForm($this->formFields, $this->formState);
    $this->assertArrayHasKey('first_name', $this->formState->getErrors());
  }

  public function testInvalidLastName() {
    $this->formState->setValue('last_name', 'LastName0');
    $this->form->validateForm($this->formFields, $this->formState);
    $this->assertArrayHasKey('last_name', $this->formState->getErrors());
  }

  public function testInvalidEmailAddress() {
    $this->formState->setValue('email', 'email');
    $this->form->validateForm($this->formFields, $this->formState);
    $this->assertArrayHasKey('email', $this->formState->getErrors());
  }

  public function testInvalidPhoneNumber() {
    $this->formState->setValue('phone_number', '2');
    $this->form->validateForm($this->formFields, $this->formState);
    $this->assertArrayHasKey('phone_number', $this->formState->getErrors());
  }

  public function testSubmitForm() {
    $this->form->submitForm($this->formFields, $this->formState);
  }

}
