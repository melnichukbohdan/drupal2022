<?php

namespace Drupal\smile_entity\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\smile_entity\SmileEntityInterface;

/**
 * Defines the smile entity entity class.
 *
 * @ContentEntityType(
 *   id = "smile_entity",
 *   label = @Translation("Smile entity"),
 *   label_collection = @Translation("Smile entities"),
 *   label_singular = @Translation("smile entity"),
 *   label_plural = @Translation("smile entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count smile entities",
 *     plural = "@count smile entities",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\smile_entity\SmileEntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\smile_entity\SmileEntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\smile_entity\Form\SmileEntityForm",
 *       "edit" = "Drupal\smile_entity\Form\SmileEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "smile_entity",
 *   admin_permission = "administer smile entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "title" = "title",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/smile",
 *     "add-form" = "/smile/add",
 *     "canonical" = "/smile/{smile_entity}",
 *     "edit-form" = "/smile/{smile_entity}/edit",
 *     "delete-form" = "/smile/{smile_entity}/delete",
 *   },
 *   field_ui_base_route = "entity.smile_entity.settings",
 * )
 */
class SmileEntity extends ContentEntityBase implements SmileEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['body'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Body'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 1500)
      ->setDisplayOptions('form', [
        'type' => 'text_area',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_area',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the smile entity was last edited.'));

    return $fields;
  }

}
