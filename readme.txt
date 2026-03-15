=== DeftCoders – Cartly Ajax Side Cart for WooCommerce ===
Contributors: deftcoders
Tags: woocommerce cart, ajax cart, side cart, mini cart, cart drawer
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
WC requires at least: 7.0
WC tested up to: 9.6
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern AJAX side cart drawer for WooCommerce with upsells, free shipping progress, sticky add to cart, and behavior-based triggers.

== Description ==

DeftCoders – Ajax Side Cart for WooCommerce adds a modern sliding cart drawer to your WooCommerce store.

Customers can view and edit their cart instantly without reloading the page, improving checkout flow and conversion rates. The cart drawer activates immediately after activation — no configuration required.

Perfect for:

* WooCommerce stores looking for a better cart experience
* Modern cart drawer UX
* Mobile shopping
* Faster checkout flow

= AJAX Side Cart Drawer =

* Opens instantly when a product is added to cart — no page reload
* Quantity controls with live subtotal update
* Remove items without leaving the page
* Direct checkout button inside the drawer
* "View Cart" and "Continue Shopping" buttons in footer
* Live cart item count badge on the floating button
* Swipe to close on mobile

= Sticky Add-to-Cart Bar =

* Appears on single product pages when the native Add to Cart button scrolls above the viewport
* Shows product thumbnail, name, price, optional quantity selector, and Add to Cart button
* Clicking adds to cart and immediately opens the drawer
* Variable products display a "Select Options" link that scrolls to the native form
* Fully configurable — enable/disable, custom button text, show/hide quantity

= Smart Upsell Engine =

* Related product suggestions powered automatically by WooCommerce
* Cross-sell products configured per product in WooCommerce
* Same-category recommendations
* Frequently Bought Together mode
* One-click add to cart directly from inside the drawer
* Optional sale/featured badges and star ratings
* Configurable section title and card/row layouts

= Multi-Tier Cart Goals =

* Up to 3 milestone goals on a visual progress track
* Animated icon unlocks when each amount is reached
* Progress message updates dynamically as items are added
* Example: $50 → Free Shipping · $100 → Free Gift · $150 → 10% Off

= Free Shipping Progress Bar =

* Fills as the customer adds items toward the threshold
* Customizable messages with {amount} placeholder support
* Animated success state when goal is reached

= Behavior and Open Triggers =

* Auto-open on Add to Cart — recommended for highest conversion
* Scroll trigger — opens after the customer scrolls a configurable percentage of the page
* Delay trigger — opens automatically after a configurable number of seconds
* Manual mode — floating button click only
* Exit intent — opens when the cursor moves toward the browser address bar (desktop)

= Countdown Urgency Timer =

* Three modes: Cart Reserved, Discount Expires, Free Shipping Ends
* Configurable duration (minutes)
* Resets each time the drawer opens
* Colour-coded per mode

= Coupons Inside the Cart =

* Collapsible coupon field in the cart footer
* Apply and remove coupons without leaving the cart
* Supports all standard WooCommerce coupon types

= Visual Builder with Live Preview =

* 6 fully designed style presets (Modern, Minimal, Dark, Glass, Gold/Luxury, Convert)
* Full color controls: primary, secondary, background, text, button, shipping bar
* Typography: font size, font weight, price font size
* Layout: drawer width, border radius, shadow style
* Custom CSS field

= Mobile Experience =

* Bottom sheet layout on screens 600px and below — slides up from the bottom
* Swipe down to close gesture
* Floating button repositioned higher on mobile to avoid navigation overlap

= Secure and Performant =

* Nonce verification on every AJAX request
* Capability checks on all admin save and reset actions
* All inputs sanitized, all outputs escaped
* Minified CSS and JS in production
* HPOS (High Performance Order Storage) compatible
* WooCommerce Blocks compatible

== Installation ==

1. Upload the `cartly` folder to `/wp-content/plugins/`
2. Activate the plugin via WordPress Admin → Plugins
3. You will be taken to the welcome screen — click "Customize Cart" to configure, or visit any product page to see the cart already working.

== Frequently Asked Questions ==

= Does this plugin work with any theme? =
Yes, it works with most WooCommerce compatible themes. Cartly injects its HTML via wp_footer and does not depend on any theme template hooks.

= Does it support AJAX add to cart? =
Yes, the cart updates instantly without page reload. All cart operations use WooCommerce's standard AJAX and fragment APIs.

= Is it mobile friendly? =
Yes, the side cart drawer uses a bottom sheet layout on mobile devices (600px and below) with swipe-to-close support.

= Does it work immediately after activation? =
Yes. No configuration is required. After activation you are taken to the welcome screen — click Customize Cart to configure, or visit any product page directly to see the cart already working.

= Does it require any theme edits or shortcodes? =
No. The plugin works entirely through standard WordPress hooks. No template files need to be edited.

= Is it HPOS compatible? =
Yes. The plugin declares compatibility with WooCommerce High Performance Order Storage (custom_order_tables).

= Does it work with WooCommerce Blocks? =
Yes. The plugin declares compatibility with cart_checkout_blocks and is excluded from the native WooCommerce checkout page automatically.

= Does it support guests (non-logged-in users)? =
Yes. All AJAX cart endpoints work for both logged-in users and guests.

= Can I restrict which pages the cart loads on? =
Yes. The Advanced tab includes a "Load Only on WooCommerce Pages" toggle and a field to hide on specific page IDs.

== Screenshots ==

1. AJAX side cart drawer with free shipping progress bar and coupon field
2. Admin visual builder — Cart tab with live preview and style presets
3. Smart upsells inside the cart — "You Might Also Like" section with one-click add buttons
4. Multi-tier cart goals — Free Shipping, Bonus Discount, and Free Gift milestones with animated progress
5. Countdown urgency timer in the cart drawer — Discount Expires mode
6. Free shipping progress bar — success state with animated unlock message
7. Rewards and Goals admin panel — 3-tier milestone goals and countdown timer settings
8. Upsells configuration panel — source strategy, layout style, and display options
9. Behavior triggers panel — auto-open, scroll, exit intent, and Sticky Add-to-Cart settings
10. Design controls — layout, typography, and checkout button style settings
11. Sticky add-to-cart bar on a product page — appears on scroll with quantity selector
12. Mobile bottom sheet layout with free shipping bar and swipe-to-close
13. Mobile settings panel — bottom sheet, floating button, auto-open, and sticky summary bar
14. Advanced settings — page visibility, performance toggles, custom CSS, and compatibility declarations
15. Welcome screen shown after activation — quick-start guide and direct links

== Changelog ==

= 1.0.0 =
* Initial release
* AJAX-powered floating side cart drawer
* Sticky Add-to-Cart bar on product pages
* Smart upsell engine (related, cross-sell, category, rule-based, frequently bought together)
* Free shipping progress bar
* Multi-tier cart goals with animated progress track
* Countdown urgency timer (3 modes)
* Behavior triggers: auto-open, scroll, delay, exit intent
* Mobile bottom sheet layout with swipe-to-close
* Coupons inside the cart
* Trust badges and announcement banner
* Visual live builder with 6 style presets
* Full color, typography, shadow, and layout controls
* Custom CSS field
* WooCommerce HPOS and Blocks compatibility
* Nonce verification and capability checks on all AJAX endpoints
* Minified assets with SCRIPT_DEBUG support
