<?php

namespace Drupal\pets_owners_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PetsOwnersForm extends FormBase {


  /*
   * {@Inheritdoc}
   */
  public function getFormId()   {
    return 'pets_owners_form';
  }

  /*
   * {@Inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state) {
      //build form 'Pets Owners Form'
      //name (text)
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Name',
    //  '#required' => TRUE
    ];
      //gender (radios: male, female, unknown)
    $form['settings']['active'] = [
      '#type' => 'radios',
      '#title' => 'Gender',
      '#options' => [0 => 'male',
                     1 => 'female',
                     2 => 'unknown'],
      '#default_value' => 2,
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
   //   '#required' => TRUE
    ];
      //age (text, numeric)
    $form['age'] = [
      '#type' => 'number',
      '#title' => 'Age',
   //   '#required' => TRUE

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

    //“Have you some pets?“ (checkbox), names(s) of your pet(s) (text, shown only when “Have you some pets?” checked),
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
   //   '#required' => TRUE
    ];
      //buttom 'Submit'
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit'
    ];

    return $form;

  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

    // check age
    if ($form_state->getValue('age') <= 0 || $form_state->getValue('age') > 120) {
      $form_state->setErrorByName('age', $this->t('Enter valid age'));
    }

    //check name
    if (mb_strlen(trim($form_state->getValue('name'))) <= 0 || mb_strlen(trim($form_state->getValue('name'))) >= 100) {
      $form_state->setErrorByName('name', $this->t('Enter valid name'));
    }

    // checkemail
    if (!$form_state->getValue('email') || !filter_var($form_state->getValue('email'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', $this->t('Enter valid e-mail'));
    }

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {


  }


}
