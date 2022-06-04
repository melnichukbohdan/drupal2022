<?php

namespace Drupal\node_save_event\EventSubscriber;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\node_save_event\Event\NodeSaveEvent;
use Drupal\Core\Messenger\MessengerInterface;

class NodeSaveEventsSubscriber implements EventSubscriberInterface {

use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;


  public function __construct(MessengerInterface $messenger)   {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents () {
    return [
      NodeSaveEvent::NODE_SAVE => ['nodeSave', -1]
    ];
  }

  /**
   * Generate message for user after save some node
   * Message have node type and node title
   *
   * @param NodeSaveEvent $event
   */
  public function nodeSave (NodeSaveEvent $event) {
    $this->messenger->addMessage($this->t('@node_type : @title  saved!', [
      '@node_type' => $event->getNode()->getType(),
      '@title' => $event->getNode()->getTitle(),
    ]));
  }
}
