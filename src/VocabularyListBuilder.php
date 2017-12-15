<?php

namespace Drupal\taxonomy_per_user;

use Drupal\taxonomy\VocabularyListBuilder as VocabularyListBuilderBase;

/**
 * Class VocabularyListBuilder.
 *
 * @package Drupal\gvocab
 */
class VocabularyListBuilder extends VocabularyListBuilderBase {

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]
   */
  public function load() {
    $entities = parent::load();
    // Remove vocabularies the current user doesn't have any access for.
    foreach ($entities as $id => $entity) {
      if (!TaxonomyPerUserAccessCheck::checkCreatorAccess('list terms', $id)) {
        unset($entities[$id]);
      }
    }

    return $entities;
  }

}
