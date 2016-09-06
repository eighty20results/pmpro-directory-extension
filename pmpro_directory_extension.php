<?php
/*
Plugin Name: PMPro Membership Directory Add-on (for BER Associates)
Plugin URI: https://eighty20results.com/support/
Description: Extending the PMPro Membership Directory (Adds skill & area search support)
Version: 1.3
Author: Thomas Sjolshagen <thomas@eighty20results.com>
Author URI: https://eighty20results.com/thomas-sjolshagen
License: GPL2
*/
/**
 * Returns a list of counties in Ireland (per Wikipedia)
 * @return array - Array of counties in Ireland
 */
function pmproemd_getCounties() {
	return array(                  // Options for the drop-down (select) field.
		'antrim'      => 'Antrim',
		'armagh'      => 'Armagh',
		'carlow'      => 'Carlow',
		'cavan'       => 'Cavan',
		'clare'       => 'Clare',
		'cork'        => 'Cork',
		'donegal'     => 'Donegal',
		'down'        => 'Down',
		'dublin'      => 'Dublin',
		'fermanagh'   => 'Fermanagh',
		'galway'      => 'Galway',
		'kerry'       => 'Kerry',
		'kildare'     => 'Kildare',
		'kilkenny'    => 'Kilkenny',
		'laois'       => 'Laois',
		'leitrim'     => 'Leitrim',
		'limerick'    => 'Limerick',
		'londonderry' => 'Londonderry',
		'longford'    => 'Longford',
		'louth'       => 'Louth',
		'mayo'        => 'Mayo',
		'meath'       => 'Meath',
		'monaghan'    => 'Monaghan',
		'offaly'      => 'Offaly',
		'roscommon'   => 'Roscommon',
		'sligo'       => 'Sligo',
		'tipperary'   => 'Tipperary',
		'tyrone'      => 'Tyrone',
		'waterford'   => 'Waterford',
		'westmeath'   => 'Westmeath',
		'wexford'     => 'Wexford',
		'wicklow'     => 'Wicklow',
	);
}

/**
 * Unload any preexisting "Select2" scripts & styles & use v4 based stuff instead (specifically for Register Helper)
 */
function enqueue_select2() {
	wp_deregister_script( 'select2' );
	wp_deregister_style( 'select2' );

	wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css', null, "4.0.2" );
	wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.min.js', array( 'jquery' ), '4.0.2' );

	wp_enqueue_style( 'select2' );
	wp_enqueue_script( 'select2' );

	wp_register_style( 'pmproemd', plugin_dir_url( __FILE__ ) . "/pmpro-directory-extension.css", null, '1.1' );
	wp_enqueue_style( 'pmproemd' );
}

add_action( 'wp_enqueue_scripts', 'enqueue_select2', 10 );

/**
 * Enqueue (load) the JavaScript for the Directory admin page
 */
function pmproemd_enqueue_admin() {
	wp_register_script( 'pmproemd-admin', plugin_dir_url( __FILE__ ) . '/pmpro-extended-directory.js', array( 'jquery' ), '1.1' );
	wp_enqueue_script( 'pmproemd-admin' );
}

add_action( 'admin_enqueue_scripts', 'pmproemd_enqueue_admin' );

/**
 * Return array of HTML to display as search fields in the membership directory list.
 *
 * @return array - Array of HTML definitions for the search field(s).
 */
