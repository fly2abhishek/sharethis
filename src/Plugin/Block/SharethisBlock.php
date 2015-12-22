<?php

/**
 * @file
 * Contains \Drupal\sharethis\Plugin\Block\AutologoutWarningBlock.
 */

namespace Drupal\sharethis\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\Config;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Sharethis Logout info' block.
 *
 * @Block(
 *   id = "sharethis_block",
 *   admin_label = @Translation("Sharethis"),
 * )
 */
class SharethisBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The module manager service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The config object for 'sharethis.settings'.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $autoLogoutSettings;

  /**
   * Constructs an SharethisBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module manager service.
   * @param \Drupal\Core\Config\Config $sharethis_settings
   *   The config object for 'sharethis.settings'.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Config $sharethis_settings) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->sharethisSettings = $sharethis_settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')->get('sharethis.settings')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $sharethis_manager = \Drupal::service('sharethis.manager');
    $markup = $sharethis_manager->blockContents();
    return [
      '#theme' => 'sharethis_block',
      '#content' => $markup,
      '#attached' => array(
        'library' => array(
          'sharethis/drupal.sharethispickerexternalbuttonsws',
          'sharethis/drupal.sharethispickerexternalbuttons',
        ),
      ),
    ];

  }

}
