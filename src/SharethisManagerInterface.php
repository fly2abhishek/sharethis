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
   * @return array
   */
  public function blockContents();

  /**
   * Custom html markup for widget.
   *
   * @param $array
   *
   * @return array
   */
  public function widgetContents($array);

  /**
   * Include st js scripts.
   */
  public function sharethisIncludeJs();


  /**
   * function is creating options to be passed to stLight.
   *
   * @param array $data_options
   *   The settings selected by publisher in admin panel.
   *
   */
  public function getShareThisLightOptions($data_options);


  /**
   * Converts given value to boolean.
   *
   * @param $val
   *   Which value to convert to boolean
   *
   * @return bool
   *
   * @todo To be replaced with bool
   */
  public function toBoolean($val);

  /**
   * Custom html block.
   *
   * @param array $array
   * @param string $title
   * @param string $string
   *
   * @return array
   */
  public function renderSpans($array, $title, $string);

}
