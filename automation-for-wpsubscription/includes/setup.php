<?php
if ( ! defined('ABSPATH') ) {
    exit; // Prevent direct access
}

add_action('admin_menu', 'rup_wpsco_add_automation_submenu', 100);

function rup_wpsco_add_automation_submenu() {
    global $submenu;

    if (!isset($submenu['wp-subscription'])) {
        return;
    }

    add_submenu_page(
        'wp-subscription',
        'WPSubscription Automation',
        'Automation',
        'manage_woocommerce',
        'wpsub_automation',
        'rup_wpsco_admin_automation_page'
    );
}

function rup_wpsco_admin_automation_page() {
    ?>
    <div class="wrap">
        <h1>WPSubscription Automation Tools</h1>

        <form method="post" style="margin-bottom:2em;">
            <h2>Trigger Real Hook</h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="subscription_id">Subscription ID</label></th>
                    <td><input name="subscription_id" type="number" id="subscription_id" class="regular-text" required></td>
                </tr>
            </table>
            <input type="hidden" name="real_action" value="1">
            <?php submit_button('Simulate Real Expiry'); ?>
        </form>

        <hr>

        <form method="post">
            <h2>Simulate Fake Events (Static Payloads)</h2>
            <?php submit_button('Simulate Activated', 'primary', 'simulate_fake_activated'); ?>
            <?php submit_button('Simulate Cancelled', 'secondary', 'simulate_fake_cancelled'); ?>
            <?php submit_button('Simulate Status Change', 'secondary', 'simulate_fake_status_changed'); ?>
            <?php submit_button('Simulate Expired', 'secondary', 'simulate_fake_expired'); ?>
        </form>

        <?php rup_wpsco_handle_automation_submissions(); ?>
    </div>
    <?php
}

function rup_wpsco_handle_automation_submissions() {
    if (isset($_POST['real_action'], $_POST['subscription_id'])) {
        $id = absint($_POST['subscription_id']);
        if (get_post_type($id) !== 'subscrpt_order') {
            echo '<div class="notice notice-error"><p>Invalid subscription ID.</p></div>';
            return;
        }

        do_action('subscrpt_subscription_expired', $id);
        if (function_exists('rup_wpsco_build_payload')) {
            do_action('rup_wpsco_subscription_expired', rup_wpsco_build_payload($id));
        }

        echo '<div class="notice notice-success"><p>Real expiry simulated for Subscription ID ' . esc_html($id) . '.</p></div>';
        return;
    }

    if (isset($_POST['simulate_fake_activated'])) {
        do_action('rup_wpsco_subscription_activated', rup_wpsco_fake_payload('activated'));
        echo '<div class="notice notice-success"><p>Fake "Activated" event fired.</p></div>';
    }

    if (isset($_POST['simulate_fake_cancelled'])) {
        do_action('rup_wpsco_subscription_cancelled', rup_wpsco_fake_payload('cancelled'));
        echo '<div class="notice notice-success"><p>Fake "Cancelled" event fired.</p></div>';
    }

    if (isset($_POST['simulate_fake_status_changed'])) {
        do_action('rup_wpsco_subscription_status_changed', rup_wpsco_fake_payload('status_changed'));
        echo '<div class="notice notice-success"><p>Fake "Status Changed" event fired.</p></div>';
    }

    if (isset($_POST['simulate_fake_expired'])) {
        do_action('rup_wpsco_subscription_expired', rup_wpsco_fake_payload('expired'));
        echo '<div class="notice notice-success"><p>Fake "Expired" event fired.</p></div>';
    }
}

function rup_wpsco_fake_payload($type) {
    $order = json_decode('{
        "id":80,
        "order_key":"wc_order_XYZ123",
        "card_tax":"0",
        "currency":"GBP",
        "discount_tax":"0",
        "discount_to_display":"£0.00",
        "discount_total":"0",
        "fees":[],
        "shipping_tax":"0",
        "shipping_total":"0",
        "tax_totals":[],
        "total":"1.00",
        "total_refunded":0,
        "total_tax_refunded":0,
        "total_shipping_refunded":0,
        "total_qty_refunded":0,
        "remaining_refund_amount":"1.00",
        "shipping_method":"",
        "date_created":"2025-07-13 13:18:39",
        "date_modified":"2025-07-13 13:34:52",
        "date_completed":"2025-07-13 13:20:57",
        "date_paid":"2025-07-13 13:20:57",
        "customer_id":1,
        "created_via":"store-api",
        "customer_note":"",
        "billing_first_name":"Alice",
        "billing_last_name":"Taylor",
        "billing_company":"",
        "billing_address_1":"12 Rose Lane",
        "billing_address_2":"",
        "billing_city":"Manchester",
        "billing_state":"",
        "billing_postcode":"M1 4AB",
        "billing_country":"GB",
        "billing_email":"alice.taylor@example.com",
        "billing_phone":"",
        "shipping_first_name":"Alice",
        "shipping_last_name":"Taylor",
        "shipping_company":"",
        "shipping_address_1":"12 Rose Lane",
        "shipping_address_2":"",
        "shipping_city":"Manchester",
        "shipping_state":"",
        "shipping_postcode":"M1 4AB",
        "shipping_country":"GB",
        "payment_method":"wp_subscription_paypal",
        "payment_method_title":"PayPal",
        "status":"completed",
        "checkout_order_received_url":"https://example.com/order/80",
        "line_items":[
            {"product_id":64,"variation_id":0,"product_name":"Trial Item","quantity":1,"subtotal":"1","total":"1","subtotal_tax":"0","tax_class":"","tax_status":"taxable","product_sku":"","product_unit_price":"1.00"}
        ],
        "product_names":"Trial Item",
        "line_items_quantity":1,
        "_wc_order_attribution_referrer":"https://example.com/product/trial-item/",
        "_wc_order_attribution_user_agent":"Mozilla/5.0",
        "_wc_order_attribution_utm_source":"(direct)",
        "_wc_order_attribution_device_type":"Desktop",
        "_wc_order_attribution_source_type":"typein",
        "_wc_order_attribution_session_count":"1",
        "_wc_order_attribution_session_entry":"https://example.com/product/trial-item/",
        "_wc_order_attribution_session_pages":"10",
        "_wc_order_attribution_session_start_time":"2025-07-13 12:11:51"
    }', true);

    $payloads = [
        'activated' => [
            'subscription' => ['id' => 81, 'title' => 'Subscription #81', 'status' => 'active'],
            'order' => $order,
            'customer' => ['id' => 1, 'email' => 'alice.taylor@example.com', 'name' => 'Alice Taylor']
        ],
        'cancelled' => [
            'subscription' => ['id' => 81, 'title' => 'Subscription #81', 'status' => 'cancelled'],
            'order' => $order,
            'customer' => ['id' => 1, 'email' => 'robert.harris@example.com', 'name' => 'Robert Harris']
        ],
        'expired' => [
            'subscription' => ['id' => 81, 'title' => 'Subscription #81', 'status' => 'cancelled'],
            'order' => $order,
            'customer' => ['id' => 1, 'email' => 'isabelle.smith@example.com', 'name' => 'Isabelle Smith']
        ],
        'status_changed' => [
            'subscription' => ['id' => 81, 'title' => 'Subscription #81', 'status' => 'active'],
            'order' => $order,
            'customer' => ['id' => 1, 'email' => 'daniel.clarke@example.com', 'name' => 'Daniel Clarke'],
            'old_status' => 'cancelled',
            'new_status' => 'active'
        ]
    ];

    return $payloads[$type] ?? [];
}

