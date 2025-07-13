<?php
/**
 * Plugin Name:       Automation for WPSubscriptions
 * Description:       A plugin to capture WPSubscription triggers using an automator like flowmattic
 * Tested up to:      6.8.1
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Version:           0.5.5
 * Author:            reallyusefulplugins.com
 * Author URI:        https://reallyusefulplugins.com
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       automation-for-wpsubscription
 * Website:           https://reallyusefulplugins.com
 */

if ( ! defined('ABSPATH') ) {
    exit; // Prevent direct access
}


// include other files
require_once __DIR__ . '/includes/setup.php'; // Flowmattic Admin Test located in Automation

// Define plugin constants
define('rup_wpsco_automation_for__wpsubscription_VERSION', '0.5.5');
define('rup_wpsco_automation_for__wpsubscription_SLUG', 'automation-for-wpsubscription'); // Replace with your unique slug if needed
define('rup_wpsco_automation_for__wpsubscription_MAIN_FILE', __FILE__);
define('rup_wpsco_automation_for__wpsubscription_DIR', plugin_dir_path(__FILE__));
define('rup_wpsco_automation_for__wpsubscription_URL', plugin_dir_url(__FILE__));

add_action('plugins_loaded', function () {
    if (!class_exists('Sdevs_Subscription')) {
        return; // WPSubscription not active
    }

    // Hook into WPSubscription events and reroute with custom action names
    add_action('subscrpt_subscription_activated', function ($subscriptionId) {
        rup_wpsco_trigger_subscription_event('activated', $subscriptionId);
    }, 20, 1);

    add_action('subscrpt_subscription_expired', function ($subscriptionId) {
        rup_wpsco_trigger_subscription_event('expired', $subscriptionId);
    }, 20, 1);

    add_action('subscrpt_subscription_cancelled_email_notification', function ($subscriptionId) {
        rup_wpsco_trigger_subscription_event('cancelled', $subscriptionId);
    }, 20, 1);
});

