<?php

namespace Drupal\option_sets\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Define option sets entity.
 */
interface OptionSetsEntityInterface extends ConfigEntityInterface {

  /**
   * Get option sets options as a string.
   *
   * @return string
   */
  public function options();

  /**
   * Option sets processed options.
   *
   * @return array
   *   An array of option sets processed options.
   */
  public function processedOptions();

  /**
   * Determine if field dependencies exist.
   *
   * @return bool
   */
  public function hasFieldDependency();

  /**
   * List field dependencies.
   *
   * @param bool $has_data_check
   *   Check that field data already exist.
   *
   * @return array
   *   An array of field dependencies, keyed by field id.
   */
  public function listFieldDependencies($has_data_check = TRUE);

}
