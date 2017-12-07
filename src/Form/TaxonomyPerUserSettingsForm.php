<?php

namespace Drupal\taxonomy_per_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TaxonomyPerUserSettingsForm.
 *
 * @ingroup taxonomy_per_user
 */
class TaxonomyPerUserSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'taxonomy_per_user.settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['taxonomy_per_user.settings']['#markup'] = 'Settings form for Taxonomy Per User. Manage field settings here.';
    return $form;
  }

}
