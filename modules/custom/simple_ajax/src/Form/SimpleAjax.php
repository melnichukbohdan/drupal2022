<?php

namespace Drupal\simple_ajax\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class SimpleAjax extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_ajax';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    //bild text field
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Simple text field',
    ];

    //bild checkbox
    $form['filds'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Clik on me!'),
      '#ajax' => [
        'callback' => '::textfieldsCallback',
        'wrapper' => 'textfields-container',
        'effect' => 'fade',
      ],
    ];

    // creates empty field 'container' and
    // set attribute id='textfields_container' in teg 'div'
    $form['textfields_container'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'textfields-container'],
    ];

    //bild checkbox
    $form['link'] = [
      '#type' => 'checkbox',
      '#title' => 'Google link',
      '#ajax' => [
        'callback' => '::googleLink',
        'event' => 'change',
      ],
    ];

    // creates empty field 'container' and
    // set attribute class='google_link' in teg 'div'
    $form['google_link'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['google_link'],
      ],
    ];

    return $form;
  }

  /**
   * Callback for ajax fields.
   */
  public function textfieldsCallback($form, FormStateInterface $form_state) {
    if ($form_state->getValue('filds', NULL) === 1) {
      // Hide or show fields
      $form['textfields_container']['textfields']['field_1'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Field 1'),
      ];

      $form['textfields_container']['textfields']['fields_2'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Field 2'),
      ];

      return $form['textfields_container'];
    }
  }

  /**
   * Callback for ajax link.
   */
  public function googleLink($form, FormStateInterface $form_state) {
    if ($form_state->getValue('link', NULL) === 1) {
      $response = new AjaxResponse();
      $selector = '.google_link';
      // Hide or show link.
      if ($form_state->getValue('link') === 1) {
        $data = '<a href="https://www.google.com/" target="_blank">Google</a>';
      }
      else {
        $data = '';
      }
      $response->addCommand(new HtmlCommand($selector, $data));
      return $response;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
