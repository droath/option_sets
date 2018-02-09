<?php

namespace Drupal\option_sets\Plugin\PatternModifierType;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\option_sets\Entity\OptionSetsEntity;
use Drupal\option_sets\Entity\OptionSetsEntityInterface;
use Drupal\option_sets\OptionSetsManagerInterface;
use Drupal\pattern_library\Annotation\PatternModifierType;
use Drupal\pattern_library\Plugin\PatternModifierTypeBase;
use Drupal\pattern_library\Plugin\PatternModifierTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define option sets pattern library modifier.
 *
 * @PatternModifierType(
 *   id = "option_sets"
 * )
 */
class OptionSets extends PatternModifierTypeBase implements PatternModifierTypeInterface, ContainerFactoryPluginInterface {

  /**
   * @var OptionSetsManagerInterface
   */
  protected $optionSetsManager;

  /**
   * Option sets modifier construct.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    OptionSetsManagerInterface $option_sets_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->optionSetsManager = $option_sets_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition) {

    return new static (
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('option_sets.manager')
    );
  }

  /**
   * Pattern modifier render output.
   *
   * @return array
   *   A render array.
   */
  public function render() {
    return [
      '#type' => 'select',
      '#options' => $this->getOptionSetsOptions(),
      '#empty_option' => $this->t('- Default -'),
    ] + parent::render();
  }

  /**
   * Get option sets processed options.
   *
   * @return array
   */
  protected function getOptionSetsOptions() {
    $config = $this->getConfiguration();

    if (!isset($config['option_sets_id'])) {
      return [];
    }

    /** @var OptionSetsEntity $option_sets */
    $option_sets = $this
      ->optionSetsManager
      ->loadEntity($config['option_sets_id']);

    if (!$option_sets instanceof OptionSetsEntityInterface) {
      return [];
    }

    return $option_sets->processedOptions();
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultConfiguration() {
    return [
      'description' => 'Select the value to use for the modifier.'
    ];
  }
}
