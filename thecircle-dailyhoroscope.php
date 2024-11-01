<?php
/*
Plugin Name: TheCircle: Daily Horoscope
Plugin URI: http://www.thecircle.com
Description: What do the stars have in store for you today? Is it time for a change? The free daily horoscope widget calculates the daily dangers, opportunities, and advice for each selected star sign.
Version: 1.0
Author: TheCircle
Author URI: http://www.thecircle.com
*/

/*  Copyright 2013 The Circle

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Do not load directly
if (!function_exists('is_admin')) {
  header('Status: 403 Forbidden');
  header('HTTP/1.1 403 Forbidden');
  exit();
}

if (!class_exists('thecircleDailyHoroscope')) {
class thecircleDailyHoroscope extends WP_Widget {
  private $jsSimple = 'http://advget.com/cir/loader/dailyhoroscopesimple/loader.js';
  private $jsAdvanced = 'http://advget.com/cir/loader/dailyhoroscope/iconset/%d/color/%s/backgroundcolor/%s/loader.js';
  private $widgetData = array(
    'Name'        => 'thecircleDailyHoroscope',
    'Title'       => 'TheCircle: Daily Horoscope',
    'Description' => 'What do the stars have in store for you today? Is it time for a change? The free daily horoscope widget calculates the daily dangers, opportunities, and advice for each selected star sign.',
  );

  private $widgetFormData = array(
    'Title'           => 'Daily Horoscope from TheCircle',
    'Advanced'        => '',
    'ColorBackground' => '#77349A',
    'ColorText'       => '#ffffff',
    'IconSet'         => 1
  );

  private $widgetIcons = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
  private $widgetIconsName = 'images/set%d.png';

  public function thecircleDailyHoroscope() {
    thecircleDailyHoroscope::__construct();
  }

  public function __construct() {

    $widget_options = array(
      'classname'   => $this->widgetData['Name'],
      'description' => __($this->widgetData['Description'])
    );
    $control_options = array();
    $this->WP_Widget($this->widgetData['Name'], __($this->widgetData['Title']), $widget_options, $control_options);
  }

  /**
   * form
   *
   * @see WP_Widget::form()
   */
public function form($instance) {
  /**
   * form defaults
   *
   * @var array
   */
  $instance = wp_parse_args((array)$instance, $this->widgetData);
  ?>
  <script type="text/javascript">
    jQuery(document).ready(function ($) {
      $('.color-background').wpColorPicker();
      $('.color-text').wpColorPicker();
    });
  </script>
  <?php
  // Simple/advanced toggle
  if ($instance['Advanced'] == 1) {
    $defaultChecked = 'checked="checked"';
  } else {
    $defaultChecked = "";
  }
  echo '<p><input id="' . $this->get_field_id('Advanced') . '" name="' . $this->get_field_name('Advanced') . '"type="checkbox" value="1"' . $defaultChecked . '/> Activate links?</p>';
  if ($instance['Advanced'] != 1) {
    echo '<p style="color:#f00;">Activate links to TheCircle to access the colour settings and alternative versions of the widget.</p>';
    echo '<p style="clear:both;"></p>';
  } else {
    // advanced settings

    // title
    echo '<label for="' . $this->get_field_id('Title') . '">' . __('Title:') . '</label>';
    echo '<p><input id="' . $this->get_field_id('Title') . '" name="' . $this->get_field_name('Title') . '" type="text" value="' . $instance['Title'] . '" /></p>';
    echo '<p style="clear:both;"></p>';

    // iconset
    echo '<label for="' . $this->get_field_id('IconSet') . '">' . __('Iconset:') . '</label>';
    echo '<br style="clear:both;"/>';
    foreach ($this->widgetIcons as $iconSet) {
      $iconSetImage = plugins_url(sprintf($this->widgetIconsName, $iconSet), __FILE__);
      if ($iconSet == $instance['IconSet']) {
        $checked = ' checked="checked"';
      } else {
        $checked = '';
      }
      echo '<div style="width:64px;text-align:center;float:left">
          <img src="' . $iconSetImage . '">
          <input id="' . $this->get_field_id('IconSet') . '" name="' . $this->get_field_name('IconSet') . '" type="radio" value="' . $iconSet . '"' . $checked . '/>
          </div>';
    }
    echo '<p style="clear:both;"></p>';

    // color background
    echo '<label for="' . $this->get_field_id('ColorBackground') . '">' . __('Color background:') . '</label>';
    echo '<p><input class="color-background" id="' . $this->get_field_id('ColorBackground') . '" name="' . $this->get_field_name('ColorBackground') . '" type="text" value="' . $instance['ColorBackground'] . '" /></p>';
    echo '<p style="clear:both;"></p>';

    // color text
    echo '<label for="' . $this->get_field_id('ColorText') . '">' . __('Color Text:') . '</label>';
    echo '<p><input class="color-text" id="' . $this->get_field_id('ColorText') . '" name="' . $this->get_field_name('ColorText') . '" type="text" value="' . $instance['ColorText'] . '" /></p>';
    echo '<p style="clear:both;"></p>';
  }
}

  /**
   * save settings to db
   *
   * @see WP_Widget::update()
   */
  public function update($new_instance, $old_instance) {
    $instance = $old_instance;

    /**
     * defaults
     *
     * @var array
     */
    $new_instance = wp_parse_args((array)$new_instance, $this->widgetFormData);

    foreach ($this->widgetFormData as $key => $keyData) {
      $instance[$key] = (string)strip_tags($new_instance[$key]);
    }
    return $instance;
  }

  /**
   * Widget frontend
   *
   * @see WP_Widget::widget()
   */
  public function widget($args, $instance) {
    extract($args);

    echo $before_widget;

    $title = (empty($instance['Title'])) ? '' : apply_filters('my_widget_title', $instance['Title']);

    if (!empty($title)) {
      echo $before_title . $title . $after_title;
    }

    echo $this->cir_widget_html_output($instance);
    echo $after_widget;
  }

  /**
   * Widget output
   *
   * @param array $args
   */
  private function cir_widget_html_output($args = array()) {
    /**
     * guess what: output
     */
    if ($args['Advanced'] == 1) {
      $widgetJS = sprintf($this->jsAdvanced, $args['IconSet'], str_replace('#', '', $args['ColorText']), str_replace('#', '', $args['ColorBackground']));
    } else {
      $widgetJS = $this->jsSimple;
    }

    $widgetHTML = sprintf('<script type="text/javascript" src="%s"></script>', $widgetJS);
    if ($args['Advanced'] == 1) {
      $widgetHTML .= '<noscript><a href="http://www.thecircle.com">TheCircle psychic reading service</a></noscript>';
    }

    return $widgetHTML;
  } // private function cir_widget_html_output($args = array())
}

  /**
   * adding colorpicker
   */
  add_action('widgets_init', 'cir_enqueue_color_picker');
  function cir_enqueue_color_picker($hook_suffix) {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('cir-script-handle', plugins_url('cir-script.js', __FILE__), array('wp-color-picker'), false, true);
  }

  /**
   * widget initialization
   */
  add_action('widgets_init', create_function('', 'return register_widget("thecircleDailyHoroscope");'));
}