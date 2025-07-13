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

`add_action('rup_wpsco_subscription_status_changed',
'rup_wpsco_log_event_data');`

 

`// Log handler`

`function rup_wpsco_log_event_data($data) {`

`    if (!defined('WP_DEBUG') || !WP_DEBUG || !defined('WP_DEBUG_LOG') ||
!WP_DEBUG_LOG) {`

`        return;`

`    }`

 

`    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);`

`    $hook = current_filter();`

 

`    error_log("=== [RUP WPSCO Event Fired: {$hook}] ===");`

`    error_log(print_r($data, true));`

`    error_log("=== END ===\n");`

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

 
