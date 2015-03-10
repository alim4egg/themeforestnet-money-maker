<?php
	
	/*
		Plugin Name: Themeforest.net Money Maker
		Plugin URI:
		Description: Simple way earn money. Show newest themes from themeforest.net and get % from you referral.
		Author: djjmz
		Version: 1.0
		Author URI:
	*/
	
	function themeforest_themes_widget_load_widget() {
		register_widget('Themeforest_Thenes_Widget');
	}
	
	add_action('widgets_init', 'themeforest_themes_widget_load_widget');
	
	class Themeforest_Thenes_Widget extends WP_Widget {
		
		function __construct() {
			parent::__construct('Themeforest_Thenes_Widget', 'Themeforest.net Money Maker', array('description' => '&nbsp;'));
		}
		
		public function widget($args, $instance) {
			global $post;
			extract($args);
			$title = apply_filters('widget_title', $instance['title']);
			$cache = ($instance['cache'] == 'on') ? 'on' : 'off';
			$nick = ($instance['nick'] != '' or $instance['nick'] != NULL) ? $instance['nick'] : '';
			$item_number = ($instance['item_number'] and $instance['item_number'] >= 1) ? $instance['item_number'] : '25';
			$item_per_row = ($instance['item_per_row'] and $instance['item_per_row'] >= 1) ? $instance['item_per_row'] : '4';
			echo $args['before_widget'];
			if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];
			$current_url = home_url(add_query_arg(array()));
			$cache_file = WP_CONTENT_DIR . '/cache/EWFW.html';
			if (!is_dir(WP_CONTENT_DIR . '/cache/')) {
				mkdir(WP_CONTENT_DIR . '/cache/', 0777);
			}
			if ($cache == 'on') {
				if (!file_exists($cache_file) or ( time() - filemtime($cache_file)) > 3600) {
					$ch_data = wp_remote_get('http://marketplace.envato.com/api/v3/new-files:themeforest,wordpress.json', array('timeout' => 10, 'httpversion' => '1.1'));
					$ch_data = wp_remote_retrieve_body($ch_data);
					if (!empty($ch_data)) {
						$json_data = json_decode($ch_data, true);
						$data_count = $item_number ? $item_number - 1 : count($json_data['new-files']) - 1;
						$table = '<table style="display:block;"><tr>';
						$j = $item_per_row - 1;
						for ($i = 0; $i <= $data_count; $i++) {
							$table .= '<td><a href="' . $json_data['new-files'][$i]['url'] . '?ref=' . $nick . '" target="_blank"><img style="width: 50px;" src="' . $json_data['new-files'][$i]['thumbnail'] . '" alt="' . $json_data['new-files'][$i]['item'] . '" /></td>';
							if ($i == $j) {
								$table .= '</tr><tr>';
								$j = $j + $item_per_row;
							}
						}
						$table .= '</table>';
						} else {
						$table = 'Sorry, but there was a problem connecting to the API.';
					}
					file_put_contents($cache_file, $table);
					echo $table;
					} else {
					echo file_get_contents($cache_file);
				}
				} else {
				$ch_data = wp_remote_get('http://marketplace.envato.com/api/v3/new-files:themeforest,wordpress.json', array('timeout' => 10, 'httpversion' => '1.1'));
				$ch_data = wp_remote_retrieve_body($ch_data);
				if (!empty($ch_data)) {
					$json_data = json_decode($ch_data, true);
					$data_count = $item_number ? $item_number - 1 : count($json_data['new-files']) - 1;
					echo '<table style="display:block;"><tr>';
					$j = $item_per_row - 1;
					for ($i = 0; $i <= $data_count; $i++) {
						echo '<td><a href="' . $json_data['new-files'][$i]['url'] . '?ref=' . $nick . '" target="_blank"><img style="width: 50px;" src="' . $json_data['new-files'][$i]['thumbnail'] . '" alt="' . $json_data['new-files'][$i]['item'] . '" /></td>';
						if ($i == $j) {
							echo '</tr><tr>';
							$j = $j + $item_per_row;
						}
					}
					echo'</table>';
					} else {
					echo 'Sorry, but there was a problem connecting to the API.';
				}
			}
			echo $args['after_widget'];
		}
		
		public function form($instance) {
			if (isset($instance['title'])) {
				$title = $instance['title'];
				} else {
				$title = 'Title';
			}
			if (isset($instance['nick'])) {
				$nick = $instance['nick'];
				} else {
				$nick = '';
			}
			if (isset($instance['item_number'])) {
				$item_number = $instance['item_number'];
				} else {
				$item_number = '25';
			}
			if (isset($instance['item_per_row'])) {
				$item_per_row = $instance['item_per_row'];
				} else {
				$item_per_row = '4';
			}
			if (isset($instance['cache']) and $instance['cache'] == 'on') {
				$checked = 'checked="checked"';
				} else {
				$checked = '';
			}
			echo '<p><label for="' . $this->get_field_id('title') . '">Title:</label>
			<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" /><br>'
			. '<label for="' . $this->get_field_id('nick') . '">Themeforest.net Nick:</label>
			<input class="widefat" id="' . $this->get_field_id('nick') . '" name="' . $this->get_field_name('nick') . '" type="text" value="' . esc_attr($nick) . '" /><br>'
			. '<label for="' . $this->get_field_id('item_number') . '">Items in widget:</label>                  
			<input class="widefat" id="' . $this->get_field_id('item_number') . '" name="' . $this->get_field_name('item_number') . '" type="text" value="' . intval($item_number) . '" /><br>'
			. '<label for="' . $this->get_field_id('item_per_row') . '">Items per row:</label>                  
			<input class="widefat" id="' . $this->get_field_id('item_per_row') . '" name="' . $this->get_field_name('item_per_row') . '" type="text" value="' . intval($item_per_row) . '" /><br>'
			. '<label for="' . $this->get_field_id('cache') . '">Caching </label><input type="checkbox" class="widefat"  id="' . $this->get_field_id('cache') . '" name="' . $this->get_field_name('cache') . '" ' . $checked . '/></p>';
		}
		
		public function update($new_instance, $old_instance) {
			$instance = array();
			$instance['title'] = (!empty($new_instance['title']) ) ? stripslashes(wp_filter_post_kses(addslashes($new_instance['title']))) : '';
			$instance['nick'] = (!empty($new_instance['nick']) ) ? stripslashes(wp_filter_post_kses(addslashes($new_instance['nick']))) : '';
			$instance['item_number'] = (!empty($new_instance['item_number']) ) ? intval($new_instance['item_number']) : '25';
			$instance['item_per_row'] = (!empty($new_instance['item_per_row']) ) ? intval($new_instance['item_per_row']) : '4';
			$instance['cache'] = (!empty($new_instance['cache'])) ? $new_instance['cache'] : 'false';
			return $instance;
		}
		
	}
