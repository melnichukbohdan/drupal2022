<?php

namespace Drupal\smile_test;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a smile test entity type.
 */
interface SmileTestInterface extends ContentEntityInterface, EntityOwnerInterface {

}
