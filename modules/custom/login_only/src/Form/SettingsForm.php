<?php

namespace Drupal\login_only\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure login_only settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'login_only_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['login_only.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['login_only_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Login Only mode'),
      '#default_value' => $this->config('login_only.settings')->get('login_only_mode'),
      '#description' => '
      <h4>If enable this mode:</h4>
      <h3>Anonymous user</h3>
      1. Anonymous user can’t access any page except Login and pages related to the password recovery <br>
      2. Every node page, home page, etc. should not be accessible for anonymous user -
      he will be always redirected to login page
      <h3>Authenticated user</h3>
      1. Every node page, home page, etc. not be accessible for authenticated user -
      he will be always redirected to his profile page <br>
      2. Authenticated user can’t access any page except his profile page, Contact page and My Contact Submission pages.
      ',
    ];


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('login_only.settings')
      ->set('login_only_mode', $form_state->getValue('login_only_mode'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
