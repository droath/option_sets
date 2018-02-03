<?php

namespace Drupal\option_sets\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Define the option sets delete form.
 */
class OptionSetsDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name', [
      '%name' => $this->entity->label()
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->entity->toUrl('collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $element = $form[$this->getFormName()];

    $message = $this->t(
      'Please remove these dependencies: @depends prior to deleting the @label option sets.', [
        '@label' => $entity->label(),
        '@depends' => implode(',', $entity->listFieldDependencies())
    ]);
    $form_state->setError($element, $message);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Option sets @label has been deleted.', [
        '@label' => $this->entity->label()
      ]));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
