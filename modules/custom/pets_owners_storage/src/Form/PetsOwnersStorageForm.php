<?php

namespace Drupal\pets_owners_storage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PetsOwnersStorageForm extends FormBase {

  /**
   * {@Inheritdoc}
   */
  public function getFormId()   {
    return 'pets_owners_storage_form';
  }

  /**
   * {@Inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state) {
      //build form 'Pets Owners Storage Form'
      //name (text)
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Name',
      '#required' => TRUE
    ];

      //gender (radios: male, female, unknown)
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => 'Gender',
      '#options' => ['male' => 'male',
                     'female' => 'female',
                     'unknown' => 'unknown'],
      '#default_value' => 'unknown',
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
      '#empty_option' => '-select-',
      '#required' => TRUE
    ];

      //age (text, numeric)
    $form['age'] = [
      '#type' => 'number',
      '#title' => 'Age',
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
      '#title' => 'Father Name',
    ];

    $form['parents']['mother'] = [
      '#type' => 'textfield',
      '#title' => 'Mother Name'
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
      '#states' => [
        'invisible' => [
          'input[name="have_pets"]' => ['checked' => FALSE],
        ],
      ],
    ];

    // email (text)
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => 'Email',
      '#required' => TRUE
    ];

    //button 'Submit'
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit'
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
      $form_state->setErrorByName('name',
        $this->t('Enter valid name'));
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

    // get data with form
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
      $connection
        ->insert('pets_owners_storage')
        ->fields($data)
        ->execute();
    } catch (Exception $e) {
      $transaction
      ->rollBack();
      watchdog_exception('type', $e);
    }

    //invalidate cache
    \Drupal::service('cache_tags.invalidator')
      ->invalidateTags(['node_list']);

    $this->messenger()->addMessage($this->t('Thank you.'));
    $form_state->setRedirect('pets_owners_storage.table');
  }
}

