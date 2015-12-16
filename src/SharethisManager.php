<?php
/**
 * @file
 * Contains \Drupal\sharethis\SharethisManager.
 */

namespace Drupal\sharethis;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\Entity\NodeType;

/**
 * Defines an SharethisManager service.
 */
class SharethisManager implements SharethisManagerInterface {

  /**
   * The config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs an SharethisManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The Configuration Factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $sharethisConfig = $this->configFactory->get('sharethis.settings');

    $default_sharethis_nodetypes = array('article' => 'article', 'page' => 'page');
    $view_modes = array();
    foreach (array_keys(NodeType::loadMultiple()) as $type) {
      //$view_modes[$type] = Drupal::config('sharethis.settings')->get('sharethis_' . $type . '_options');
      $view_modes[$type] = array("article"=>"article", "page"=>"page");
    }

    return [
      'buttons' => $sharethisConfig->get('button_option', 'stbc_button'),
      'publisherID' => $sharethisConfig->get('publisherID'),
      'services' => $sharethisConfig->get('service_option'),
      'option_extras' => $sharethisConfig->get('option_extras'),
      'widget' => $sharethisConfig->get('widget_option'),
      'onhover' => $sharethisConfig->get('option_onhover'),
      'neworzero' => $sharethisConfig->get('option_neworzero'),
      'twitter_suffix' => $sharethisConfig->get('twitter_suffix'),
      'twitter_handle' => $sharethisConfig->get('twitter_handle'),
      'twitter_recommends' => $sharethisConfig->get('twitter_recommends'),
      'late_load' => $sharethisConfig->get('late_load'),
      'view_modes' => $view_modes,
      'sharethis_cns' => $sharethisConfig->get('cns'),
      'sharethis_callesi' => (NULL == $sharethisConfig->get('sharethis_cns')) ? 1 : 0,
      'node_types' => $sharethisConfig->get('node_types'),
      'shorten' => $sharethisConfig->get('option_shorten'),
    ];
  }
}
