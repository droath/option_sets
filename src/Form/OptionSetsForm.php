<?php

namespace Drupal\option_sets\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Define option sets entity form.
 */
class OptionSetsForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['label'] = [
      "#type" => 'textfield',
      '#title' => $this->t('Label'),
      '#description' => $this->t('Input a human-readable label for the option sets'),
      '#default_value' => $entity->label(),
      '#maxlegnth' => 255,
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => [$entity, 'entityExist']
      ],
      '#disabled' => !$entity->isNew(),
    ];
    $options = $entity->options();

    $form['options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Options'),
      "#description" => $this->t('Input each option on a separate line. The 
      following formats are supported: value, or key|value.'),
      '#rows' => 10,
      '#required' => TRUE,
      '#default_value' => $options
    ];
    $form['#original_options'] = $options;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state
      ->cleanValues()
      ->getValues();

    $entity = $this->entity;
    $dependencies = $entity->listFieldDependencies();

    if (isset($values['options']) && !empty($dependencies)) {
      $options = array_map('trim', explode("\n", $values['options']));
      $original_options = array_map('trim', explode("\n", $form['#original_options']));

      $element = $form['options'];
      foreach (array_diff($original_options, $options) as $changed_value) {
        if (empty($changed_value) || !in_array($changed_value, $original_options)) {
          continue;
        }

        // If the option is defined using key|value then the readable value can
        // be updated, but the key is not able to change. This logic is
        // sensitive to repositioning of option indexes (aka moving lines).
        if (FALSE !== strpos($changed_value, '|')) {
          $index = array_search($changed_value, $original_options);

          if (isset($options[$index])) {
            $key_value = explode('|', $options[$index]);
            $original_key_value = explode('|', $original_options[$index]);

            if ($key_value[0] === $original_key_value[0]) {
              continue;
            }
          }
        }

        // Determine if the a single value was updated to a key|value pair.
        foreach ($options as $option) {
          if (FALSE === strpos($option, $changed_value)) {
            continue;
          }
          $key_value = explode('|', $option);

          if ($key_value[0] === $changed_value) {
            return;
          }
        }

        $form_state->setError($element, $this->t(
          "Existing option keys can't be removed or changed. </br>" .
          " Due to the following dependencies: @depends referencing this option set.", [
          '@label' => $element['#title'],
          '@depends' => implode(',', array_values($dependencies))
        ]));
      }
    }

    $form_state->setValue('options', trim($values['options']));
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();
    $operation = SAVED_NEW == $status ? 'added' : 'updated';

    drupal_set_message($this->t(
      'The option sets was @op.', ['@op' => $operation]
    ));

    $form_state->setRedirectUrl($entity->toUrl('collection'));
  }
}
