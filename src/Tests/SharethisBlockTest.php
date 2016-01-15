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
  /**
   * {@inheritdoc}
   */
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
    \Drupal::service('theme_handler')->install(['bartik', 'seven', 'stark']);
    $theme_settings = $this->config('system.theme');
    foreach (['bartik', 'seven', 'stark'] as $theme) {
      $this->drupalGet('admin/structure/block/list/' . $theme);
      $this->assertTitle(t('Block layout') . ' | Drupal');
      // Select the 'Sharethis' block to be placed.
      $block = array();
      $block['id'] = strtolower($this->randomMachineName());
      $block['theme'] = $theme;
      $block['region'] = 'content';
      $this->drupalPostForm('admin/structure/block/add/sharethis_block', $block, t('Save block'));
      $this->assertText(t('The block configuration has been saved.'));
      // Set the default theme and ensure the block is placed.
      $theme_settings->set('default', $theme)->save();
      $this->drupalGet('node');
      // $this->drupalGet('');.
      $result = $this->xpath('//div[@class=:class]', array(':class' => 'sharethis-wrapper'));
      $this->assertEqual(count($result), 1, 'Sharethis links found');
    }
  }

}