function rup_wpsco_trigger_subscription_event($event, $subscriptionId, $extra = []) {
    if (empty($subscriptionId)) return;

    $orderId = get_post_meta($subscriptionId, '_subscrpt_order_id', true);
    if (!$orderId) return;

    $order = wc_get_order($orderId);
    if (!$order) return;

    $customerId = $order->get_customer_id();
    $user = get_userdata($customerId);
    $subscription = get_post($subscriptionId);
    if (!$subscription) return;

    $orderData = [
        'id' => $order->get_id(),
        'order_key' => $order->get_order_key(),
        'card_tax' => $order->get_cart_tax(),
        'currency' => $order->get_currency(),
        'discount_tax' => $order->get_discount_tax(),
        'discount_to_display' => $order->get_discount_to_display(),
        'discount_total' => $order->get_discount_total(),
        'fees' => $order->get_fees(),
        'shipping_tax' => $order->get_shipping_tax(),
        'shipping_total' => $order->get_shipping_total(),
        'tax_totals' => $order->get_tax_totals(),
        'total' => $order->get_total(),
        'total_refunded' => $order->get_total_refunded(),
        'total_tax_refunded' => $order->get_total_tax_refunded(),
        'total_shipping_refunded' => $order->get_total_shipping_refunded(),
        'total_qty_refunded' => $order->get_total_qty_refunded(),
        'remaining_refund_amount' => $order->get_remaining_refund_amount(),
        'shipping_method' => $order->get_shipping_method(),
        'date_created' => $order->get_date_created() ? $order->get_date_created()->format('Y-m-d H:i:s') : '',
        'date_modified' => $order->get_date_modified() ? $order->get_date_modified()->format('Y-m-d H:i:s') : '',
        'date_completed' => $order->get_date_completed() ? $order->get_date_completed()->format('Y-m-d H:i:s') : '',
        'date_paid' => $order->get_date_paid() ? $order->get_date_paid()->format('Y-m-d H:i:s') : '',
        'customer_id' => $customerId,
        'created_via' => $order->get_created_via(),
        'customer_note' => $order->get_customer_note(),
        'billing_first_name' => $order->get_billing_first_name(),
        'billing_last_name' => $order->get_billing_last_name(),
        'billing_company' => $order->get_billing_company(),
        'billing_address_1' => $order->get_billing_address_1(),
        'billing_address_2' => $order->get_billing_address_2(),
        'billing_city' => $order->get_billing_city(),
        'billing_state' => $order->get_billing_state(),
        'billing_postcode' => $order->get_billing_postcode(),
        'billing_country' => $order->get_billing_country(),
        'billing_email' => $order->get_billing_email(),
        'billing_phone' => $order->get_billing_phone(),
        'shipping_first_name' => $order->get_shipping_first_name(),
        'shipping_last_name' => $order->get_shipping_last_name(),
        'shipping_company' => $order->get_shipping_company(),
        'shipping_address_1' => $order->get_shipping_address_1(),
        'shipping_address_2' => $order->get_shipping_address_2(),
        'shipping_city' => $order->get_shipping_city(),
        'shipping_state' => $order->get_shipping_state(),
        'shipping_postcode' => $order->get_shipping_postcode(),
        'shipping_country' => $order->get_shipping_country(),
        'payment_method' => $order->get_payment_method(),
        'payment_method_title' => $order->get_payment_method_title(),
        'status' => $order->get_status(),
        'checkout_order_received_url' => $order->get_checkout_order_received_url(),
        'line_items' => [],
        'product_names' => '',
        'line_items_quantity' => 0
    ];

    if (defined('WC_VERSION') && version_compare(WC_VERSION, '8.5.1', '>=')) {
        $orderData += [
            '_wc_order_attribution_referrer' => $order->get_meta('_wc_order_attribution_referrer'),
            '_wc_order_attribution_user_agent' => $order->get_meta('_wc_order_attribution_user_agent'),
            '_wc_order_attribution_utm_source' => $order->get_meta('_wc_order_attribution_utm_source'),
            '_wc_order_attribution_device_type' => $order->get_meta('_wc_order_attribution_device_type'),
            '_wc_order_attribution_source_type' => $order->get_meta('_wc_order_attribution_source_type'),
            '_wc_order_attribution_session_count' => $order->get_meta('_wc_order_attribution_session_count'),
            '_wc_order_attribution_session_entry' => $order->get_meta('_wc_order_attribution_session_entry'),
            '_wc_order_attribution_session_pages' => $order->get_meta('_wc_order_attribution_session_pages'),
            '_wc_order_attribution_session_start_time' => $order->get_meta('_wc_order_attribution_session_start_time'),
        ];
    }

    $lineItems = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $lineItems[] = [
            'product_id' => $item->get_product_id(),
            'variation_id' => $item->get_variation_id(),
            'product_name' => $item->get_name(),
            'quantity' => $item->get_quantity(),
            'subtotal' => $item->get_subtotal(),
            'total' => $item->get_total(),
            'subtotal_tax' => $item->get_subtotal_tax(),
            'tax_class' => $item->get_tax_class(),
            'tax_status' => $item->get_tax_status(),
            'product_sku' => $product ? $product->get_sku() : '',
            'product_unit_price' => $product ? $product->get_price() : '',
        ];
    }

    $orderData['line_items'] = $lineItems;
    $orderData['product_names'] = implode(', ', array_column($lineItems, 'product_name'));
    $orderData['line_items_quantity'] = count($lineItems);

    $payload = [
        'subscription' => [
            'id' => $subscriptionId,
            'title' => $subscription->post_title,
            'status' => $subscription->post_status,
        ],
        'order' => $orderData,
        'customer' => [
            'id' => $customerId,
            'email' => $user ? $user->user_email : '',
            'name' => $user ? $user->display_name : '',
        ],
    ];

    $payload = array_merge($payload, $extra);

    do_action("rup_wpsco_subscription_{$event}", $payload);
}


// ──────────────────────────────────────────────────────────────────────────
//  Updater bootstrap (plugins_loaded priority 1):
// ──────────────────────────────────────────────────────────────────────────
add_action( 'plugins_loaded', function() {
    // 1) Load our universal drop-in. Because that file begins with "namespace UUPD\V1;",
    //    both the class and the helper live under UUPD\V1.
    require_once __DIR__ . '/includes/updater.php';

    // 2) Build a single $updater_config array:
    $updater_config = [
        'plugin_file' => plugin_basename(__FILE__),             // e.g. "simply-static-export-notify/simply-static-export-notify.php"
        'slug'        => rup_wpsco_automation_for__wpsubscription_SLUG,           // must match your updater‐server slug
        'name'        => 'Automation for WPSubscriptions',         // human‐readable plugin name
        'version'     => rup_wpsco_automation_for__wpsubscription_VERSION, // same as the VERSION constant above
        'key'         => '',                 // your secret key for private updater
        'server'      => 'https://raw.githubusercontent.com/stingray82/automation-for-wpsubscription/main/uupd/index.json',
    ];

    // 3) Call the helper in the UUPD\V1 namespace:
    \RUP\Updater\Updater_V1::register( $updater_config );
}, 1 );