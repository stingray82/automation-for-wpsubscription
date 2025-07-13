automation-for-wpsubscription
=============================

Automation Bridge for WPSubscritpion

 

This allows automators like Flowmattic to hook into wpsubscription

 

This is an alpha release although it needs testing:
---------------------------------------------------

 

**Install the plugin as usual**

**Ensure WP Debug is On**  
In your `wp-config.php`, confirm these lines exist:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false); // optional but recommended
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

 

**Add the following Snippet to your functions.php or snippet plugin​**

 

`// Enable logging for all custom WPSubscription events`

`add_action('rup_wpsco_subscription_activated', 'rup_wpsco_log_event_data');`

`add_action('rup_wpsco_subscription_expired', 'rup_wpsco_log_event_data');`

`add_action('rup_wpsco_subscription_cancelled', 'rup_wpsco_log_event_data');`

 

`// Log handler`

`function rup_wpsco_log_event_data($data) {`

`if (!defined('WP_DEBUG') || !WP_DEBUG || !defined('WP_DEBUG_LOG') ||
!WP_DEBUG_LOG) {`

`return;`

`}`

 

`$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);`

`$hook = current_filter();`

 

`error_log("=== [RUP WPSCO Event Fired: {$hook}] ===");`

`error_log(print_r($data, true));`

`error_log("=== END ===\n");`

`}`

 

**Trigger Subscription Events**  
Perform actions that cause:

-   Subscription activation

-   Expiration

-   Cancellation

-   Status change

**Check the Log**  
Look in:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
wp-content/debug.log
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You’ll see output like:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
=== [RUP WPSCO Event Fired: rup_wpsco_subscription_activated] ===
Array
(
    [subscription] => Array
        (
            [id] => 1234
            [title] => Monthly Plan
            [status] => publish
        )

    [order] => Array
        (
            ...
        )

    [customer] => Array
        (
            ...
        )
)
=== END ===
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

 

 

Available Hooks

rup_wpsco_subscription_activated

rup_wpsco_subscription_expired

rup_wpsco_subscription_cancelled

<br>Payload Example:<br>
------------------------

Payload

[

'subscription' =\> [ /\* subscription data \*/ ],

'order' =\> [ /\* detailed order info \*/ ],

'customer' =\> [ /\* customer info \*/ ],

// Optional only for \`status_changed\`

'old_status' =\> 'pending',

'new_status' =\> 'active'

]

 

 

Example with Dummy Data:

[

'subscription' =\> [

'id' =\> 1234,

'title' =\> 'Premium Plan - Monthly',

'status' =\> 'active'

],

 

'order' =\> [

'id' =\> 9876,

'order_key' =\> 'wc_order_abcd1234',

'card_tax' =\> '5.00',

'currency' =\> 'USD',

'discount_tax' =\> '0.00',

'discount_to_display' =\> '\$10.00',

'discount_total' =\> '10.00',

'fees' =\> [],

'shipping_tax' =\> '2.50',

'shipping_total' =\> '5.00',

'tax_totals' =\> [

'VAT' =\> [

'label' =\> 'VAT',

'amount' =\> '5.00'

]

],

'total' =\> '100.00',

'total_refunded' =\> '0.00',

'total_tax_refunded' =\> '0.00',

'total_shipping_refunded' =\> '0.00',

'total_qty_refunded' =\> '0',

'remaining_refund_amount' =\> '0.00',

'shipping_method' =\> 'Flat rate',

'date_created' =\> '2025-07-13 09:30:00',

'date_modified' =\> '2025-07-13 09:31:00',

'date_completed' =\> '2025-07-13 09:35:00',

'date_paid' =\> '2025-07-13 09:32:00',

'customer_id' =\> 15,

'created_via' =\> 'checkout',

'customer_note' =\> 'Please deliver after 5 PM',

 

// Billing

'billing_first_name' =\> 'John',

'billing_last_name' =\> 'Doe',

'billing_company' =\> 'Acme Inc',

'billing_address_1' =\> '123 Main St',

'billing_address_2' =\> 'Apt 4B',

'billing_city' =\> 'New York',

'billing_state' =\> 'NY',

'billing_postcode' =\> '10001',

'billing_country' =\> 'US',

'billing_email' =\> 'john\@example.com',

'billing_phone' =\> '555-123-4567',

 

// Shipping

'shipping_first_name' =\> 'John',

'shipping_last_name' =\> 'Doe',

'shipping_company' =\> '',

'shipping_address_1' =\> '123 Main St',

'shipping_address_2' =\> 'Apt 4B',

'shipping_city' =\> 'New York',

'shipping_state' =\> 'NY',

'shipping_postcode' =\> '10001',

'shipping_country' =\> 'US',

 

// Payment

'payment_method' =\> 'stripe',

'payment_method_title' =\> 'Credit Card (Stripe)',

 

'status' =\> 'completed',

'checkout_order_received_url' =\>
'https://example.com/checkout/order-received/9876/',

 

// Line items

'line_items' =\> [

[

'product_id' =\> 101,

'variation_id' =\> 0,

'product_name' =\> 'Premium Membership',

'quantity' =\> 1,

'subtotal' =\> '100.00',

'total' =\> '100.00',

'subtotal_tax' =\> '5.00',

'tax_class' =\> '',

'tax_status' =\> 'taxable',

'product_sku' =\> 'PREM-MONTHLY',

'product_unit_price' =\> '100.00',

]

],

'product_names' =\> 'Premium Membership',

'line_items_quantity' =\> 1,

 

// WC Attribution data (WooCommerce 8.5.1+ only)

'_wc_order_attribution_referrer' =\> 'https://google.com',

'_wc_order_attribution_user_agent' =\> 'Mozilla/5.0',

'_wc_order_attribution_utm_source' =\> 'google',

'_wc_order_attribution_device_type' =\> 'desktop',

'_wc_order_attribution_source_type' =\> 'organic',

'_wc_order_attribution_session_count' =\> '1',

'_wc_order_attribution_session_entry' =\> '/pricing',

'_wc_order_attribution_session_pages' =\> '3',

'_wc_order_attribution_session_start_time' =\> '2025-07-13T09:25:00',

],

 

'customer' =\> [

'id' =\> 15,

'email' =\> 'john\@example.com',

'name' =\> 'John Doe'

]
