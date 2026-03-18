<?php
// includes/class-activator.php
class WZR_Activator {
    public static function activate() {
    add_role(
        'zero_pricing_role',
        __('Zero Pricing Role', 'wc-zero-role'),
        [
            'read' => true, // Allows login and frontend access
        ]
    );
}
}
