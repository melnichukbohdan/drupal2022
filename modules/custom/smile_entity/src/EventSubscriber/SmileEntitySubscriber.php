<?php

namespace Drupal\smile_entity\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Smile Entity event subscriber.
 */
class SmileEntitySubscriber implements EventSubscriberInterface {

  /**
   * The route match service.
   *
   * @var Drupal\Core\Routing\RouteMatchInterface
   */
  protected $current_route_match;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $current_user;

  /**
   * @param RouteMatchInterface $current_route_match
   * @param AccountInterface $current_user
   */
  public function __construct(RouteMatchInterface $current_route_match,
                              AccountInterface $current_user) {
    $this->current_route_match = $current_route_match;
    $this->current_user = $current_user;
  }


  /**
   * Kernel request event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Response event.
   */
  public function onKernelRequest(RequestEvent $event) {
    //get Request Header 'Referer' and turn or equal to 'http://drupal2022/'
    $referer = $event->getRequest()->headers->get('referer');
    $checkReferer = preg_match('/^(https?:\/\/)?(drupal2022\/)$/', $referer);

    //get route for some entity
    $route = $this->getCurrentRouteMatch()->getRouteName();

    // get user ID
    $user = $this->getCurrentUser()->id();

    if ($route == 'entity.smile_entity.canonical' && $checkReferer == FALSE && $user != 1) {
      $event->setResponse(new RedirectResponse('http://drupal2022/'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onKernelRequest'],
    ];
  }

  /**
   * @return Drupal\Core\Routing\RouteMatchInterface
   */
  public function getCurrentRouteMatch(): RouteMatchInterface {
    return $this->current_route_match;
  }

  /**
   * @return AccountInterface
   */
  public function getCurrentUser(): AccountInterface {
    return $this->current_user;
  }
}
