<?php

namespace Drupal\option_sets\Plugin\Field\FieldType;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\MapDataDefinition;
use Drupal\option_sets\OptionSetsManager;

/**
 * Define option sets field item.
 *
 * @FieldType(
 *   id = "option_sets",
 *   label = @Translation("Option sets"),
 *   description = @Translation("Reuse option sets as field options."),
 *   default_widget = "option_sets_select",
 *   default_formatter = "option_sets_default"
 * )
 */
class OptionSetsItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'option_sets' => NULL,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return empty(array_filter($value));
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = MapDataDefinition::create('map')
        ->setLabel(new TranslatableMarkup('Option sets value'))
        ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ],
      ],
      'indexes' => []
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $settings_form['option_sets'] = [
      '#type' => 'select',
      '#title' => $this->t('Option Sets'),
      '#description' => $this->t('Select the option sets.'),
      '#options' => $this->getOptionSets(),
      '#empty_option' => $this->t(' -Select- '),
      '#disabled' => $has_data,
      '#required' => TRUE,
      '#default_value' => $this->getSetting('option_sets'),
    ];

    return $settings_form;
  }

  /**
   * Get the option set options.
   *
   * @return array
   */
  protected function getOptionSets() {
    return $this->getOptionSetsManager()->getOptions();
  }

  /**
   * Get the option set manager.
   *
   * @return OptionSetsManager
   */
  protected function getOptionSetsManager() {
    return \Drupal::service('option_sets.manager');
  }
}
