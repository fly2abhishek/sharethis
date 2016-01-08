<?php

/**
 * @file
 * Provide views data for tracker.module.
 */

/**
 * Implements hook_views_data().
 */
function sharethis_views_data() {
  $data = array();
  // ShareThis link.
  $data['node']['sharethis_node'] = array(
    'title' => t('ShareThis Link'),
    'help' => t('Link provided by the ShareThis service.'),
    'field' => array(
      'id' => 'sharethis_node',
      'help' => t('Show Sharethis for a particular node.'),

    ),
  );
  return $data;
}

/**
 * Implements hook_views_data_alter().
 */
// Function tracker_views_data_alter(&$data) {
//  // Provide additional uid_touch handlers which are handled by tracker
//  $data['node_field_data']['uid_touch_tracker'] = array(
//    'group' => t('Tracker - User'),
//    'title' => t('User posted or commented'),
//    'help' => t('Display nodes only if a user posted the node or commented on the node.'),
//    'argument' => array(
//      'field' => 'uid',
//      'name table' => 'users_field_data',
//      'name field' => 'name',
//      'id' => 'tracker_user_uid',
//      'no group by' => TRUE,
//    ),
//    'filter' => array(
//      'field' => 'uid',
//      'name table' => 'users_field_data',
//      'name field' => 'name',
//      'id' => 'tracker_user_uid'
//    ),
//  );
// }.
