<?php

namespace Drupal\rfi_form\Form;

use Drupal\Core\Database\Driver\mysql\Connection as DataBaseConnection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RFIForm extends FormBase {

  const EMAIL_REGEX = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
  const PHONE_REGEX = '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/';

  private DataBaseConnection $dbConnection;

  public function __construct(DataBaseConnection $dbConnection) {
    $this->dbConnection = $dbConnection;
  }

  /**
   * @inheritDoc
   */
  public function getFormId(): string {
    return 'rfi_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name')
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name')
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email')
    ];

    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone Number')
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit')
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $formValues = $form_state->getValues();
    // Validate First Name
    if(strlen($formValues['first_name']) > 40 || $this->hasInvalidCharacters($formValues['first_name'])) {
      $form_state->setErrorByName('first_name', 'Invalid First Name');
    }

    // Validate Last Name
    if(strlen($formValues['last_name']) > 40 || $this->hasInvalidCharacters($formValues['last_name'])) {
      $form_state->setErrorByName('last_name', 'Invalid Last Name');
    }

    //Validate Email
    if(preg_match(self::EMAIL_REGEX, $formValues['email']) === 0) {
      $form_state->setErrorByName('email', 'Please Check your email address');
    }

    //Validate Phone Number
    if(preg_match(self::PHONE_REGEX, $formValues['phone_number']) === 0) {
      $form_state->setErrorByName('phone_number', 'Your phone number is invalid');
    }
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->dbConnection->insert('rfi_submissions')
      ->fields(['lead' => json_encode($form_state->getValues())])->execute();
  }

  private function hasInvalidCharacters($name): bool {
    return preg_match('~[0-9]+~', $name) > 0;
  }

}