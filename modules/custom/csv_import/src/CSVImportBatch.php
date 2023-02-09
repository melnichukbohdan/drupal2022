<?php

namespace Drupal\csv_import;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Queue\Batch;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class CSVImportBatch extends Batch {

  use StringTranslationTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Database\Connection|MessengerInterface
   */
  protected $messenger;

  /**
   * The Constructor for CSV Import Batch service.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MessengerInterface $messenger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->messenger = $messenger;
    $this->batchBuilder = new BatchBuilder();

  }

  /**
   * Gets data into csv file and starting batch.
   *
   * @param integer $fileId
   *   The csv file id.
   * @param boolean $skip_first_line
   *   Skip the first line of the CSV file.
   * @param string $delimiter
   *   Delimiter for exploding data.
   */
  public function parseCSV($fileId, $skip_first_line, $delimiter) {
    $file = $this->entityTypeManager->getStorage('file')->load($fileId);
    $queue = [];
    if (($csv = fopen($file->uri->getString(), 'r')) !== FALSE) {
      if ($skip_first_line) {
        fgetcsv($csv, 0, $delimiter);

      }
      while (($row = fgetcsv($csv, 0, $delimiter)) !== FALSE) {
        $queue[] = $row;

      }

      fclose($csv);

    }

    $this->batchBuilder
      ->setTitle($this->t('Processing'))
      ->setInitMessage($this->t('Initializing.'))
      ->setProgressMessage($this->t('Completed @current of @total.'))
      ->setErrorMessage($this->t('An error has occurred.'));

    $this->batchBuilder->addOperation([$this, 'processItems'], [$queue]);
    $this->batchBuilder->setFinishCallback([$this, 'finished']);
    $this->setBatch($this->batchBuilder->toArray());

  }

  /**
   * Separates the queue with all items into small parts.
   *
   * @param array $queue
   *   All items that will need process.
   * @param array $context
   *   The context.
   */
  public function processItems(array $queue, array &$context) {
    $limit = 10;

    // Set default progress values.
    if (empty($context['sandbox']['progress'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['max'] = count($queue);

    }

    // Save items to array which will be changed during processing.
    if (empty($context['sandbox']['items'])) {
      $context['sandbox']['items'] = $queue;

    }

    $counter = 0;

    if (!empty($context['sandbox']['items'])) {
      for ($i = 0; $i < $limit; $i++) {
        if (empty($context['sandbox']['items'])) {
          break;

        }

        if ($counter != $limit) {
          $item = array_shift($context['sandbox']['items']);
          $this->processItem($item,$context);
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
   * Process one item from the queue.
   *
   * @param array $item
   *   The item.
   * @param array $context
   *   The context.
   */
  public function processItem ($item, &$context) {
    list($id, $name, $body, $category) = $item;
    $category_array = explode(',', $category);

        $category_ids = [];

    foreach ($category_array as $k => $v) {
      $query = \Drupal::entityQuery('taxonomy_term');
      $query->condition('vid', 'category');
      $query->condition('name', $v);
      $query->range(0, 1);
      $result = $query->execute();
      $tid = reset($result);

      // If isset the taxonomy term with the name adds its to array.
      if ($tid) {
        $category_ids[] = $tid;

      }

      // Else creates taxonomy term.
      else {
        $term = $this->entityTypeManager->getStorage('taxonomy_term')->create([
          'name' => $v,
          'vid' => 'category',
        ]);
        $term->save();
        $category_ids[] = $term->id();

      }

    }

    $values = [
      'type'        => 'imported',
      'title'       => $name,
      'langcode'    => 'en',
      'uid' => 1,
      'status' => 1,
      'body' => $body,
      'field_category' => $category_ids,
    ];

    $node = $this->entityTypeManager->getStorage('node')->create($values);

    // Adds the result into array Results batch operation.
    // By these data we will show how many imported data.
    $context['results'][] = $node->id() . ' : ' . $node->label();
    $context['message'] = $node->label();

  }

  /**
   * Sets the batch.
   *
   * @param object $batch
   */
  public function setBatch($batch) {
    batch_set($batch);

  }

  /**
   * {@inheritdoc}
   *
   * Метод который будет вызван по окончанию всех batch операций, или в случае
   * возникновения ошибки в процессе.
   */

  /**
   * Shows message after all batch operations.
   *
   * @param boolean $success
   *   The import status
   * @param array $results
   *   Array wits names success added.
   */
  public function finished($success, $results) {
    if ($success) {
      $message = $this->formatPlural($results['processed'], 'One post processed.', '@count posts processed.');

    }
    else {
      $message = t('Finished with an error.');

    }

    $this->messenger->addMessage($message);

  }

}
