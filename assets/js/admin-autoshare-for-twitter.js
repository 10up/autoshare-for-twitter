(function($) {
	'use strict';

	// Dismiss migrate to Twitter API v2 notice
	$( function() {
        $( '.ast_notice' ).on( 'click', '.notice-dismiss', function( event, el ) {
            var $notice = $(this).parent('.notice.is-dismissible');
            var dismiss_url = $notice.attr('data-dismiss-url');
            if ( dismiss_url ) {
                $.get( dismiss_url );
            }
        });
    } );
})(jQuery);
