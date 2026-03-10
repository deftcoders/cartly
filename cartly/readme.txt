=== Cartly – Conversion Focused WooCommerce Side Cart, Sticky Add to Cart & Upsells ===
Contributors: codelitix
Tags: woocommerce, cart, floating cart, cart drawer, side cart, ajax cart, upsells, free shipping bar, sticky cart
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
WC requires at least: 7.0
WC tested up to: 9.6
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Boost WooCommerce conversions with a modern AJAX side cart drawer, floating cart, sticky add to cart bar, smart upsells, free shipping progress, and behavior-based triggers — all customizable with a live visual builder.

== Description ==

**Cartly** is a premium WooCommerce cart plugin built to increase conversions, reduce cart abandonment, and deliver a polished shopping experience — with zero configuration required after activation.

The cart drawer activates **immediately after installation**. Visit any product page, click Add to Cart, and watch it work.

= 🛒 AJAX Side Cart Drawer =

* Opens instantly when a product is added to cart — no page reload
* Quantity controls with live subtotal update
* Remove items without leaving the page
* Direct checkout button inside the drawer
* "View Cart" and "Continue Shopping" buttons in footer
* Live cart item count badge on the floating button
* Swipe to close on mobile

= ⭐ Sticky Add-to-Cart Bar =

* Appears on single product pages when the native Add to Cart button scrolls above the viewport
* Shows product thumbnail, name, price, optional quantity selector, and Add to Cart button
* Clicking adds to cart and immediately opens the drawer
* Variable products display a "Select Options" link that scrolls to the native form
* Fully configurable — enable/disable, custom button text, show/hide quantity

= 🚀 Behavior & Open Triggers =

* Auto-open on Add to Cart — recommended for highest conversion
* Scroll trigger — opens after the customer scrolls a configurable percentage of the page
* Delay trigger — opens automatically after a configurable number of seconds
* Manual mode — floating button click only
* Exit intent — opens when the cursor moves toward the browser address bar (desktop)
* Configurable exit intent delay

= 🎯 Smart Upsell Engine =

* Related product suggestions — powered automatically by WooCommerce
* Cross-sell products — configured per product in WooCommerce
* Same-category recommendations
* Frequently Bought Together mode
* Rule-based upsells — specific products appear when cart total exceeds a threshold
* One-click add to cart directly from inside the drawer
* Optional sale/featured badges and star ratings
* Configurable section title and card/row layouts
* Set maximum number of products shown

= 🏆 Multi-Tier Cart Goals =

* Up to 3 milestone goals on a visual progress track
* Animated icon unlocks when each amount is reached
* Progress message updates dynamically as items are added
* Example: $50 → 🚚 Free Shipping · $100 → 🎁 Free Gift · $150 → 💰 10% Off

= ⏱ Countdown Urgency Timer =

* Three modes: Cart Reserved, Discount Expires, Free Shipping Ends
* Configurable duration (minutes)
* Resets each time the drawer opens
* Colour-coded per mode (red / amber / green)

= 🎁 Free Shipping Progress Bar =

* Fills as the customer adds items toward the threshold
* Customizable messages with {amount} placeholder support
* Animated success state when goal is reached

= 💰 Coupons Inside the Cart =

* Collapsible coupon field in the cart footer
* Apply and remove coupons without leaving the cart
* Supports all standard WooCommerce coupon types
* Clear validation and success messages

= 📢 Cart Notice Banner =

* Optional announcement bar at the top of the drawer
* Custom text and background color

= 🛡️ Trust Badges =

* Optional secure checkout message in the cart footer
* Fully customizable text

= 🎨 Visual Builder with Live Preview =

* 6 fully designed style presets (Modern, Minimal, Dark, Glass, Gold/Luxury, Convert)
* Clicking a preset updates all color pickers instantly
* Full color controls: primary, secondary, background, text, button, shipping bar
* Typography: font size, font weight, price font size
* Layout: drawer width, border radius, shadow style
* Animation style selector
* Device switcher in preview panel
* Custom CSS field

= 📱 Mobile Experience =

* Bottom sheet layout on screens <= 600px — slides up from the bottom
* Swipe down to close gesture
* Floating button repositioned higher on mobile to avoid navigation overlap
* Mobile-specific auto-open toggle

= 🔧 Advanced & Display Controls =

* Load only on WooCommerce pages (shop, product, cart)
* Skip loading when cart is empty
* Hide on specific pages by ID
* WooCommerce cart fragments compatibility toggle
* Disable animations option
* AJAX refresh on cart update

= 🔒 Secure & Performant =

* Nonce verification on every AJAX request
* Capability checks (manage_woocommerce) on all admin save and reset actions
* All inputs sanitized, all outputs escaped
* Minified CSS and JS in production; full files loaded automatically when SCRIPT_DEBUG is on
* Assets excluded from checkout and admin pages
* HPOS (High Performance Order Storage) compatible
* WooCommerce Blocks compatible
* Fully works for both guests and logged-in users

== Installation ==

1. Upload the `cartly` folder to `/wp-content/plugins/`
2. Activate the plugin via WordPress Admin → Plugins
3. You will be taken to the **Cartly Welcome screen** — from there click **Customize Cart** to go to settings, or **View Live Demo** to see it on your store. The cart is already live.

== Frequently Asked Questions ==

= Does it work immediately after activation? =
Yes. No configuration is required. After activation you are taken to the Cartly Welcome screen — click Customize Cart to configure, or visit any product page directly to see the cart already working.

= Does it require any theme edits or shortcodes? =
No. Cartly works entirely through standard WordPress hooks. No template files need to be edited and no shortcodes need to be placed.

= Is it HPOS compatible? =
Yes. Cartly declares compatibility with WooCommerce High Performance Order Storage (custom_order_tables).

= Does it work with WooCommerce Blocks? =
Yes. Cartly declares compatibility with cart_checkout_blocks and is excluded from the native WooCommerce checkout page automatically.

= Does it work with all themes? =
Yes. Cartly injects its HTML via wp_footer and does not depend on any theme template hooks.

= Does it support guests (non-logged-in users)? =
Yes. All AJAX cart endpoints work for both logged-in users and guests.

= Does the sticky add-to-cart bar work with page builders? =
The bar detects the native WooCommerce form.cart element. Most standard themes and page builders include this. Contact support if you use a heavily customised product template.

= Can I restrict which pages the cart loads on? =
Yes. The Advanced tab has a "Load Only on WooCommerce Pages" toggle and a field to hide on specific page IDs.

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
