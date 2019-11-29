;(function($) {
    var storeLists = {
        /**
         * Query holder
         *
         * @type object
         */
        query: {},

        /**
         * Items array holder
         *
         * @type array
         */
        itemsArray: [],

        /**
         * Init all the methods
         *
         * @return void
         */
        init: function() {
            $( '#dokan-store-listing-filter-wrap .toggle-view span' ).on( 'click', this.toggleView );
            $( '#dokan-store-listing-filter-wrap .dokan-store-list-filter-button' ).on( 'click', this.toggleForm );
            $( '#dokan-store-listing-filter-form-wrap .store-lists-category .category-input' ).on( 'click', this.toggleCategory );
            $( '#dokan-store-listing-filter-form-wrap .store-lists-category .category-box ul li' ).on( 'click', this.selectCategory );

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
         * Toggle category
         *
         * @return void
         */
        toggleCategory: function() {
            $( '.store-lists-category .category-box' ).slideToggle();
        },

        /**
         * Select Category
         *
         * @param  string event
         *
         * @return void
         */
        selectCategory: function( event ) {
            const item = $( event.target );
            const currentItem = item.text();
            const categoryHolder = $( '.category-items' );
            const self = storeLists;

            item.toggleClass('dokan-btn-theme');

            if ( ! self.itemsArray.includes( currentItem ) ) {
                self.itemsArray.push( currentItem );
            } else {
                itemToRemove = self.itemsArray.indexOf( currentItem );
                self.itemsArray.splice( itemToRemove, 1 );
            }

            // building query string
            self.query.categories = self.itemsArray;

            const itemString = self.itemsArray.join( ', ' );

            if ( itemString.length > 15 ) {
                categoryHolder.text( ' ' ).append( itemString.substr( 0, 15 ) + '...' );
            } else {
                categoryHolder.text( ' ' ).append( self.itemsArray.join( ', ' ) );
            }
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
        }
    };

    /**
     * Lets run
     *
     * @return void
     */
    storeLists.init();
})(jQuery);