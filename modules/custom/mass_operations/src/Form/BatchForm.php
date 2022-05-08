<?php

namespace Drupal\mass_operations\Form;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;



class BatchForm extends FormBase {

  /**
   * @var LoggerChannelInterface
   */
  protected $loger;

  /**
   * Batch Builder.
   *
   * @var \Drupal\Core\Batch\BatchBuilder
   */
  protected $batchBuilder;

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * BatchForm constructor.
   */
  public function __construct(LoggerChannelInterface $loger,
                              EntityTypeManagerInterface $entityTypeManager
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->loger = $loger;
    $this->batchBuilder = new BatchBuilder();

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory')->get('noticeDBLog'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'batch';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['help'] = [
      '#markup' => $this->t(
        'This form process all pending items - logs into DB logs and informs administrators about all new nodes.'),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['run'] = [
      '#type' => 'submit',
      '#value' => $this->t('Run batch'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->batchBuilder
      ->setTitle($this->t('Processing'))
      ->setInitMessage($this->t('Initializing.'))
      ->setProgressMessage($this->t('Completed @current of @total.'))
      ->setErrorMessage($this->t('An error has occurred.'));

    $this->batchBuilder->addOperation([$this, 'processItems'], []);
    $this->batchBuilder->setFinishCallback([$this, 'finished']);

    batch_set($this->batchBuilder->toArray());
  }

  /**
   * Processor for batch operations.
   */
  public function processItems(array &$context) {
    //get queue 'noticeDBLog'
    $queue = \Drupal::queue('noticeDBLog');
    // Elements per operation.
    $limit = 10;

    // Set default progress values.
    if (empty($context['sandbox']['progress'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['max'] = $queue->numberOfItems();
    }

    // Save items to array which will be changed during processing.
    if (empty($context['sandbox']['items'])) {
      $context['sandbox']['items'] = $queue;
    }

    $counter = 0;

    if (!empty($context['sandbox']['items'])) {

      for ($i = 0; $i < $limit; $i++) {

        if ($counter != $limit) {
          $item = $queue->claimItem();
          $this->processItem($item->data);
          $queue->deleteItem($item);

          $counter++;
          $context['sandbox']['progress']++;

          $context['message'] = $this->t('Now processing node :progress of :count.', [
            ':progress' => $context['sandbox']['progress'],
            ':count' => $context['sandbox']['max'],
          ]);

          // Increment total processed item values. Will be used in finished
          // callback.
          $context['results']['processed'] = $context['sandbox']['progress'];
        }
      }
    }

    // If not finished all tasks, we count percentage of process. 1 = 100%.
    if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
      $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {

    if (!empty($data)) {
      // get all users with role 'administrator'
      $users = $this->entityTypeManager->getStorage('user')
        ->getQuery()
        ->condition('status', '1')
        ->condition('roles', 'administrator')
        ->execute();

      foreach ($users as $uid) {
        // get user name
        $user = User::load($uid);
        $name = $user->getAccountName();

        // generate notice for dblog
          $this->loger->notice('User @username should be notified about new node ‘@node_title[@node_id]', [
          '@username' => $name,
          '@node_title' => $data['node_title'],
          '@node_id' => $data['node_id']
        ]);
      }
    }
  }

  /**
   * Finished callback for batch.
   */
  public function finished($success, $results, $operations) {
    $message = $this->t('Processing completed successfully. All administrators are notified of all new nodes');

    $this->messenger()->addStatus($message);
  }

}
