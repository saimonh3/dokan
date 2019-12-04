;(function($) {
    var storeLists = {
        /**
         * Query holder
         *
         * @type object
         */
        query: {},

        /**
         * Init all the methods
         *
         * @return void
         */
        init: function() {
            $( '#dokan-store-listing-filter-wrap .toggle-view span' ).on( 'click', this.toggleView );
            $( '#dokan-store-listing-filter-wrap .dokan-store-list-filter-button, #dokan-store-listing-filter-wrap .dokan-icons ' ).on( 'click', this.toggleForm );

            // Build query string
            $( '#dokan-store-listing-filter-form-wrap .store-search-input' ).on( 'change', this.buildSearchQuery );

            // Submit the form
            $( '#dokan-store-listing-filter-form-wrap .apply-filter #apply-filter-btn' ).on( 'click', this.submitForm );

            const self = storeLists;
            const view = self.getLocal( 'dokan-layout' );

            if ( view ) {
                const toggleBtns = $( '.toggle-view span' );
                self.setView( view, toggleBtns );
            }

            // const params = self.getParams();

            // params.forEach( function( value ) {
            //     value.forEach(function(v,k) {
            //         console.log(k);
            //     })
            //         // self.setParam( key, value );
            // });
        },

        /**
         * Toggle store layout view
         *
         * @param  string event
         *
         * @return void
         */
        toggleView: function( event ) {
            const self = storeLists;
            const currentElement = $( event.target );
            const elements = currentElement.parent().find( 'span' );
            const view = currentElement.data( 'view' );

            self.setView( view, elements );
            self.addLocal( 'dokan-layout', view );
        },

        /**
         * Set grid or list view
         *
         * @param string view
         * @param array elements
         *
         * @return void
         */
        setView: function( view, elements ) {
            if ( typeof view === 'undefined'
                || view.length < 1
                || typeof elements === 'undefined'
                || elements.length < 1
                ) {
                return;
            }

            const listingWrap = $( '#dokan-seller-listing-wrap' );

            [...elements].forEach( function( value ) {
                const element = $( value );

                if ( view === element.data( 'view' ) ) {
                    element.addClass( 'active' );
                    listingWrap.addClass( view );
                } else {
                    element.removeClass( 'active' );
                    listingWrap.removeClass( element.data( 'view' ) );
                }
            });
        },

        /**
         * Toggle form
         *
         * @param  string event
         *
         * @return void
         */
        toggleForm: function( event ) {
            event.preventDefault();

            $('#dokan-store-listing-filter-form-wrap').slideToggle();
        },

        /**
         * Build Search Query
         *
         * @param  string event
         *
         * @return void
         */
        buildSearchQuery: function( event ) {
            storeLists.query.search = event.target.value;

            // const self = storeLists;

            // self.setParam( 'search', event.target.value );

            // console.log('load')
        },

        /**
         * Submit the form
         *
         * @param  string event
         *
         * @return void
         */
        submitForm: function( event ) {
            event.preventDefault();

            const queryString = $.param( storeLists.query );
            // const queryString = decodeURIComponent( $.param( storeLists.query ) );
            console.log(queryString);

            window.history.pushState( null, null, `?${queryString}` );
            window.location.reload();
        },

        /**
         * Add data into local storage
         *
         * @param string key
         * @param mix value
         *
         * @return void
         */
        addLocal: function( key, value ) {
            window.localStorage.setItem( key, value );
        },

        /**
         * Get data from local storage
         *
         * @param  string key
         *
         * @return mix
         */
        getLocal: function( key ) {
            return window.localStorage.getItem( key );
        },

        // setParam: function( key, value ) {
            // storeLists.query.key = value;
            // console.log(storeLists.query)
            // console.log({key: key})
            // console.log({value: value})
        // },

        // getParams: function() {
        //     const params = new URLSearchParams( location.search );
        //     const allParams = [];

        //     params.forEach( function( value, key ) {
        //         allParams.push( {
        //             [key]: value
        //         } );
        //     });

        //     return allParams;
        // }
    };

    if ( window.dokan ) {
        window.dokan.storeLists = storeLists;
        window.dokan.storeLists.init();
    }

})(jQuery);