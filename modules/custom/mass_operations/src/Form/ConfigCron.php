<?php

namespace Drupal\mass_operations\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigCron extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mass_operations_cron_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mass_operations.config'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['period'] = [
      '#type' => 'number',
      '#title' => 'Period',
      '#description' => 'min 180 days',
      '#default_value' => $this->config('mass_operations.set_param')->get('period'),
      '#required' => TRUE
    ];

    $form['items'] = [
      '#type' => 'number',
      '#title' => 'Items',
      '#description' => 'min 5, max 25',
      '#default_value' => $this->config('mass_operations.set_param')->get('items'),
      '#required' => TRUE
    ];

    $form['disabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Active'),
      '#default_value' => $this->config('mass_operations.set_param')->get('disabled'),
    ];

    $form['unpublsihed_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Add to label'),
      '#default_value' => $this->config('mass_operations.set_param')->get('unpublsihed_label'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->getValue('period') < 180) {
      $form_state->setErrorByName('period', $this->t('Enter valid period'));
    }

    if ($form_state->getValue('items') < 5 || $form_state->getValue('items') > 25) {
      $form_state->setErrorByName('items', $this->t('Enter valid items'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()->getEditable('mass_operations.set_param')
      ->set('period', $form_state->getValue('period'))
      ->set('items', $form_state->getValue('items'))
      ->set('disabled', $form_state->getValue('disabled'))
      ->set('unpublsihed_label', $form_state->getValue('unpublsihed_label'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
