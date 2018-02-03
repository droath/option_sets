<?php

namespace Drupal\option_sets\Entity;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Annotation\ConfigEntityType;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldStorageConfigInterface;

/**
 * Define option sets configuration entity.
 *
 * @ConfigEntityType(
 *   id = "option_sets",
 *   label = @Translation("Option sets"),
 *   config_prefix = "option_sets",
 *   admin_permission = "administer option sets configuration",
 *   handlers = {
 *     "form" = {
 *       "add" = "\Drupal\option_sets\Form\OptionSetsForm",
 *       "edit" = "\Drupal\option_sets\Form\OptionSetsForm",
 *       "delete" = "\Drupal\option_sets\Form\OptionSetsDeleteForm",
 *     },
 *     "list_builder" = "\Drupal\option_sets\Controller\OptionSetsListBuilder",
 *     "route_provider" = {
 *       "html" = "\Drupal\option_sets\Entity\Routing\OptionSetsRouteProvider"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "collection" = "/admin/structure/option-sets",
 *     "add-form" = "/admin/structure/option-sets/add",
 *     "edit-form" = "/admin/structure/option-sets/{option_sets}",
 *     "delete-form" = "/admin/structure/option-sets/{option_sets}/delete"
 *   }
 * )
 */
class OptionSetsEntity extends ConfigEntityBase implements OptionSetsEntityInterface {

  /**
   * Option sets identifier.
   *
   * @var string
   */
  public $id;

  /**
   * Option sets label.
   *
   * @var string
   */
  public $label;

  /**
   * Option sets options.
   *
   * @var array
   */
  public $options = [];

  /**
   * {@inheritdoc}
   */
  public function options() {
    return $this->get('options') ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function processedOptions() {
    $options = [];
    $options_string = Xss::filterAdmin($this->options());

    foreach (explode("\n", $options_string) as $option) {
      $option = array_map('trim', explode('|', $option));

      if (count($option) > 1) {
        $options[$option[0]] = $option[1];
      }
      else {
        $options[strtolower($option[0])] = $option[0];
      }
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function hasFieldDependency() {
    return !empty($this->listFieldDependencies());
  }

  /**
   * {@inheritdoc}
   */
  public function listFieldDependencies($has_data_check = TRUE) {
    $dependencies = [];

    foreach ($this->listEntityDependencies() as $field_id => $field) {
      if ($has_data_check && !$field->hasData()) {
        continue;
      }
      $dependencies[$field_id] = $field->getName();
    }

    return $dependencies;
  }

  /**
   * List entity dependencies.
   *
   * @return array
   *   An array of FieldConfigStorage objects, keyed by field entity id.
   */
  protected function listEntityDependencies() {
    $dependencies = [];

    foreach ($this->getFieldDependencies() as $id => $field_info) {
      if (!$field_info instanceof FieldStorageConfigInterface) {
        continue;
      }
      $selected_id = $field_info->getSetting('option_sets');

      if ($selected_id !== $this->id()) {
        continue;
      }

      $dependencies[$id] = $field_info;
    }

    return $dependencies;
  }

  /**
   * Get field dependencies.
   *
   * @return array
   */
  protected function getFieldDependencies() {
    $dependencies = [];

    foreach ($this->getOptionSetsFieldMap() as $entity_type_id => $fields) {
      foreach (array_keys($fields) as $field_name) {
        $dependencies["{$entity_type_id}.{$field_name}"] = FieldStorageConfig::loadByName(
          $entity_type_id, $field_name
        );
      }
    }

    return $dependencies;
  }

  /**
   * Get option sets field map.
   *
   * @return array
   */
  protected function getOptionSetsFieldMap() {
    return $this
      ->entityFieldManager()
      ->getFieldMapByFieldType('option_sets');
  }

  /**
   * Determine if an entity exist.
   *
   * @param $id
   *   An entity identifier.
   *
   * @return bool
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function entityExist($id) {
    return (bool) $this->getQuery()
      ->condition('id', $id)
      ->execute();
  }

  /**
   * Get entity query.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected function getQuery() {
    return $this->getStorage()->getQuery();
  }

  /**
   * Get entity storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected function getStorage() {
    return $this
      ->entityTypeManager()
      ->getStorage($this->getEntityTypeId());
  }

  /**
   * Entity field manager.
   *
   * @return EntityFieldManagerInterface
   */
  protected function entityFieldManager() {
    return \Drupal::service('entity_field.manager');
  }
}
