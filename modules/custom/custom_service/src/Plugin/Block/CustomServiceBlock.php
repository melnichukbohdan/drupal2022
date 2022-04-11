<?php

namespace Drupal\custom_service\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an custom service block.
 *
 * @Block(
 *   id = "custom_service_block",
 *   admin_label = @Translation("Custom Service Block"),
 *   category = @Translation("custom_service_block")
 * )
 */
class CustomServiceBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data['name'] = \Drupal::service('custom_service.custom_services')->getData();
    $data['count'] = \Drupal::service('custom_service.custom_services')->getActiveUsers();
    $data['id'] = \Drupal::service('custom_service.custom_services')->getPositionOfRegistration();
    $data['node'] = \Drupal::service('custom_service.custom_services')->getNode();

  return [
    '#theme' => 'service_template',
    '#data' => $data,
    '#attached'=>[
      'library'=>[
        'custom_service/custom_service'
      ],
    ],
  ];
  }

}
