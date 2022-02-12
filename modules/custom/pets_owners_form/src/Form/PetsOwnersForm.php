<?php

namespace Drupal\pets_owners_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PetsOwnersForm extends FormBase {

  public function getFormId()   {
    return 'pets_owners_form';
  }

  /*
   * build form 'Pets Owners Form'
   */

  public function buildForm(array $form, FormStateInterface $form_state) {

      //name (text)
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Name',
      '#required' => TRUE
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
      '#title' => $this->t('prefix'),
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
      '#title' => 'age',
      '#required' => TRUE

    ];
    // parents (fieldset collapsed),  * father`s name (text in parents fieldset),
    //                                * mother`s name (text in parents fieldset),
    $form['parents'] = [
      '#type' => 'details',
      '#title' => 'parents',
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
      '#title' => 'name of your pet',
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
      //buttom 'Submit'
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit'
    ];

    return $form;

  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.
  }

}
