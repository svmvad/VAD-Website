
window.vad = window.vad || {};

( function( exports, $ ) {

    /**
     * [Zoom description]
     *
     * @type {Object}
     */
    exports.common = {
        start: function () {
            this.setupGlobals();

            this.setupView();
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

        },

        /**
         * [setupView description]
         */
        setupView: function () {
            UIkit.icon.add('navbar-toggle-icon','<svg width="20" height="20" viewBox="0 0 20 20"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg>');
        },
    }

    $( document ).ready(function() {
        exports.common.start();
    });


} )( vad, jQuery );
