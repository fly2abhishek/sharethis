<?php

/**
 * @file
 * Definition of Drupal\sharethis\SharethisManagerInterface.
 */

namespace Drupal\sharethis;

/**
 * Interface for SharethisManager.
 */
interface SharethisManagerInterface {

  /**
   * Determine if connection should be refreshed.
   *
   * @return []
   *   Returns the list of options that sharethis provides.
   */
  public function getOptions();

  /**
   * Custom html block.
   *
   * @return string
   */
  public function blockContents();

  /**
   * Include st js scripts.
   */
  public function sharethis_include_js();


  /**
   * Get_stLight_options() function is creating options to be passed to stLight.options
   * $data_options array is the settings selected by publisher in admin panel.
   */
  public function get_stLight_options($data_options);


  /**
   * Converts given value to boolean.
   *
   * @param val
   *   Which value to convert to boolean
   *
   * @todo To be replaced with bool
   */
  function to_boolean($val);

}
