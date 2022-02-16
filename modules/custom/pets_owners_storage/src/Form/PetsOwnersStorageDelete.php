<?php

namespace Drupal\pets_owners_storage\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;



class PetsOwnersStorageDelete extends ConfirmFormBase {

  public $id;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('pets_owners_storage.table');
  }
  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Delete pet owner');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('Delete pet owner number %id ?', array('%id' => $this->id));
  }
  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText() {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $this->id = $id;
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
   $connection = \Drupal::database();
   $connection->delete('pets_owners_storage')
     ->condition('poid', $this->id)
     ->execute();
   \Drupal::messenger()->addStatus('Succesfully deleted');
   $form_state->setRedirect('pets_owners_storage.table');
  }
}
