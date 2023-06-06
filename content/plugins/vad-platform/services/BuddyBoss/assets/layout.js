window.vad = window.vad || {};

( function( exports, $ ) {

    /**
     * We use this so that the buddyboss pages look good when using yootheme builder
     * hacky but it works
     *
     * @type {Object}
     */
    exports.buddyboss_layout = {

        /**
         * [start description]
         *
         * @return {[type]} [description]
         */
        start: function () {
            // help with yootheme css
            $('.tm-page').attr('id', 'page');
            $('.tm-header,.tm-header-mobile').addClass('site-header');

            //disable dropdown on cart
            $('#header-notifications-dropdown-elem.notification-wrap').removeClass( 'menu-item-has-children' );
            $('.header-cart-link-wrap.cart-wrap.menu-item-has-children').removeClass( 'menu-item-has-children' );
        },


    };

    $( document ).ready(function() {
        exports.buddyboss_layout.start();
    });



} )( vad, jQuery );
