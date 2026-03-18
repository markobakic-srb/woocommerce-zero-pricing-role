=== WooCommerce Zero Pricing Role ===
Contributors: markobakic-srb
Tags: woocommerce, zero pricing, free role, user role, discount, employee perks
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight plugin that creates a dedicated user role with zero pricing across your WooCommerce store – perfect for employees, testers, or giveaways.

== Description ==

**WooCommerce Zero Pricing Role** adds a new user role that automatically sets all product prices, shipping, and taxes to $0 for assigned users. Whether you need to give free meals to staff, test your checkout flow, or run a giveaway, this plugin handles it with zero configuration.

= Key Features =

* **Dedicated Role** – Creates a "Zero Pricing Role" on activation
* **Universal Zero Pricing** – All products, variations, shipping, and taxes show $0 for that role
* **Frontend & Admin** – Works on product pages, cart, checkout, and manual order creation
* **Secure** – Hides admin bar and redirects wp-admin access
* **Lightweight** – No settings, no bloat – just activate and assign

= Use Cases =

* Employee meal programs in restaurants or cafes
* Testing and QA on staging sites
* Free samples or giveaways for influencers
* Internal supply ordering for staff

== Installation ==

1. Upload the `woocommerce-zero-pricing-role` folder to `/wp-content/plugins/`
2. Activate the plugin via the 'Plugins' menu in WordPress
3. Go to **Users → Add New** and assign the "Zero Pricing Role" to any user
4. That user will now enjoy $0 pricing throughout the store

== Frequently Asked Questions ==

= Will this affect my regular customers? =

No. Only users explicitly assigned the "Zero Pricing Role" receive zero pricing. Everyone else sees normal prices.

= Can I make only certain products free? =

This version applies zero pricing to all products. For selective discounts, consider using a separate discount plugin alongside this one.

= How do I remove the role? =

Deactivate and delete the plugin – it will automatically remove the role on uninstall (if you included the uninstall.php script).

= Does it work with WooCommerce subscriptions? =

Yes, but subscription signups will still create subscriptions with $0 initial payment. Recurring payments will also be $0.

== Changelog ==

= 1.0.0 =
* Initial release
