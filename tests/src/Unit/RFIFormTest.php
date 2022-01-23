<?php
namespace Drupal\Tests\rfi_form\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\rfi_form\Form\RFIForm;
use Drupal\Core\Form\FormState;
use Drupal\Tests\UnitTestCase;

class RFIFormTest extends UnitTestCase {

  private RFIForm $form;

  private FormState $formState;

  private array $formFields;

  public function setUp() : void {
    parent::setUp();
    $container = new ContainerBuilder();
    $insert = $this->getMockBuilder('\Drupal\Core\Database\Query\Insert')->disableOriginalConstructor()->getMock();
    $insert->method('fields')->willReturn($insert);
    $insert->method('execute')->willReturn(1);
    $conn = $this->getMockBuilder('\Drupal\Core\Database\Driver\mysql\Connection')
      ->disableOriginalConstructor()
      ->getMock();
    $conn->method('insert')->willReturn($insert);
    $translation = $this->getMockBuilder('\Drupal\Core\StringTranslation\TranslationManager')
      ->disableOriginalConstructor()->getMock();
    $container->set('database', $conn);
    $container->set("string_translation", $translation);
    \Drupal::setContainer($container);
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
