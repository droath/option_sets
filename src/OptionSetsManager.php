<?php

namespace Drupal\option_sets;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\option_sets\Entity\OptionSetsEntityInterface;

/**
 * Define the option sets manager.
 */
class OptionSetsManager implements OptionSetsManagerInterface {

  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Option sets manager constructor.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function loadEntity($entity_id) {
    if (!isset($entity_id)) {
      return NULL;
    }

    return $this->optionSetsStorage()->load($entity_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = [];

    foreach ($this->optionSetsStorage()->loadMultiple() as $entity_id => $entity) {
      if (!$entity instanceof OptionSetsEntityInterface) {
        continue;
      }

      $options[$entity_id] = $entity->label();
    }

    return $options;
  }

  /**
   * Get option sets storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected function optionSetsStorage() {
    return $this
      ->entityTypeManager
      ->getStorage('option_sets');
  }
}
