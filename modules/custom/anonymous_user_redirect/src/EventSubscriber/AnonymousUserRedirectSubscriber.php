<?php

namespace Drupal\anonymous_user_redirect\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Anonymous user redirect event subscriber.
 */
class AnonymousUserRedirectSubscriber implements EventSubscriberInterface {

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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onKernelRequest'],
    ];
  }

  /**
   * Kernel request event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Response event.
   */
  public function onKernelRequest(RequestEvent $event) {
    //exceptions routes
    $routes = [
      'user.pass',
      'user.register',
      'user.login'
    ];

    if ($this->getCurrentUser()->isAnonymous() &&
      !in_array($this->getCurrentRouteMatch()->getRouteName(), $routes)) {
      $event->setResponse(new RedirectResponse('/user/login', 302));
    }
  }

  /**
   * @return Drupal\Core\Routing\RouteMatchInterface
   */
  public function getCurrentRouteMatch(): Drupal\Core\Routing\RouteMatchInterface|RouteMatchInterface   {
    return $this->current_route_match;
  }

  /**
   * @return AccountInterface
   */
  public function getCurrentUser(): AccountInterface {
    return $this->current_user;
  }
}