function pmproemd_extra_search_fields() {

	$fields         = array();
	$max_selections = apply_filters( 'pmproemd_searchable_max_selections', 5 );

	ob_start(); ?>
	<div class="pmpromd-search-field-div">
		<label>
			<p class="screen-reader-text"
			   style="padding-bottom: 0; margin-bottom: 0;"><?php _e( 'Search by area(s)', 'pmpromd' ); ?>:</p>
			<select id="pmpro_service_area" class="e20r-select2-container select2" name="pmpro_service_area[]"
			        multiple="multiple">
				<option
					value="na" <?php empty( $_REQUEST['pmpro_service_area'] ) ? 'selected="selected"' : null; ?>></option><?php

				$counties = apply_filters( 'pmpromd_searchable_service_areas', pmproemd_getCounties() );

				foreach ( $counties as $key => $value ) {
					?>
					<option
					value="<?php echo $key; ?>" <?php echo isset( $_REQUEST['pmpro_service_area'] ) ? pmproemd_selected( $_REQUEST['pmpro_service_area'], $key ) : null; ?>><?php echo $value; ?></option><?php
				} ?>
			</select>
		</label>
	</div>
	<?php
	$html[] = ob_get_clean();

	ob_start(); ?>
	<div class="pmpromd-search-field-div">
		<label for="pmpro_skills">
			<p class="screen-reader-text"
			   style="padding-bottom: 0; margin-bottom: 0;"><?php _e( 'Search by service(s)', 'pmpromd' ); ?>:</p>
			<select id="pmpro_skills" class="e20r-select2-container select2" name="pmpro_skills[]" multiple="multiple">
				<option
					value="na" <?php empty( $_REQUEST['pmpro_skills'] ) ? 'selected="selected"' : null; ?>></option><?php

				$skills = apply_filters( 'pmpromd_searchable_skills', pmproemd_getSkills() );
				foreach ( $skills as $key => $value ) {
					?>
					<option
					value="<?php echo $key; ?>" <?php echo isset( $_REQUEST['pmpro_skills'] ) ? pmproemd_selected( $_REQUEST['pmpro_skills'], $key ) : null; ?>><?php echo $value; ?></option><?php
				} ?>
			</select>
		</label>
	</div>
	<script>
		jQuery(document).ready(function () {
			var area_options = {
				theme: "classic",
				allowClear: true,
				width: "60%",
				maximumSelectionLength: <?php echo $max_selections; ?>,
				maximumInputLength: 35,
				placeholder: {
					id: -1,
					text: <?php echo "Select up to {$max_selections} areas"; ?>
				}
			};
			var service_options = {
				theme: "classic",
				allowClear: true,
				width: "60%",
				maximumInputLength: 35,
				placeholder: {
					id: -1,
					text: <?php echo "Select services"; ?>
				}
			};

			jQuery('#pmpro_skills').select2(service_options);
			jQuery('#pmpro_service_area').select2(area_options);
		});
	</script><?php

	$html[] = ob_get_clean();

	return $html;
}

add_filter( 'pmpro_member_directory_extra_search_input', 'pmproemd_extra_search_fields' );

/**
 * Custom 'selected' setting for select inputs
 *
 * @param $comp - Variable to compare against
 * @param $var - Variable to check
 *
 * @return string (html) for the selected field.
 */
function pmproemd_selected( $var, $comp ) {
	$selected = null;

	if ( is_array( $var ) ) {
		$selected = ( in_array( $comp, $var ) ? 'selected="selected"' : null );
	} else {
		$selected = selected( $var, $comp );
	}

	return $selected;
}

/**
 * Filter function to allow custom search fields for the directory (assumes the field exists as usermeta)
 *
 * @param $fields - Array of fields to search based on
 *
 * @return array -- Array of fields to add as search fields.
 */
function pmproemd_extra_search_fieldlist( $fields ) {

	foreach ( array( 'pmpro_skills', 'pmpro_service_area' ) as $metafield ) {
		$fields[] = $metafield;
	}

	return $fields;
}

add_filter( 'pmpromd_extra_search_fields', 'pmproemd_extra_search_fieldlist' );

/**
 * Generate RH fields to use for the extended PMPro Directory add-on.
 */
