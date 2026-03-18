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

        // Admin order creation (optional - keep for admins creating orders for zero role)
        add_action('woocommerce_before_save_order_items', [$this, 'zero_admin_order'], 10, 2);

        // Fees
        add_action('woocommerce_cart_calculate_fees', [$this, 'remove_fees'], 10);

        // Session flag
        add_action('woocommerce_cart_emptied', [$this, 'clear_session']);

        // ========== ADMIN RESTRICTIONS ==========
        // Hide admin bar on frontend
        add_filter('show_admin_bar', [$this, 'hide_admin_bar']);

        // Redirect from wp-admin (except login/logout/AJAX)
        add_action('admin_init', [$this, 'restrict_admin_access']);

        // ========== FRONTEND BANNER ==========
        // Display a top banner for zero-pricing users
        add_action('wp_body_open', [$this, 'display_zero_pricing_banner']);
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

    // Hide admin bar
    public function hide_admin_bar($show) {
        return $this->user_has_role() ? false : $show;
    }

    // Redirect from wp-admin (allow login/logout)
    public function restrict_admin_access() {
        if ($this->user_has_role() && !defined('DOING_AJAX') && !wp_doing_ajax()) {
            // Allow wp-login.php (including logout action)
            if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
                return;
            }
            // Also allow if the action is logout (though covered by above, but keep for safety)
            if (isset($_GET['action']) && $_GET['action'] === 'logout') {
                return;
            }
            wp_safe_redirect(home_url());
            exit;
        }
    }

    // Display top banner with logout link
    public function display_zero_pricing_banner() {
        if (!$this->user_has_role()) {
            return;
        }
        ?>
        <div class="wzr-zero-pricing-banner">
            <div class="wzr-banner-content">
                <span class="wzr-banner-message">🔔 <strong>Zero Pricing Mode Active:</strong> All prices, shipping, and taxes are $0 for you.</span>
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="wzr-banner-logout">Logout</a>
            </div>
        </div>
        <style>
    .wzr-zero-pricing-banner {
        position: sticky;  /* or fixed – see note below */
        top: 0;
        width: 100%;
        background: #2F7F7D;
        color: white;
        text-align: center;
        padding: 8px 0;
        font-size: 14px;
        z-index: 9999;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .wzr-banner-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .wzr-banner-message {
        flex: 1;
        text-align: left;
    }
    .wzr-banner-logout {
        background: rgba(255,255,255,0.2);
        color: white;
        text-decoration: none;
        padding: 2px 12px;
        border-radius: 20px;
        font-weight: 500;
        transition: background 0.3s;
        white-space: nowrap;
    }
    .wzr-banner-logout:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }
    /* Add a small top offset for themes with fixed headers */
    body {
        /* No extra padding needed with sticky, but if using fixed you might need:
           padding-top: 40px; */
    }
    @media (max-width: 600px) {
        .wzr-banner-content {
            flex-direction: column;
            gap: 5px;
        }
        .wzr-banner-message {
            text-align: center;
        }
    }
</style>
        <?php
    }
}
Fixes #1 – Add top banner and allow logout
