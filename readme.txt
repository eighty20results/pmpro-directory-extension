=== Member Directory Extension for BER Associates ===
Contributors: eighty20results
Tags: pmpro, paid memberships pro, members, directory
Requires at least: 3.5
Tested up to: 4.4.2
Stable tag: 1.2

Add a robust Member Directory and Profiles to Your Membership Site - with attributes to customize the display.

== Description ==
The Member Directory Extension enhances your membership directory by adding support for searches based
on Assessor Specialties & Service Area.

This plugin uses an enhanced version of the Paid Memberships Pro Member Directory and Member Profile page add-on.

A programmer can extend the Service Areas options by using a WordPress filter ('pmproemd_searchable_service_areas'),
but the default values currently include the counties of Ireland (as specified on Wikipedia).

The Speciality list can be modified via the "Memberships" -> "Directory" setting spage in the Wordpress Admin Dashboard.
It can also be augmented/extended by a programmer by using the 'pmproemd_searchable_skills' WordPress filter

The search fields are 'select2' based drop-downs (multi-select capable, max # of search items allowed are 3).

This add-on includes & loads 2 new Register Helper fields ('pmpro_skills', 'pmpro_service_area') to the users
Profile page & to the checkout page. The fields are marked as being required.

This add-on requires Paid Memberships Pro, PMPro Register Helper and the customized Member Directory & Profile add-on.

== Installation ==

1. Upload the `pmpro-directory-extension` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the `Plugins` menu in WordPress.
1. Add the 'fields="Select the area you cover,pmpro_service_area;Select the services you provide,pmpro_skills"' attribute (or extend your existing attribute) to your [pmpro_membership_directory] shortcode.
1. Navigate to Memberships > Directory to add your initial list of Specialties.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it via the support section of our website, and we'll fix it as soon as we can. Thanks for helping. https://eighty20results.com/support

== Changelog ==
= 1.2 =
* ENH: Can use filter to set max number of selectable items in the area & skills select-2 boxes (default value = 5).

= 1.0 =
* Initial release

