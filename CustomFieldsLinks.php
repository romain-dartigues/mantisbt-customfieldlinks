<?php

/**
 * Custom fields links plugin
 */
class CustomFieldsLinksPlugin extends MantisPlugin {
	const VERSION = '0.0.1';
	const SEPARATOR = ', ';
	const R_FIELD = '/^([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(.+)\s*$/m';

	protected $fields = array();
	protected $separator = NULL;

	/**
	 * A method that populates the plugin information and minimum requirements.
	 * @return void
	 */
	function register()
	{
		$this->name = lang_get('plugin_CustomFieldsLinks_name');
		$this->description = lang_get('plugin_CustomFieldsLinks_description');
		$this->page = 'config';
		$this->version = self::VERSION;
		$this->requires = array(
			'MantisCore' => '1.3.5',
			// requiring https://github.com/mantisbt/mantisbt/pull/655?
		);
		$this-> uses = array();
		$this->author = 'Romain Dartigues';
		$this->contact = '';
		$this->url = 'https://github.com/romain-dartigues/mantisbt-customfieldslinks';

	}

	/**
	 * Default plugin configuration.
	 * @return array
	 */
	function config()
	{
		return array(
			'separator' => self::SEPARATOR,
			'fields' => NULL,
		);
	}

	function hooks()
	{
		return array(
			'EVENT_DISPLAY_FORMATTED' => 'display_formatted',
			'EVENT_PLUGIN_INIT' => 'get_configuration',
		);
	}

	function get_configuration()
	{
		$this->fields = self::parse_fields(
			plugin_config_get('fields')
		);
		$this->separator = plugin_config_get('separator');
	}

	/**
	 * Hopefully convert a list of elements to a list of links
	 *
	 * @param string $p_event
	 * @param string $p_chained_param
	 * @print Formatted text
	 * @return NULL
	 */
	function display_formatted($p_event, $p_chained_param)
	{
		$t_args = NULL;
		$t_data = array();
		$t_return = array();
		$t_url_format = '%s';

		// find if we are called by the custom field API
		foreach(debug_backtrace() as $t_row)
		{
			if ($t_row['function'] == 'string_custom_field_value')
			{
				$t_args = $t_row['args'][0];
				break;
			}
		}

		if (
			!$t_args
			or
			!$p_chained_param
			or
			$p_event != 'EVENT_DISPLAY_FORMATTED'
			or
			!(
				empty($t_args['valid_regexp'])
				or
				preg_match_all(
					"/${t_args['valid_regexp']}/",
					$p_chained_param,
					$t_data
				)
			)
		)
		{
			return $p_chained_param;
		}

		// generate the links
		if (!empty($this->fields[ $t_args['name'] ]))
		{
			$t_url_format =&$this->fields[ $t_args['name'] ];
		}

		foreach($t_data[0] as $t_val)
		{
			$t_return[] = sprintf(
				'<a href="' . $t_url_format .'">%s</a>',
				urlencode($t_val),
				htmlspecialchars($t_val)
			);
		}

		/*
		 * Work around plugin MantisCoreFormatting messing with
		 * the output when either "text" or "url" processing is enabled.
		 * Print the result and return NULL to stop further processing.
		 * See: plugin.php?page=MantisCoreFormatting/config
		 */
		echo implode($this->separator, $t_return);

		return NULL;
	}

	/**
	 * Extract fields from a string
	 *
	 * @param string $input
	 * @return array or FALSE
	 */
	function parse_fields($input)
	{
		$data = array();
		if (preg_match_all(self::R_FIELD, $input, $data))
		{
			return array_combine($data[1], $data[2]);
		}
		return FALSE;
	}

}

?>
