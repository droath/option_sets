<?php

namespace Drupal\option_sets\Plugin\Field\FieldWidget;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\option_sets\Entity\OptionSetsEntityInterface;
use Drupal\option_sets\OptionSetsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define option sets select widget.
 *
 * @FieldWidget(
 *   id = "option_sets_select",
 *   label = @Translation("Select"),
 *   field_types = {
 *      "option_sets"
 *   }
 * )
 */
class OptionSetsSelectWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * @var OptionSetsManagerInterface
   */
  protected $optionSetsManager;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'multiple_select' => FALSE,
      ] + parent::defaultSettings();
  }

  /**
   * Option set select widget constructor.
   *
   * @param $plugin_id
   * @param $plugin_definition
   * @param FieldDefinitionInterface $field_definition
   * @param array $settings
   * @param array $third_party_settings
   * @param OptionSetsManagerInterface $option_sets_manager
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    OptionSetsManagerInterface $option_sets_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->optionSetsManager = $option_sets_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static (
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('option_sets.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element = [
      '#type' => 'select',
      '#options' => $this->optionSetsOptions(),
      '#multiple' => $this->getSetting('multiple_select'),
      '#default_value' => !$items->isEmpty() ? $items->get($delta)->value : [],
    ] + $element;

    if (isset($element['#required']) && !$element['#required']) {
      $element['#empty_option'] = $this->t('- None -');
    }

    return ['value' => $element];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['multiple_select'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Multiple Select'),
      '#description' => $this->t('Allow the user to select more than one option.'),
      '#default_value' => $this->getSetting('multiple_select'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $values = parent::massageFormValues($values, $form, $form_state);

    // We need to ensure that all values are casted to an array.
    foreach ($values as &$value) {
      $value = &$value['value'];

      if (!is_array($value)) {
        $value = (array) $value;
      }
    }

    return $values;
  }

  /**
   * Option sets options.
   *
   * @return array
   */
  protected function optionSetsOptions() {
    $entity = $this->optionSetsEntity();

    if (!isset($entity)) {
      return [];
    }

    return $entity->processedOptions();
  }

  /**
   * Get option sets entity.
   *
   * @return OptionSetsEntityInterface
   */
  protected function optionSetsEntity() {
    $entity_id = $this->getFieldSetting('option_sets');

    return $this->optionSetsManager
      ->loadEntity($entity_id);
  }
}
