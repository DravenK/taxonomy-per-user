<?php

namespace Drupal\taxonomy_per_user;

use Drupal\Core\Access\AccessResult;

abstract class TaxonomyPerUserAccessCheck {

  /**
   * @param null $op
   * @param null $vid
   *
   * @return bool
   */
  public static function checkCreatorAccess($op = NULL, $vid = NULL) {

    // Admin: always.
    if (\Drupal::currentUser()->hasPermission('administer taxonomy')) {
      return TRUE;
    }

    // If this user has permission to view vocabulary.
    $uid = \Drupal::currentUser()->id();
    $tpu = \Drupal::database()->select('taxonomy_per_user','t')
      ->fields('t',['user_id'])
      ->condition('target_id',$vid)
      ->condition('user_id,',$uid)
      ->execute()->fetchField();

    if(!empty($tpu)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * @param $vid
   * @param $operation
   * @param $account
   *
   * @return \Drupal\Core\Access\AccessResult|\Drupal\Core\Access\AccessResultNeutral
   */
  public static function vocabularyTermCheckAccess($operation, $vid, $account) {
    switch ($operation) {
      case 'view terms':
        return AccessResult::allowedIfHasPermissions($account, ["view terms in $vid", 'administer taxonomy'], 'OR');
        break;

      case 'create terms':
        return AccessResult::allowedIfHasPermissions($account, ["create terms in $vid", 'administer taxonomy'], 'OR');
        break;

      case 'edit terms':
        return AccessResult::allowedIfHasPermissions($account, ["edit terms in $vid", 'administer taxonomy'], 'OR');
        break;

      case 'delete terms':
        return AccessResult::allowedIfHasPermissions($account, ["delete terms in $vid", 'administer taxonomy'], 'OR');
        break;

      default:
        // No opinion.
        return AccessResult::neutral();
    }
  }

}
