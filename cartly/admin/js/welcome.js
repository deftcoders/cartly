/**
 * codelitix – Cartly Welcome Screen JS
 * Animate steps as user reads through the page
 */
(function ($) {
	'use strict';
	// Animate steps 2 and 3 after delays
	setTimeout(
		function () {
			$( '[data-step="1"]' ).addClass( 'cw-onboard__step--done' );
			$( '[data-step="2"]' ).addClass( 'cw-onboard__step--active' );
		},
		1200
	);
	setTimeout(
		function () {
			$( '[data-step="2"]' ).addClass( 'cw-onboard__step--done' ).removeClass( 'cw-onboard__step--active' );
			$( '[data-step="3"]' ).addClass( 'cw-onboard__step--active' );
		},
		2400
	);
})( jQuery );
