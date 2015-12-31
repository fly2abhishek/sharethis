<?php

/**
 * @file
 * Contains \Drupal\sharethis\Plugin\Block\AutologoutWarningBlock.
 */

namespace Drupal\sharethis\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\sharethis\SharethisManagerInterface;
use Drupal\Core\Url;

/**
 * Provides an 'Share this Widget' block.
 *
 * @Block(
 *   id = "sharethis_widget_block",
 *   admin_label = @Translation("Sharethis Widget"),
 *   category = @Translation("Widgets")
 * )
 */
class SharethisWidgetBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config object for 'sharethis.settings'.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $sharethisSettings;

  /**
   * The Sharethis Manager.
   *
   * @var \Drupal\sharethis\SharethisManager
   */
  protected $sharethisManager;

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Config $sharethis_settings, SharethisManagerInterface $sharethisManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->sharethisSettings = $sharethis_settings;
    $this->sharethisManager = $sharethisManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')->get('sharethis.settings'),
      $container->get('sharethis.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'path' => "global",
      'external_path' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  function blockForm($form, FormStateInterface $form_state) {
    $formValues = $form_state->getUserInput();
    $description = t('Variable - Different per URL');
    $description .= '<br />';
    $description .= t('External - Useful in iframes (Facebook Tabs, etc.)');
    $form['sharethis_path'] = array(
      '#type' => 'select',
      '#title' => t('Path to share'),
      '#options' => array(
        'global' => t('Global'),
        'current' => t('Variable'),
        'external' => t('External URL'),
      ),
      '#description' => $description,
      '#default_value' => $this->configuration['sharethis_path'],
    );

    $form['sharethis_path_external'] = array(
      '#type' => 'url',
      '#title' => t('External URL'),
      '#default_value' => $this->configuration['sharethis_path_external'],
      '#states' => array(
        'visible' => array(
          ':input[name="settings[sharethis_path]"]' => array('value' => 'external'),
        ),
      ),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['sharethis_path'] = $values['sharethis_path'];
    $this->configuration['sharethis_path_external'] = $values['sharethis_path_external'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if ($this->configuration['sharethis_path'] == 'external') {
      $url = $this->configuration['sharethis_path_external'];
    }
    else {
      $current_path = \Drupal::url('<current>');
      $url = ($this->configuration['sharethis_path'] == 'global') ? '<front>' : $current_path ;
    }
    $request = \Drupal::request();
    $route_match = \Drupal::routeMatch();
    $title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
    $mtitle = ($this->configuration['sharethis_path'] == 'current') ? $title : \Drupal::config('system.site')->get('name');
    $markup = $this->sharethisManager->widgetContents(array('m_path' => $url, 'm_title' => $mtitle));
    return [
      '#theme' => 'sharethis_block',
      '#content' => $markup,
      '#attached' => array(
        'library' => array(
          'sharethis/sharethispickerexternalbuttonsws',
          'sharethis/sharethispickerexternalbuttons',
        ),
      ),
    ];
  }
}
