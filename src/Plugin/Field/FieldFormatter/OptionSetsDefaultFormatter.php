<?php

namespace Drupal\option_sets\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Define option sets default formatter.
 *
 * @FieldFormatter(
 *   id = "option_sets_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "option_sets"
 *   }
 * )
 */
class OptionSetsDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#plain_text' => implode(',', $item->value),
      ];
    }

    return $elements;
  }
}
