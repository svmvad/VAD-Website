window.vad = window.vad || {};

( function( exports, $ ) {

    /**
     * [Zoom description]
     *
     * @type {Object}
     */
    exports.postcode = {

        /**
         * [start description]
         *
         * @return {[type]} [description]
         */
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
            this.choices = null;
        },

        /**
         * [addListeners description]
         */
        addListeners: function () {
            $(document).on('click', 'form.is-archive input[type="radio"]',  this.handleRadioClick.bind(this));
            $(document).on('click', 'form.is-archive button[type="submit"]',  this.handleSubmitClick.bind(this));
        },


        handleSubmitClick :function(e) {
            e.preventDefault();

            exports.Filter_Common.filterProducts(
                { postcode: this.choices.getValue(true) }
            );

            exports.Filter_Common.__updateUrl(
                Object.assign(
                    { postcode:this.choices.getValue(true) },
                    exports.Filter_Common.filters
                )
            );
        },


        handleRadioClick :function(e) {
            e.preventDefault();
            window.location.assign('/doorverwijsgids/' + $(e.currentTarget).attr('id') + '/' + document.location.search );
            return false;
        },

        setupView: function() {

            this.choices = new Choices('#geo', {
                allowHTML: true,
                placeholder: true,
                removeItemButton: true,
            });

        }

    }

    $( document ).ready(function() {
        exports.postcode.start();
    });


} )( vad, jQuery );
