<?php

namespace Drupal\smile_entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the smile entity entity type.
 */
class SmileEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if (in_array($entity->role->value, $account->getRoles())) {
       switch ($operation) {
          case 'view':
            return AccessResult::allowed();

          case 'update':
            return AccessResult::allowed();

          case 'delete':
            return AccessResult::allowed();

          default:
            // No opinion.
            return AccessResult::neutral();
       }
    }
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions(
      $account,
      ['create smile entity', 'administer smile entity'],
      'OR',
    );
  }

}
