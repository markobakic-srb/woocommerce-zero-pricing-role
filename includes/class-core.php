<?php
class WZR_Core {
    private $target_role = 'zero_pricing_role';

    public function __construct() {
        // Product prices
        add_filter('woocommerce_product_get_price', [$this, 'zero_price'], 99, 2);
        add_filter('woocommerce_product_get_regular_price', [$this, 'zero_price'], 99, 2);
        add_filter('woocommerce_product_get_sale_price', [$this, 'zero_price'], 99, 2);
        add_filter('woocommerce_product_variation_get_price', [$this, 'zero_price'], 99, 2);

        // Cart
        add_action('woocommerce_before_calculate_totals', [$this, 'zero_cart'], 99);

        // Shipping
        add_filter('woocommerce_package_rates', [$this, 'zero_shipping'], 99, 2);

        // Taxes
        add_filter('woocommerce_cart_totals_get_taxes', [$this, 'zero_taxes'], 99);
        add_filter('woocommerce_order_get_taxes', [$this, 'zero_taxes'], 99);

        // Admin order creation (optional - you may remove if not needed)
        add_action('woocommerce_before_save_order_items', [$this, 'zero_admin_order'], 10, 2);

        // Fees
        add_action('woocommerce_cart_calculate_fees', [$this, 'remove_fees'], 10);

        // Session flag
        add_action('woocommerce_cart_emptied', [$this, 'clear_session']);

        // ========== ADMIN RESTRICTIONS ==========
        // Hide admin bar on frontend
        add_filter('show_admin_bar', [$this, 'hide_admin_bar']);

        // Redirect from wp-admin (except AJAX)
        add_action('admin_init', [$this, 'restrict_admin_access']);
    }

    private function user_has_role() {
        $user = wp_get_current_user();
        return in_array($this->target_role, (array) $user->roles);
    }

    public function zero_price($price, $product) {
        return $this->user_has_role() ? 0 : $price;
    }

    public function zero_cart($cart) {
        if ($this->user_has_role() && !WC()->session->get('wzr_cart_zeroed')) {
            foreach ($cart->get_cart() as $item) {
                $item['data']->set_price(0);
            }
            WC()->session->set('wzr_cart_zeroed', true);
        }
    }

    public function zero_shipping($rates, $package) {
        if ($this->user_has_role()) {
            foreach ($rates as $rate) {
                $rate->cost = 0;
                $rate->taxes = [];
            }
        }
        return $rates;
    }

    public function zero_taxes($taxes) {
        return $this->user_has_role() ? [] : $taxes;
    }

    public function zero_admin_order($order_id, $items) {
        if ($this->user_has_role()) {
            $order = wc_get_order($order_id);
            if (!$order) return;
            foreach ($order->get_items() as $item) {
                $item->set_subtotal(0);
                $item->set_total(0);
                $item->save();
            }
            foreach ($order->get_shipping_methods() as $shipping) {
                $shipping->set_total(0);
                $shipping->save();
            }
            $order->calculate_totals();
        }
    }

    public function remove_fees($cart) {
        if ($this->user_has_role() && is_object($cart)) {
            $cart->fees = [];
        }
    }

    public function clear_session() {
        if (WC()->session) {
            WC()->session->__unset('wzr_cart_zeroed');
        }
    }

    // ========== NEW METHODS ==========
    public function hide_admin_bar($show) {
        if ($this->user_has_role()) {
            return false;
        }
        return $show;
    }

    public function restrict_admin_access() {
        if ($this->user_has_role() && !defined('DOING_AJAX') && !wp_doing_ajax()) {
            wp_safe_redirect(home_url());
            exit;
        }
    }
}
