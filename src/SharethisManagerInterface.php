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

}
