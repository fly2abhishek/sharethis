<?php

/**
 * @file
 */

namespace Drupal\sharethis\Tests;
use Drupal\simpletest\WebTestBase;


/**
 * Tests the SystemConfigFormTestBase class.
 *
 * @group Sharethis
 */
class SharethisConfigFormTest extends WebTestBase {


  protected $adminUser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('node', 'system_test', 'user', 'sharethis');
  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    // Create and log in admin user.
    $this->adminUser = $this->drupalCreateUser(['administer sharethis']);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests the SharethisConfigForm.
   */
  function testSharethisConfigForm() {
  }

}
