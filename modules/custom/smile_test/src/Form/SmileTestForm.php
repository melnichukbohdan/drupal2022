<?php

namespace Drupal\smile_test\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the smile test entity edit forms.
 */
class SmileTestForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New smile test %label has been created.', $message_arguments));
        $this->logger('smile_test')->notice('Created new smile test %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The smile test %label has been updated.', $message_arguments));
        $this->logger('smile_test')->notice('Updated smile test %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.smile_test.canonical', ['smile_test' => $entity->id()]);

    return $result;
  }

}
