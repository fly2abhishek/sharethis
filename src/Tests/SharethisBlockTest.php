<?php

/**
 * @file
 * Contains \Drupal\sharethis\Tests\SharethisBlockTest.
 */

namespace Drupal\sharethis\Tests;

use Drupal\simpletest\WebTestBase;
/**
 * Tests if the sharethis block is available.
 *
 * @group sharethis
 */
class SharethisBlockTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('system_test', 'node', 'block' , 'user', 'sharethis' , 'menu_ui');

  protected function setUp() {
    parent::setUp();

    // Create and login user.
    $admin_user = $this->drupalCreateUser(array('administer blocks', 'administer site configuration', 'access administration pages'));
    $this->drupalLogin($admin_user);
  }

  /**
   * Test that the sharethis form block can be placed and works.
   */
  public function testSharethisBlock() {

    // Test availability of the sharethis block in the admin "Place blocks" list.
    $this->drupalGet('admin/structure/block');
    $this->clickLinkPartialName('Place block');
    $settings = [
      'theme' => 'bartik',
      'region' => 'header',
    ];

    $block = $this->drupalPlaceBlock('sharethis_block', $settings);
  }
}