function pmproemd_searchable_fields() {
	//require PMPro and PMPro Register Helper
	if ( ! defined( 'PMPRO_VERSION' ) || ! defined( 'PMPRORH_VERSION' ) ) {
		return;
	}

	$counties       = apply_filters( 'pmproemd_searchable_service_areas', pmproemd_getCounties() );
	$max_selections = apply_filters( 'pmproemd_searchable_max_selections', 5 );
	$skills         = apply_filters( 'pmproemd_searchable_skills', pmproemd_getSkills() );

	$fields   = array();
	$fields[] = new PMProRH_Field(
		"pmpro_service_area",                              // Needs to match a field from `pmpromd_extra_search_fields`
		"select2",                                         // The type of field to use
		array(
			"label"          => "Select the area(s) you cover",
			// Label to use for the field when displaying it
			"class"          => "pmpro_rh_select",
			// Custom CSS class to add (your choice)
			"profile"        => true,
			// Include in the user's profile (true | false | 'only' | 'only_admin')
			"memberslistcsv" => true,
			// Let field be included in "Member List" CSV export (true | false)
			"addmember"      => true,
			// Used if the "Add Member Admin" add-on is present (true | false)
			"required"       => true,
			// Make this field required (true | false)
			'options'        => $counties,
			"select2options" => 'theme: "classic",
				allowClear: true,
				width: "60%",
				maximumSelectionLength: ' . $max_selections . ',
				maximumInputLength: 35,
				placeholder: {
					id: -1,
					text: "Select area(s)"
				}',
		)
	);

	$fields[] = new PMProRH_Field(
		'pmpro_skills',                 // needs to match the field from `pmpromd_extra_search_fields`
		'select2',
		array(
			'label'          => "Select the service(s) you provide",
			'class'          => "pmpro_rh_select",
			'profile'        => true,
			"memberslistcsv" => true,
			// Let field be included in "Member List" CSV export (true | false)
			"addmember"      => true,
			// Used if the "Add Member Admin" add-on is present (true | false)
			"required"       => true,
			// Make this field required (true | false)
			'options'        => $skills,
			"select2options" => 'theme: "classic",
				allowClear: true,
				width: "60%",
				maximumInputLength: 35,
				maximumSelectionLength: ' . $max_selections . ',
				placeholder: {
					id: -1,
					text: "Select service(s)"
				}',
		)
	);

	//add the fields into a new checkout_boxes are of the checkout page
	foreach ( $fields as $field ) {
		pmprorh_add_registration_field(
			"checkout_boxes", // location on checkout page
			$field            // PMProRH_Field object
		);
	}

}

add_action( 'init', 'pmproemd_searchable_fields' );

/**
 * Return stored array of specialites/skills to use for assessors
 *
 * @return mixed|void - array of specialties/skills
 */
function pmproemd_getSkills() {

	$skills = get_option( 'pmproemd_skill_list', apply_filters( 'pmproemd_searchable_skills', array() ) );

	return $skills;
}

/**
 * Load the content for the Directory Admin page
 *
 * @return string - HTML containing the skills/specialty editor
 */
