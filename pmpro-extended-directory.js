
jQuery.noConflict();

var manageSpecialties = {
    init: function() {
        "use strict";

        // load controls
        this.s_input = jQuery('#pmproemd-add-skill');
        this.s_list = jQuery('#pmproemd-skill-list');
        this.delete_button = jQuery("#delete_dir_entry");
        this.add_button = jQuery('#add_dir_entry');

        // configure events
        this._bind_inputs();
    },
    delete_from_list: function($value) {
        "use strict";
        var $class = this;
        console.log("Removing existing value: " + $value );

        $class._send_ajax('delete', $value);
    },
    add_to_list: function($value) {
        "use strict";

        var $class = this;

        // check whether the option is in the list already
        if( jQuery("#pmproemd-skill-list option[value='" + $value + "']").length === 0)
        {
            console.log("Adding new value: " + $value);
            // save to back-end
            $class._send_ajax('add', $value);
        }
    },
    _bind_inputs: function() {

        "use strict";
        var $class = this;

/*
        // handle keypresses (enter & tab)
        $class.s_input.keypress(function(k) {

            var $value = jQuery('#pmproemd-add-skill').val();

            if (k.which === 9) {
                $class.add_to_list($value);
            }
            if (k.which === 13) {
                $class.add_to_list($value);
            }
        });

        $class.s_input.unbind('focusout').on('focusout', function() {

            var $value = jQuery('#pmproemd-add-skill').val();
            $class.add_to_list($value);
        });
 */
        $class.delete_button.unbind('click').on('click', function() {

            var $value = $class.s_list.val();

            event.preventDefault();

            $class.delete_from_list($value);
        });

        $class.add_button.unbind('click').on('click', function() {

            var $value = $class.s_input.val();

            event.preventDefault();

            $class.add_to_list($value);
        });
    },
    _send_ajax: function( $operation, $value ) {
        "use strict";

        var $class = this;

        // transmit to backend (wp-admin)
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            timeout: 7000,
            dataType: 'JSON',
            data: {
                action: 'pmproemd_save_skills',
                'pmproemd-skill': $value,
                'pmproemd_action': $operation,
                'pmproemd-nonce': jQuery('#pmproemd-nonce').val()
            },
            error: function( xhr, textStatus, errorThrown ) {
                alert("Error adding specialty to database (" + textStatus + "): " + errorThrown );
                return false;
            },
            success: function($response) {

                if ($response.data.html.length !== 0) {
                    jQuery('#pmproemd-settings').html($response.data.html);
                    $class.init();
                }

                return;
            }
        });
    }
};

jQuery(document).ready(function() {
    "use strict";

    jQuery('body').ajaxStart(function() {
        jQuery(this).css({'cursor' : 'wait'});
    }).ajaxStop(function() {
        jQuery(this).css({'cursor' : 'default'});
    });

    var directorySettings = manageSpecialties;
    directorySettings.init();
});
