
window.vad = window.vad || {};

( function( exports, $ ) {

    /**
     *
     *
     * @type {Object}
     */
    exports.Filter_Common = {

        /**
         * [start description]
         *
         * @return {[type]} [description]
         */
        start: function () {
            this.setupGlobals();

            // Listen to events ("Add hooks!")
            this.addListeners();
        },

        /**
         * [setupGlobals description]
         *
         * @return {[type]} [description]
         */
        setupGlobals: function ()
        {
            this.filters = {};

            if( bs_data.query !== '[]' ) {
                const tax = JSON.parse( bs_data.taxonomies );
                if( Object.keys(tax).length > 0 ) {
                    this.filters = JSON.parse(bs_data.query, (key,value) => {
                        if(key!=='' && tax.hasOwnProperty(key)) {
                            return value.split(','); //.map( val => tax[key]['terms'][val] );
                        }
                        return value;
                    });
                }
            }

            if( this.filters!=null && Object.keys(this.filters).length  ) {
                Object.entries(this.filters).forEach(([key, value]) => {
                    this.editFilterLabels( key );
                });
            }



        },

        /**
         * [addListeners description]
         */
        addListeners: function () {

            /** Page actions **/
            $(document).on('click', '.filter-link, .remove-filter-link',  this.handleFilterClick.bind(this));

            $(document).on('keyup', 'form.uk-search .uk-search-input',  this.handleKeyUp.bind(this));
            $(document).on('click', 'form.uk-search .uk-search-icon',  this.handleSearchClick.bind(this));

        },

        handleKeyUp:function(e) {
            if (13 === e.which) {
                e.preventDefault();
                this.filterProducts();
                this.__updateUrl( this.filters );
            }
        },


        handleSearchClick :function(e) {
            e.preventDefault();

            this.filterProducts();
            this.__updateUrl( this.filters );
        },

        handleFilterClick :function(e) {
            e.preventDefault();

            const button = $(e.currentTarget);

            if( button.hasClass( 'filter-link' ) ) {
                $toggle = $('.ntdst-filter .ntdst-categories ul').not('[hidden]').siblings('H3');

                UIkit.toggle( $toggle[0] ).toggle();
            }

            this.editFilterInputs(button.data('cat'), button.data('term'));
            this.filterProducts();
            this.__updateUrl( this.filters );
        },

        editFilterInputs :function(category, term) {

            const currentFilters = this.filters.hasOwnProperty(category)  ? this.filters[category]:[];
            const newFilter = term.toString();

            if (currentFilters.includes(newFilter)) {
                const i = currentFilters.indexOf(newFilter);
                currentFilters.splice(i, 1);
                if( currentFilters.length===0 ) {
                    delete this.filters[category];
                }
                else this.filters[category] = currentFilters;

            } else {
                if( ! this.filters.hasOwnProperty(category) ) {
                    const obj = { [category] : [newFilter] }
                    this.filters = Object.assign( this.filters, obj );
                }
                else this.filters[category].push( newFilter );
            }


            this.editFilterLabels( category );

        },

        editFilterLabels: function( category ) {

            const tax = JSON.parse( bs_data.taxonomies );

            $('div#tax-'+category+' .filter-active').empty();
            $('div#tax-'+category+' a.filter-link').removeClass('activeFilter');

            if( this.filters.hasOwnProperty(category) ) {
                this.filters[category].forEach( function( item ) {
                    $('div#tax-'+category+' a.filter-link[data-term="'+item+'"]').addClass('activeFilter');
                    $('div#tax-'+category+' .filter-active' ).append('<a class="uk-button uk-button-primary uk-button-small remove-filter-link" data-cat="'+category+'" data-term="'+item+'">'+ tax[category]['terms'][item] +'<span class="uk-margin-small-left uk-icon" uk-icon="close"></span></a>');
                } );
            }
        },

        filterProducts: function( data ) {

            $('#result-count').html('');
            $('.ntdst-filter-results').html('');
            //if( $('form.uk-search input[name="s"]').val()==='') return;

            if( !data || typeof data !== 'object' ) {
                data = {};
            }


            jQuery.ajax({
                type: 'POST',
                url: bs_data.ajaxurl,
                context: this,
                data: Object.assign(data, {
                    action: bs_data.action,
                    filter: this.filters,
                    s: this.__getSearch(),
                    post: bs_data.posttype,
                    _ajax_list_nonce:bs_data.ajaxnonce
                } ),
                success: function(res) {
                    res = JSON.parse(res);
                    if( res.html !=='' ){
                        $('#result-count').html(res.total);
                        $('.ntdst-filter-results').html(res.html);
                    }

                },
                error: function(err) {
                    console.error(err);
                }
            })
        },

        __getSearch: function(  ) {
            return $('form.uk-form input[name="s"]').val();
        },

        __updateUrl: function( param ) {

            let qry = {};
            let query = '?';
            const tax = JSON.parse( bs_data.taxonomies );

            Object.assign( qry, param  );
            Object.assign( qry, { s: this.__getSearch() }  );

            Object.entries(qry).forEach(([key, value]) => {
                if(value.toString()!=='') query+=(query==='?'?'':"&")+key+"="+value.toString();
            });

            if (history.pushState) {
                query = query.replace(/ /g, '-').toLowerCase(); // we use cat labels, we need to do this
                const newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + query;
                window.history.pushState({path:newurl},'',newurl);
            }

        }
    };

    $( document ).ready(function() {
        exports.Filter_Common.start();
    });


} )( vad, jQuery );