function pmproemd_load_page() {

	ob_start();

	$skills = get_option( 'pmproemd_skill_list', apply_filters( 'pmproemd_searchable_skills', array() ) );
	$size   = count( $skills ) < 4 ? 4 : count( $skills );

	// render Directory page content
	?>
	<div id="pmproemd-settings">
		<h2>Directory Settings</h2>

		<form id="pmproemd_settings">
			<?php wp_nonce_field( 'update-specialty-list', 'pmproemd-nonce' ); ?>
			<table id="skill_settings">
				</tr>
				<th class="pmproemd-heading"><?php _e( "Add new service", "pmproemd" ); ?>:</th>
				<th class="pmproemd-heading"><?php _e( "Services List (select before clicking 'Remove')", "pmproemd" ); ?>
					:
				</th>
				<tr>

					<td class="pmproemd-edit" style="vertical-align: top;"><input id="pmproemd-add-skill"
					                                                              name="pmproemd-add-skill"
					                                                              placeholder="<?php _e( "Type service to add", "pmproemd" ); ?>"
					                                                              style="width: 300px;"></td>
					<td class="pmproemd-skill-list" style="vertical-align: text-top;">
						<select id="pmproemd-skill-list" name="pmproemd-skills" class="pmproemd-skill-list"
						        size="<?php echo $size; ?>" style="width: 300px; height: auto;">
							<?php

							if ( ! empty( $skills ) ) {
								foreach ( $skills as $key => $s ) { ?>
									<option
									value="<?php echo strtolower( $key ); ?>"><?php echo ucfirst( $s ); ?></option><?php
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<p class="submit">
							<input id="add_dir_entry" name="add_dir_settings" type="submit" style="float: right;"
							       class="button button-primary" value="<?php _e( 'Add', 'pmpro' ); ?>"/>
						</p>
					</td>
					<td>
						<p class="submit">
							<input id="delete_dir_entry" style="float: left;" name="delete_dir_entry" type="submit"
							       class="button button-secondary" value="<?php _e( 'Remove', 'pmpro' ); ?>"/>
						</p>

					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php

	$html = ob_get_clean();

	return $html;
}

/**
 * Process & load the admin page
 */
function pmproemd_admin_page() {

	// Process settings if needed.
	if ( ! empty( $_REQUEST['save_dir_settings'] ) ) {

		$specialties = array();

		// save all entries from the skills list
		foreach ( $_REQUEST['pmpro_skills'] as $skill ) {
			// lowercase the key & sentence case the description (value)
			$specialties[ strtolower( sanitize_text_field( $skill ) ) ] = ucfirst( sanitize_text_field( $skill ) );
		}

		// save it as a WP options array
		update_option( 'pmproemd_skill_list', $specialties, false );
	}

	echo pmproemd_load_page();
}

/**
 * Adds the received specialty to the specialties array.
 */
function pmproemd_update_specialty() {
	check_ajax_referer( 'update-specialty-list', 'pmproemd-nonce' );

	$s      = isset( $_REQUEST['pmproemd-skill'] ) ? ucfirst( sanitize_text_field( $_REQUEST['pmproemd-skill'] ) ) : null;
	$action = isset( $_REQUEST['pmproemd_action'] ) ? sanitize_key( $_REQUEST['pmproemd_action'] ) : null;

	if ( empty( $action ) || empty( $s ) ) {
		if ( WP_DEBUG === true ) {
			error_log( "Action: {$action}, Skill: {$s}" );
		}

		wp_send_json_error();
	}

	$skills = get_option( 'pmproemd_skill_list', apply_filters( 'pmproemd_searchable_skills', array() ) );

	if ( 'add' === $action ) {
		if ( ! empty( $s ) ) {
			if ( ! in_array( $s, $skills ) ) {
				$skills[ strtolower( str_replace( ' ', '_', $s ) ) ] = ucfirst( $s );
			}
		}
	} elseif ( 'delete' === $action ) {
		if ( ! empty( $s ) ) {
			unset( $skills[ strtolower( $s ) ] );
		}
	}

	if ( ! empty( $s ) && update_option( 'pmproemd_skill_list', $skills, false ) ) {
		wp_send_json_success( array( 'html' => pmproemd_load_page() ) );
		wp_die();
	} else {
		wp_send_json_error( array( 'message' => sprintf( __( 'Error saving speciality: %s', 'pmproemd' ), $s ) ) );
		wp_die();
	}
}

add_action( 'wp_ajax_pmproemd_save_skills', 'pmproemd_update_specialty' );

/**
 * Returns error message to caller.
 */
function pmproemd_unpriv() {
	wp_send_json_error( array(
		'message' => __( 'You must be logged in to edit specialties', "pmproemd" )
	) );
	wp_die();
}

add_action( 'wp_ajax_nopriv_pmproemd_save_skills', 'pmproemd_unpriv' );

/**
 * Define menu item & load admin page for wp-admin menu item
 */
function pmproemd_loadAdminPage() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( "You do not have permission to perform this action.", "pmproemd" ) );
	}

	add_submenu_page( 'pmpro-membershiplevels', __( "Directory", "pmproemd" ), __( "Directory", "pmproemd" ), 'manage_options', 'pmproemd_settings', 'pmproemd_admin_page' );
}

add_action( 'admin_menu', 'pmproemd_loadAdminPage' );

/**
 * Check that Register Helper is installed & active. If not, display wp-admin warning
 */
function pmproemd_isRHPresent() {

	//require PMPro and PMPro Register Helper
	if ( defined( 'PMPRO_VERSION' ) && defined( 'PMPRORH_VERSION' ) ) {
		return;
	}

	// PMPro or RH is missing!
	?>
	<div class="update-nag error notice">
	<p><?php _e( "PMPro Member Directory needs both Paid Memberships Pro and Register Helper activated", 'pmpromd' ); ?></p>
	</div><?php
}

add_action( 'admin_notices', 'pmproemd_isRHPresent' );

function pmpremd_randomize_sort_order( $sql, $order_by_field, $order ) {

	// order this by the record ID for the membership table.
	$order_by_field = 'mu.id';

	$sql = sprintf( "ORDER BY %s %s", $order_by_field, $order );

	return $sql;
}

// add_filter( "pmpro_member_directory_set_order", 'pmpremd_randomize_sort_order', 10, 3 );