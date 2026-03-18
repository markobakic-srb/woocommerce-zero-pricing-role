# WooCommerce Zero Pricing Role

A lightweight WordPress plugin that creates a dedicated user role with zero pricing across your WooCommerce store. Perfect for employee perks, testing, giveaways, or internal use.

## Features

- Creates a **"Zero Pricing Role"** upon activation
- All product prices, shipping, and taxes automatically set to $0 for users with this role
- Works on frontend (product pages, cart, checkout) and admin order creation
- Hides admin bar and redirects wp-admin access for security
- No configuration needed – just activate and assign the role
- Fully WooCommerce compatible

## Installation

1. Upload the `woocommerce-zero-pricing-role` folder to `/wp-content/plugins/`
2. Activate the plugin via the WordPress admin
3. Go to **Users → Add New** and assign the "Zero Pricing Role" to any user
4. Done! That user will now see $0 for everything

## Usage

- Log in as a user with the Zero Pricing Role
- Browse products – all prices show $0
- Add items to cart – cart total is $0
- Proceed to checkout – shipping and taxes are $0
- Create orders manually in admin – all totals are automatically zero

The role has no admin capabilities; users are redirected to the homepage if they try to access wp-admin.

## Frequently Asked Questions

### Does this affect regular customers?
No. Only users explicitly assigned the "Zero Pricing Role" receive zero pricing.

### Can I customize which products are free?
Not in this version – the plugin applies zero pricing to all products. For more advanced rules, consider companion plugins.

### Will this work with other discount plugins?
The plugin runs at high priority, but conflicts are rare. Test with your setup.

## Changelog

### 1.0.0
- Initial release

## License

GPL v2 or later
