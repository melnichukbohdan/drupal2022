<?php

namespace Drupal\csv_import\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure CSV Import settings.
 */
class CSVImportSettingsForm extends ConfigFormBase {

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
   */
  public function __construct(ConfigFactoryInterface $config_factory, DateFormatterInterface $date_formatter, EntityTypeManagerInterface $entity_type_manager, FileUsageInterface $file_usage) {
    parent::__construct($config_factory);
    $this->dateFormatter = $date_formatter;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUsage = $file_usage;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('entity_type.manager'),
      $container->get('file.usage')
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
      '#default_value' => $config->get('fid') ? [$config->get('fid')] : NULL,
      '#upload_validators' => [
        'file_validate_extensions' =>['csv'],
      ],
      '#required' => TRUE,
    ];

    // if file downloaded show form element for import the file.
    if (!empty($config->get('fid'))) {
      $file = $this->entityTypeManager->getStorage('file')->load($config->get('fid')); //File::load($config->get('fid'));
      $created = $this->dateFormatter->format($file->getCreatedTime(), 'medium');

      $form['file_information'] = [
        '#markup' => $this->t('This file was uploaded at @created.', ['@created' => $created]),
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
    $config = $this->config('csv_import.settings');

    // Saves file ID for the module config.
    $fid_old = $config->get('fid');
    $fid_form = array_shift($form_state->getValue('file'));

    // Checks was file downloaded early and has different ID.
    if (empty($fid_old) || $fid_old != $fid_form) {
      if (!empty($fid_old)) {
        $previous_file = $this->entityTypeManager->getStorage('file')->load($fid_old);
        $this->fileUsage->delete($previous_file, 'csv_import', 'config_form', $previous_file->id());

      }

      $new_file = $this->entityTypeManager->getStorage('file')->load($fid_form);
      $new_file->save();
      $this->fileUsage->add($new_file, 'csv_import', 'config_form', $new_file->id());
      $time = new DrupalDateTime('', 'UTC');

      $config->set('fid', $fid_form)
        ->set('creation', $time->getTimestamp());
    }

    $config->set('file', array_shift($form_state->getValue('file')))
      ->set('skip_first_line', $form_state->getValue('skip_first_line'))
      ->set('delimiter', $form_state->getValue('delimiter'))
      ->save();

  }

}
