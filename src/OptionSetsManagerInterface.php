<?php

namespace Drupal\option_sets;

use Drupal\option_sets\Entity\OptionSetsEntityInterface;

interface OptionSetsManagerInterface {

  /**
   * Get option setts as options.
   *
   * @return array
   *   An array of option sets as an option array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getOptions();

  /**
   * Load option sets by entity ID.
   *
   * @param $entity_id
   *   The configuration entity id.
   *
   * @return OptionSetsEntityInterface|null
   */
  public function loadEntity($entity_id);

}
