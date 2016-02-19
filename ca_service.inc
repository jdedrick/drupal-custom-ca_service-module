<?php

/**
 * confirm service import access permissions
 */
function _ca_service_access($op, $args) {
  //global $user;

  $access = false;

  if (user_access('ca service import data')) {
    return true;
  } else {
    return false;
  }
}

/**
 * service index callback
 */
function _ca_service_index() {
  return 'testing service index';
}

function _ca_service_import($data) {

#  global $user;
#
#  $node = (object) null;
#  $node->type = 'ca_node';
#
#  $form_state = array();
#  $form_state['values'] = array(
#    'title' => 'test import title',
#    'body' => 'test import body',
#    'name' => $user->name,
#    'op' => t('Save')
#  );
#
#  module_load_include('inc', 'node', 'node.pages');
#  drupal_execute('ca_node_node_form', $form_state, $node);
#
#  $errors = form_get_errors();
#  if(count($errors)) {
#    return print_r($errors);
#  }

  return 'Testing: Here is the data you sent : ' . $data;

}
