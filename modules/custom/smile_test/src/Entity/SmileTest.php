<?php

namespace Drupal\smile_test\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\smile_test\SmileTestInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the smile test entity class.
 *
 * @ContentEntityType(
 *   id = "smile_test",
 *   label = @Translation("Smile test"),
 *   label_collection = @Translation("Smile test"),
 *   label_singular = @Translation("smile test"),
 *   label_plural = @Translation("smile tests"),
 *   label_count = @PluralTranslation(
 *     singular = "@count smile tests",
 *     plural = "@count smile tests",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\smile_test\SmileTestListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\smile_test\SmileTestAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\smile_test\Form\SmileTestForm",
 *       "edit" = "Drupal\smile_test\Form\SmileTestForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "smile_test",
 *   admin_permission = "administer smile test",
 *   entity_keys = {
 *     "id" = "id",
 *     "client_name" = "client_name",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/smile-test",
 *     "add-form" = "/smile-test/add",
 *     "canonical" = "/smile-test/{smile_test}",
 *     "edit-form" = "/smile-test/{smile_test}/edit",
 *     "delete-form" = "/smile-test/{smile_test}/delete",
 *   },
 *   field_ui_base_route = "entity.smile_test.settings",
 * )
 */
class SmileTest extends ContentEntityBase implements SmileTestInterface {

  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Client.'))
      ->setReadOnly(TRUE);


    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Client.'))
      ->setReadOnly(TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    //fild client name
    $fields['client_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Client name'))
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

    //fild prefered brand
    $fields['prefered_brand'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Prefered Brand'))
      ->setDescription(t('The prefered brand of Client.'))
      ->setSettings([
        'allowed_values' => [
          'Daiwa' => 'Daiwa',
          'Shimano' => 'Shimano',
        ],
      ])
      // Set the predefined value of this field to 'user'.
      ->setDefaultValue(NULL)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    //fild products owned count
    $fields['products_owned_count'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Products owned count'))
      ->setDescription(t('The count of products of Client.'))
      ->setSettings([
        'unsigned' => TRUE
      ])
      // Set no default value.
      ->setDefaultValue(NULL)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'numeric',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'numeric',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['registration_date'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Registration date'))
      ->setDescription(t('The date of registration of Client.'))
      ->setReadOnly(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of Client.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
