<?php

namespace Drupal\service_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * @Block(
 *   id = "custom_block",
 *   admin_label = @Translation("SERVICES"),
 * )
 */

class ServiceBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    //selected 3 last nodes with content type 'service'
    $query = \Drupal::entityQuery('node');
    $results = $query->condition('type', 'service', '=')
      ->sort('created', 'DESC')
      ->range(0, 3)
      ->execute();

    $entity_type_manager = \Drupal::entityTypeManager();
    $node_view_builder = $entity_type_manager->getViewBuilder('node');
    //prepare data for render in block
    foreach ($results as $id) {
     $node = $entity_type_manager->getStorage('node')->load($id);
     //get entity ID, set values in array 'render', and call view mode 'teaser'
     $data['render'][$node->id()] = $node_view_builder->view($node, 'teaser');
    }
      return [
        '#theme' => 'block__service-block',
        '#data' => $data,
        '#attached'=>[
          'library'=>[
            'service_block/service_block'
          ],
        ],
      ];
    }
}
