<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

/*
 * Form handling
 */
$t_errors = array();
define('CSRF_NAME', 'plugin_CustomFieldsLinks_config');


/**
 * workaround config_eval() called by plugin_config_get()
 *
 * This function exist because I did not understand how to deal with
 * the function `config_eval()` and it's evaluation of lines with multiple percent sign.
 *
 * @see config_eval()
 * @see plugin_config_set()
 * @return void
 */
function workaround_plugin_config_set($p_option, $p_value) {
	return plugin_config_set(
		$p_option,
		str_replace( '%', '\\%', $p_value )
	);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	form_security_validate(CSRF_NAME);
	$t_fields = gpc_get_string('fields', NULL);
	if (!CustomFieldsLinksPlugin::parse_fields($t_fields)) {
		$t_errors[] = 'fields';
	}

	if (empty($t_errors)) {
		workaround_plugin_config_set(
			'separator',
			gpc_get_string(
				'separator',
				CustomFieldsLinksPlugin::SEPARATOR
			)
		);
		workaround_plugin_config_set(
			'fields',
			$t_fields
		);
		form_security_purge(CSRF_NAME);
		header('Location: ' . plugin_page( 'config', TRUE ));
		print_successful_redirect( plugin_page( 'config', TRUE ) );
	}
}


html_page_top1( 'Custom fields links' );
html_page_top2();
print_manage_menu();
?>

<form action="<?php echo plugin_page( 'config' ) ?>" method="post">
<div class="form-group">
 <label for="separator"><?php echo lang_get( 'plugin_CustomFieldsLinks_separator' ); ?></label>
 <input type="text" name="separator" id="separator" value="<?php echo htmlspecialchars(plugin_config_get( 'separator' ));?>">
 <small><?php printf(
  lang_get( 'plugin_CustomFieldsLinks_separator_help' ),
  htmlspecialchars(CustomFieldsLinksPlugin::SEPARATOR)
  ); ?></small>
</div>

<div class="form-group<?php if (in_array('fields', $t_errors)) { echo ' has-error'; } ?>">
 <label for="fields"><?php echo lang_get( 'plugin_CustomFieldsLinks_fields' ); ?></label><br>
 <small>
  <?php printf(
   lang_get( 'plugin_CustomFieldsLinks_fields_help' ),
   'manage_custom_field_page.php',
   'http://php.net/sprintf'
  ); ?>
 </small><br><?php if (in_array('fields', $t_errors)):?>
 <span class="help-block"><?php echo lang_get( 'plugin_CustomFieldsLinks_invalid_format' ); ?></span><br><?php endif; ?>
 <textarea name="fields" id="fields" placeholder="FieldName = https://mantisbt.example.net/view.php?id=%d" cols="80" rows="5" maxlength=""><?php echo htmlspecialchars(plugin_config_get( 'fields' )); ?></textarea>
</div>

<div class="form-group">
 <?php echo form_security_field(CSRF_NAME); ?>
 <input type="submit" class="button" value="<?php echo lang_get( 'plugin_update' ) ?>">
</div>

</form>

<?php
html_page_bottom1( __FILE__ );
?>
