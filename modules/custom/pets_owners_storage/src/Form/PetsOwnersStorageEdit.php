<?php

namespace Drupal\pets_owners_storage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PetsOwnersStorageEdit extends FormBase {

  /**
   * {@Inheritdoc}
   */
  public function getFormId()   {
    return 'pets_owners_storage_edit';
  }

  /**
   * {@Inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state) {

    // get data from database
    $connection = \Drupal::database()
      ->select('pets_owners_storage', 'p');
    $values = $connection->fields('p', [
      'poid',
      'name',
      'prefix',
      'gender',
      'age',
      'father',
      'mother',
      'pet_name',
      'email'])
    ->condition('poid', $this->getPOID())
    ->execute()->fetchAssoc();

    //build form 'Pets Owners Storage Edit'
    //name (text)
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Name',
      '#default_value' => $values['name'],
      '#required' => TRUE
    ];

      //gender (radios: male, female, unknown)
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => 'Gender',
      '#options' => ['male' => 'male',
                     'female' => 'female',
                     'unknown' => 'unknown'],
      '#default_value' => $values['gender'],
    ];

      //prefix (dropdown: mr, mrs, ms)
    $form['prefix'] = [
      '#type' => 'select',
      '#title' => $this->t('Prefix'),
      '#options' => [
        'mr' => 'mr',
        'mrs' => 'mrs',
        'ms' => 'ms'
      ],
      '#default_value' => $values['prefix'],
      '#empty_option' => '-select-',
      '#required' => TRUE
    ];

      //age (text, numeric)
    $form['age'] = [
      '#type' => 'number',
      '#title' => 'Age',
      '#default_value' => $values['age'],
      '#required' => TRUE
    ];

   // parents (fieldset collapsed),  * father`s name (text in parents fieldset),
   //                                * mother`s name (text in parents fieldset),
    $form['parents'] = [
      '#type' => 'details',
      '#title' => 'Parents',
    ];

    $form['parents']['father'] = [
      '#type' => 'textfield',
      '#title' => 'Father name',
      '#default_value' => $values['father'],
    ];

    $form['parents']['mother'] = [
      '#type' => 'textfield',
      '#title' => 'Mother name',
      '#default_value' => $values['mother'],
    ];

    //“Have you some pets?“ (checkbox), names(s) of your pet(s)
    // (text, shown only when “Have you some pets?” checked),
    $form['have_pets'] = [
      '#type' => 'checkbox',
      '#title' => 'Have you some pets?'

    ];
    $form['pet_name'] = [
      '#type' => 'textfield',
      '#title' => 'Name of your pet',
      '#default_value' => $values['pet_name'],
      '#states' => [
        'invisible' => [
          'input[name="have_pets"]' => ['pet_name' => 1],
        ],

      ],
    ];

    // email (text)
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => 'Email',
      '#default_value' => $values['email'],
      '#required' => TRUE
    ];

    //button 'Edit'
    $form['actions']['edit'] = [
      '#type' => 'submit',
      '#value' => 'Edit'
    ];

    //button 'Delete'
    $form['actions']['delete'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#submit' => ['::delete'],
    ];
    return $form;
  }

  /**
   * {@Inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state) {

    // check age
    if ($form_state->getValue('age') <= 0 || $form_state->getValue('age') > 120) {
      $form_state->setErrorByName('age', $this->t('Enter valid age'));
    }

    //check name
    if (mb_strlen(trim($form_state->getValue('name'))) <= 0 ||
        mb_strlen(trim($form_state->getValue('name'))) >= 100) {
      $form_state->setErrorByName('name', $this->t('Enter valid name'));
    }

    // check email
    if (!$form_state->getValue('email') || !filter_var($form_state->getValue('email'),
        FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', $this->t('Enter valid e-mail'));
    }
  }

   /**
   * {@Inheritdoc}
   */
    public function submitForm(array &$form, FormStateInterface $form_state) {

    // get data with edit form
    $data = [
      'name' => $form_state->getValue('name'),
      'gender' => $form_state->getValue('gender'),
      'prefix' => $form_state->getValue('prefix'),
      'age' => $form_state->getValue('age'),
      'father' => $form_state->getValue('father'),
      'mother' => $form_state->getValue('mother'),
      'pet_name' => $form_state->getValue('pet_name'),
      'email' => $form_state->getValue('email'),
    ];

      $connection = \Drupal::database();
      $transaction = $connection
        ->startTransaction();
      try {
        $connection->update('pets_owners_storage')
        ->fields($data)
        ->condition('poid', $this->getPOID())->execute();
      } catch (Exception $e) {
        $transaction
          ->rollBack();
        watchdog_exception('type', $e);
      }

    // show message and redirect to list page
    \Drupal::messenger()->addStatus('Succesfully edit');
    $form_state->setRedirect('pets_owners_storage.table');
  }

  //get user id with route
  public function getPOID () {
    $route = $_SERVER['REQUEST_URI'];
    preg_match('#^/pets_owners_storage/(\d)/edit$#', $route, $matches);
    $poid = $matches[1];
    return $poid;
  }

  // redirect on route - pets_owners_storage.delete
  public function delete(array &$form, FormStateInterface $form_state) {
    $id = ['id' => $this->getPOID()];
    $form_state->setRedirect('pets_owners_storage.delete', $id);
  }
}

