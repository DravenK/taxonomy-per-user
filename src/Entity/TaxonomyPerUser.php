<?php

namespace Drupal\taxonomy_per_user\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\taxonomy_per_user\TaxonomyPerUserInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the TaxonomyPerUser entity.
 *
 * @ingroup taxonomy_per_user
 * *
 * @ContentEntityType(
 *   id = "taxonomy_per_user",
 *   label = @Translation("Taxonomy Per User entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\taxonomy_per_user\Entity\Controller\TaxonomyPerUserListBuilder",
 *     "form" = {
 *       "add" = "Drupal\taxonomy_per_user\Form\TaxonomyPerUserForm",
 *       "edit" = "Drupal\taxonomy_per_user\Form\TaxonomyPerUserForm",
 *       "delete" = "Drupal\taxonomy_per_user\Form\ContactDeleteForm",
 *     },
 *     "access" = "Drupal\taxonomy_per_user\TaxonomyPerUserAccessControlHandler",
 *   },
 *   list_cache_contexts = { "user" },
 *   base_table = "taxonomy_per_user",
 *   admin_permission = "administer taxonomy_per_user entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "vid" = "vid"
 *   },
 *   links = {
 *     "canonical" = "/taxonomy_per_user/{taxonomy_per_user}",
 *     "edit-form" = "/taxonomy_per_user/{taxonomy_per_user}/edit",
 *     "delete-form" = "/tpu/{taxonomy_per_user}/delete",
 *     "collection" = "/taxonomy_per_user/list"
 *   },
 *   field_ui_base_route = "taxonomy_per_user.taxonomy_per_user_settings",
 * )
 *
 */
class TaxonomyPerUser extends ContentEntityBase implements TaxonomyPerUserInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the TaxonomyPerUser entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the TaxonomyPerUser entity.'))
      ->setReadOnly(TRUE);

    // Owner field of the tpu.
    // Entity reference field, holds the reference to the user object.
    // The view shows the user name field of the user.
    // The form presents a auto complete field for the user name.
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User Name'))
      ->setDescription(t('The Name of the associated user.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => -3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['vid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Vocabulary'))
      ->setDescription(t('The vocabulary to which the Taxonomy Per User is assigned.'))
      ->setSetting('target_type', 'taxonomy_vocabulary')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => -3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => -3,
      ]);


    // Role field for the tpu.
    // The values shown in options are 'administrator' and 'user'.
    $fields['role'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Role'))
      ->setDescription(t('The role of the TaxonomyPerUser entity.'))
      ->setSettings([
        'allowed_values' => [
          'administrator' => 'administrator',
          'user' => 'user',
        ],
      ])
      // Set the default value of this field to 'user'.
      ->setDefaultValue('user')
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

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of ContentEntityExample entity.'));
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
