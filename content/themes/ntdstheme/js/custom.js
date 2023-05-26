//https://github.com/uikit/uikit-site/blob/feature/js-utils/docs/pages/javascript-utilities.md

window.ntdst = window.ntdst || {};

( function( exports, $ ) {

    /**
     *
     *
     * @type {Object}
     */
    exports.web = {
        /**
         * [start description]
         *
         * @return {[type]} [description]
         */
        start: function () {
            this.setupGlobals();
            this.addListeners();
        },

        /**
         * [setupGlobals description]
         *
         * @return {[type]} [description]
         */
        setupGlobals: function ()
        {

        },

        /**
         * [addListeners description]
         */
        addListeners: function () {

           // UIkit.on( $('a'), 'click', this.doSomething.bind( this ) );

        },
    }

    UIkit.util.ready(function ()
    {
        exports.web.start();
    });


} )( ntdst, UIKit.util.$ );


