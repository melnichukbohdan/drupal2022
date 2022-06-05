<?php

namespace Drupal\smile_entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the smile entity entity edit forms.
 */
class SmileEntityForm extends ContentEntityForm {

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
        $this->messenger()->addStatus($this->t('New smile entity %label has been created.', $message_arguments));
        $this->logger('smile_entity')->notice('Created new smile entity %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The smile entity %label has been updated.', $message_arguments));
        $this->logger('smile_entity')->notice('Updated smile entity %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.smile_entity.canonical', ['smile_entity' => $entity->id()]);

    return $result;
  }

}
