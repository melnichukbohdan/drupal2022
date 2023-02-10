<?php

namespace Drupal\csv_import\Form;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\csv_import\CSVImportBatch;
use Drupal\file\FileUsage\FileUsageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure CSV Import settings.
 */
class CSVImportSettingsForm extends ConfigFormBase {

  /**
   * The data formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The used file service.
   *
   * @var \Drupal\file\FileUsage\FileUsageInterface
   */
  protected $fileUsage;

  /**
   * The CSVImport batch service.
   *
   * @var \Drupal\csv_import\CSVImportBatch
   */
  protected $batch;

  /**
   * The Constructor for CSV Import Settings Form.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\file\FileUsage\FileUsageInterface $file_usage
   *  The used file service.
   * @param
   */
  public function __construct(ConfigFactoryInterface $config_factory, DateFormatterInterface $date_formatter, EntityTypeManagerInterface $entity_type_manager, FileUsageInterface $file_usage, CSVImportBatch $batch) {
    parent::__construct($config_factory);
    $this->dateFormatter = $date_formatter;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUsage = $file_usage;
    $this->batch = $batch;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('entity_type.manager'),
      $container->get('file.usage'),
      $container->get('csv_import.batch')
    );

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csv_import_settings';

  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['csv_import.settings'];

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('csv_import.settings');

    $form['file'] = [
      '#title' => $this->t('CSV file'),
      '#type' => 'managed_file',
      '#upload_location' => 'public://',
      '#default_value' => $config->get('file') ? [$config->get('file')] : NULL,
      '#upload_validators' => [
        'file_validate_extensions' =>['csv'],
      ],
      '#required' => TRUE,
    ];

    // if file downloaded show form element for import the file.
    if (!empty($config->get('file'))) {
      $file = $this->entityTypeManager->getStorage('file')->load($config->get('file'));
      $created = $this->dateFormatter->format($file->getCreatedTime(), 'medium');

      $form['file_information'] = [
        '#markup' => $this->t('This file was uploaded at @created.', ['@created' => $created]),
      ];

    }

    // Adds button for start importing file. The button have its own submit handler.
     if ($config->get('file')) {
       $form['actions']['start_import'] = [
         '#type' => 'submit',
         '#value' => $this->t('Start import'),
         '#submit' => ['::startImport'],
         '#weight' => 100,
       ];
     }

    $form['additional_settings'] = [
      '#type' => 'fieldset',
      '#title' => t('Additional settings'),
    ];

    $form['additional_settings']['skip_first_line'] = [
      '#type' => 'checkbox',
      '#title' => t('Skip first line'),
      '#default_value' => $config->get('skip_first_line') ?? FALSE,
      '#description' => t('If file contain titles, this checkbox help to skip first line.'),
    ];

    $form['additional_settings']['delimiter'] = [
      '#type' => 'select',
      '#title' => $this->t('Select delimiter'),
      '#options' => [
        ',' => ',',
        '~' => '~',
        ';' => ';',
        ':' => ':',
      ],
      '#default_value' => $config->get('delimiter') ?? ',',
      '#required' => TRUE,
    ];

      return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->saveHandler($form_state);
  }

  /**
   * Starts import data.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function startImport(array &$form, FormStateInterface $form_state) {
    $this->saveHandler($form_state);

    $config = $this->config('csv_import.settings');
    $fileId = $config->get('file');
    $skipFirstLine = $config->get('skip_first_line');
    $delimiter = $config->get('delimiter');
    $this->batch->parseCSV($fileId, $skipFirstLine, $delimiter);

  }

  /**
   * Saves new settings.
   *
   * @param FormStateInterface $form_state
   *   The current state of the form.
   */
  public function saveHandler(FormStateInterface $form_state) {
    $config = $this->config('csv_import.settings');

    // Saves file ID for the module config.
    $fidOld = $config->get('file');
    $fidForm = array_shift($form_state->getValue('file'));

    // Checks was file downloaded early and has different ID.
    if (empty($fidOld) || $fidOld != $fidForm) {
      if (!empty($fidOld)) {
        $previousFile = $this->entityTypeManager->getStorage('file')->load($fidOld);
        $this->fileUsage->delete($previousFile, 'csv_import', 'config_form', $previousFile->id());
        $previousFile->delete();

      }

      $newFile = $this->entityTypeManager->getStorage('file')->load($fidForm);
      $newFile->save();
      $this->fileUsage->add($newFile, 'csv_import', 'config_form', $newFile->id());
      $time = new DrupalDateTime('', 'UTC');

      $config->set('file', $fidForm)
        ->set('creation', $time->getTimestamp());
    }

    $config->set('skip_first_line', $form_state->getValue('skip_first_line'))
      ->set('delimiter', $form_state->getValue('delimiter'))
      ->save();
  }

}
