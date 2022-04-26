<?php

namespace Drupal\mass_operations\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class CustomCron extends ConfigFormBase {

  use StringTranslationTrait;
  use MessengerTrait;

   /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'custom_cron_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'custom_cron.set_parameters'
    ];
  }

  /**
   * Form for set config to cron.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Parameters for job'),
    ];

    // Items.
    $form['item'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nodes to unpablish'),
      '#default_value' => 15,
      '#required' => TRUE,
      '#attributes' => [
        ' type' => 'number',
      ],
    ];

    //button 'Add few nodes'
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add to queue'),
      '#button_type' => 'primary',
    ];

    //button 'All nodes'
    $form['actions']['all'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add all nodes to queue'),
      '#submit' => ['::addAllNodes'],
    ];

    $form['warning'] = [
      '#type' => 'item',
      '#markup' => $this->t('WARNING! The queue will be trimmed at the next Cron'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('custom_cron.set_parameters')
      ->set('item', $form_state->getValue('item'))
      ->save();

    $this->messenger()->addMessage($this->t('@count nodes  added',[
      '@count' => $form_state->getValue('item')]));
  }

  public function addAllNodes (array &$form, FormStateInterface $form_state) {

    $nodes = \Drupal::entityQuery('node')
      ->condition('status', '1')
      ->count()
      ->execute();
    $this->config('custom_cron.set_parameters')
      ->set('item', $nodes)
      ->save();

    $this->messenger()->addMessage($this->t('@count nodes  added',[
      '@count' => $nodes]));
  }

}

