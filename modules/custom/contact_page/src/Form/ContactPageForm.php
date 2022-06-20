<?php

namespace Drupal\contact_page\Form;

use Complex\Exception;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Mail\MailManagerInterface;

/**
 * Provides a Contack page form.
 */
class ContactPageForm extends FormBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * @var EntityTypeManagerInterface $entityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var Drupal\Core\Database\Connection $database
   */
  protected $connection;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * @param AccountInterface $account
   * @param EntityTypeManagerInterface $entityTypeManager
   * @param Connection $connection
   * @param MailManagerInterface $mail_manager
   */

  public function __construct(AccountInterface $account,
                              EntityTypeManagerInterface $entityTypeManager,
                              Connection $connection,
                              MailManagerInterface $mail_manager) {
    $this->account = $account;
    $this->entityTypeManager = $entityTypeManager;
    $this->connection = $connection;
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('database'),
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contack_page-form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    if ($this->getAccount()->isAuthenticated()) {
      $form['uid'] = [
        '#type' => 'hidden',
        '#default_value' => $this->getAccount()->id()
      ];
    }

    if ($this->getAccount()->isAuthenticated()) {
      $form['email'] = [
        '#type' => 'hidden',
        '#default_value' => $this->getAccount()->getEmail(),
        '#required' => TRUE
      ];
    }else {
      $form['email'] = [
        '#type' => 'textfield',
        '#title' => 'Email',
        '#required' => TRUE
      ];
    }

    $form['phone_number'] = [
      '#type' => 'textfield',
      '#title' => 'Phone number',
      '#placeholder' => '380....',
      '#required' => TRUE
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => 'Message',
      '#description' => 'min length 10, max length 300',
      '#required' => TRUE
    ];

    $categorys = $this->getEntityTypeManager()->getStorage('taxonomy_term')->loadTree('news');
    foreach ($categorys as $term) {
      $category[$term->tid] = $term->name;
    }

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => $category,
      '#empty_option' => '-select-',
      '#required' => TRUE
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (!$form_state->getValue('email') || !filter_var($form_state->getValue('email'),
        FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', $this->t('Enter valid e-mail'));
    }

    if (mb_strlen($form_state->getValue('phone_number')) != 12  ) {
      $form_state->setErrorByName('message',
        $this->t('Enter valid phone number'));
    }

    if (mb_strlen($form_state->getValue('message')) < 10 ||
      mb_strlen($form_state->getValue('message')) > 300 ) {
      $form_state->setErrorByName('message',
        $this->t('Message should be at least 10 characters and not more 300 characters'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = [
      'uid' => $form_state->getValue('uid'),
      'email' => $form_state->getValue('email'),
      'phone_number' => $form_state->getValue('phone_number'),
      'message' => $form_state->getValue('message'),
      'category' => $form_state->getValue('category'),
    ];

    try {
      $this->getConnection()->insert('contact_page')
        ->fields($data)
        ->execute();
    } catch (Exception $e) {
      return $e->getMessage();
    }

    $this->messenger()->addStatus($this->t('The data has been saved'));
    $this->mailSender($form_state);

  }

  public function mailSender (FormStateInterface $form_state) {
    $module = 'contact_page';
    $key = 'contact_page_send';
    $to = $this->config('system.site')->get('mail');
    $reply = $this->getAccount()->getEmail();
    $params['from'] = $form_state->getValue('email');
    $params['phone_number'] = $form_state->getValue('phone_number');
    $params['message'] = $form_state->getValue('message');
    $params['category'] = $this->getEntityTypeManager()->getStorage('taxonomy_term')
      ->load($form_state->getValue('category'))->label();
    $langcode = $this->getAccount()->getPreferredLangcode();
    $send = true;

    $this->getMailManager()->mail($module, $key, $to, $langcode, $params, $reply, $send);
  }


  /**
   * @return Drupal\Core\Database\Connection
   */
  public function getConnection(): Connection {
    return $this->connection;
  }

  /**
   * @return AccountInterface
   */
  public function getAccount(): AccountInterface {
    return $this->account;
  }

  /**
   * @return EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface {
    return $this->entityTypeManager;
  }

  /**
   * @return \Drupal\Core\Mail\MailManagerInterface
   */
  public function getMailManager(): MailManagerInterface {
    return $this->mailManager;
  }

}
