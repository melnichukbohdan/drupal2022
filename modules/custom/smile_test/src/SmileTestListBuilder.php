<?php

namespace Drupal\smile_test;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides a list controller for the smile test entity type.
 */
class SmileTestListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['table'] = parent::render();

    $total = $this->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();

    $build['summary']['#markup'] = $this->t('Total smile tests: @total', ['@total' => $total]);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Client ID');
    $header['client_name'] = $this->t('Client name');
    $header['prefered_brand'] = $this->t('Prefered Brand');
    $header['products_owned_count'] = $this->t('Count of products');
    $header['registration_date'] = $this->t('Date of registration');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\smile_test\SmileTestInterface $entity */
    $row['id'] = $entity->id();
    $row['client_name'] = $entity->client_name->value;
    $row['prefered_brand'] = $entity->prefered_brand->value;
    $row['products_owned_count'] = $entity->products_owned_count->value;
    $row['registration_date'] = DrupalDateTime::createFromTimestamp($entity->registration_date->value);
    return $row + parent::buildRow($entity);
  }

}
