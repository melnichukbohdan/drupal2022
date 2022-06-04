<?php

namespace Drupal\node_save_event\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\node\NodeInterface;

class NodeSaveEvent extends Event {

  /**
   * Called during hook_node_insert()
   */
  const NODE_SAVE = 'nodeSave';

  /**
   * @param NodeInterface $node
   */
  public function __construct(NodeInterface $node) {
      $this->node = $node;
  }

  /**
   * @return NodeInterface
   */
  public function getNode () {
    return $this->node;
  }
}
