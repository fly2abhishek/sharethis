<?php
/**
 * @file
 * Contains \Drupal\sharethis\SharethisManager.
 */

namespace Drupal\sharethis;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\NodeType;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\TitleResolverInterface;

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
   * The config object.
   *
   * @var \Drupal\Core\Controller\TitleResolverInterface
   */
  protected $titleResolver;

  /**
   * Constructs an SharethisManager object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The Configuration Factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TitleResolverInterface $title_resolver) {
    $this->configFactory = $config_factory;
    $this->titleResolver = $title_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $sharethisConfig = $this->configFactory->get('sharethis.settings');

    $default_sharethis_nodetypes = array('article' => 'article', 'page' => 'page');
    $view_modes = array();
    foreach (array_keys(NodeType::loadMultiple()) as $type) {
      $view_modes[$type] = array("article" => "article", "page" => "page");
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


  /**
   * {@inheritdoc}
   */
  function blockContents() {
    $sharethisConfig = $this->configFactory->get('sharethis.settings');
    $config = $this->configFactory->get('system.site');
    if ($sharethisConfig->get('location') == 'block') {
      // First get all of the options for the sharethis widget from the database:
      $data_options = $this->getOptions();
      $current_path = $_GET['q'];
      $path = isset($current_path) ? $current_path : '/node';
      if ($path == $config->get('page.front')) {
        $path = "node";
      }
      $mpath = Url::fromRoute($path, ['absolute' => TRUE]);
      $mpath = ($mpath->getRouteName());
      $request = \Drupal::request();
      $route_match = \Drupal::routeMatch();
      $mTitle = $this->titleResolver->getTitle($request, $route_match->getRouteObject());
      $title = is_object($mTitle) ? $mTitle->getUntranslatedString() : $config->get('name');

      foreach ($data_options['option_extras'] as $service) {
        $data_options['services'] .= ',"' . $service . '"';
      }

      // The share buttons are simply spans of the form class='st_SERVICE_BUTTONTYPE' -- "st" stands for ShareThis.
      $type = Unicode::substr($data_options['buttons'], 4);
      $type = $type == "_" ? "" : Html::escape($type);
      $service_array = explode(",", $data_options['services']);
      $st_spans = "";
      foreach ($service_array as $service_full) {
        // Strip the quotes from the element in the array (They are there for javascript).
        $service = explode(":", $service_full);

        // Service names are expected to be parsed by Name:machine_name. If only one
        // element in the array is given, it's an invalid service.
        if (count($service) < 2) {
          continue;
        }

        // Find the service code name.
        $serviceCodeName = Unicode::substr($service[1], 0, -1);

        // Switch the title on a per-service basis if required.
        // $title = $title;.
        switch ($serviceCodeName) {
          case 'twitter':
            $title = empty($data_options['twitter_suffix']) ? $title : Html::escape($title) . ' ' . Html::escape($data_options['twitter_suffix']);
            break;
        }

        // Sanitize the service code for display.
        $display = Html::escape($serviceCodeName);

        // Put together the span attributes.
        $attributes = array(
          'st_url' => $mpath,
          'st_title' => $title,
          'class' => 'st_' . $display . $type,
        );
        if ($serviceCodeName == 'twitter') {
          if (!empty($data_options['twitter_handle'])) {
            $attributes['st_via'] = $data_options['twitter_handle'];
            $attributes['st_username'] = $data_options['twitter_recommends'];
          }
        }
        // Only show the display text if the type is set.
        if (!empty($type)) {
          $attributes['displayText'] = Html::escape($display);
        }
        $meta_generator = array(
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => $attributes,
        // It's an empty span tag.
          '#value' => '',
        );
        // Render the span tag.
        $st_spans .= drupal_render($meta_generator);
      }
      $this->sharethis_include_js();
      return ['data_options' => $data_options, 'm_path' => $mPath, 'm_title' => $mTitle , 'st_spans' => $st_spans];

    }
  }

  /**
   * {@inheritdoc}
   */
  function sharethis_include_js() {
    $has_run = &drupal_static(__FUNCTION__, FALSE);
    if (!$has_run) {
      // These are the ShareThis scripts:
      $data_options = $this->getOptions();
      $st_js_options = array();
      $st_js_options['switchTo5x'] = $data_options['widget'] == 'st_multi' ? TRUE : FALSE;
      if ($data_options['late_load']) {
        $st_js_options['__st_loadLate'] = TRUE;
      }
      $st_js = "";
      foreach ($st_js_options as $name => $value) {
        $st_js .= 'var ' . $name . ' = ' . Json::decode($value) . ';';

      }
      $stlight = $this->get_stLight_options($data_options);
      $st_js = "if (stLight !== undefined) { stLight.options($stlight); }";
      $has_run = TRUE;
    }
    return $has_run;
  }

  /**
   * {@inheritdoc}
   */
  function get_stLight_options($data_options) {
    // Provide the publisher ID.
    $paramsStLight = array(
      'publisher' => $data_options['publisherID'],
    );
    $paramsStLight['version'] = ($data_options['widget'] == 'st_multi') ? "5x" : "4x";
    if ($data_options['sharethis_callesi'] == 0) {
      $paramsStLight["doNotCopy"] = !$this->to_boolean($data_options['sharethis_cns']['donotcopy']);
      $paramsStLight["hashAddressBar"] = $this->to_boolean($data_options['sharethis_cns']['hashaddress']);
      if (!($paramsStLight["hashAddressBar"]) && $paramsStLight["doNotCopy"]) {
        $paramsStLight["doNotHash"] = TRUE;
      }
      else {
        $paramsStLight["doNotHash"] = FALSE;
      }
    }
    if (isset($data_options['onhover']) && $data_options['onhover'] == FALSE) {
      $paramsStLight['onhover'] = FALSE;
    }
    if ($data_options['neworzero']) {
      $paramsStLight['newOrZero'] = "zero";
    }
    if (!$data_options['shorten']) {
      $paramsStLight['shorten'] = 'false';
    }
    $stlight = Json::encode($paramsStLight);

    return $stlight;
  }

  /**
   * {@inheritdoc}
   */
  function to_boolean($val) {
    if (strtolower(trim($val)) === 'false') {
      return FALSE;
    }
    else {
      return (boolean) $val;
    }
  }

}
