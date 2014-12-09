/**
 * --------------------------------------------------------------------
 * Fits elements into container in a nice grid fashion.
 * --------------------------------------------------------------------
 */
( function ( window, $, $window ) {

  $.Grid = function( options, element, callback ) {
    this.$grid        = $( element );
    this.$parent      = this.$grid.parent();
    this.$elements    = this.$grid.children();
    this.callback     = callback;

    this.options = $.extend( {}, $.Grid.settings, options );

    this._disabled = false;

    this._init();

    if ( $window.width() <= this.options.minWidth ) {
      this.disable();
      this._fadeIn();
    } else {
      this._decideGrid();
      this.calculateSizes();
      this.layout( this.$elements, callback );
      this._fadeIn();
    }

  };

  $.Grid.settings = {
    gutterHeight: 15,
    gutterWidth: 15,
    minWidth: 768,
    rows: 3,
    columns: 4
  }

  $.Grid.prototype = {

    resize: function () {
      if ( $window.width() <= this.options.minWidth ) {
        this.disable();
        return;
      } else {
        this.enable();
      }

      this._decideGrid();
      this.calculateSizes();
      this.layout( this.$elements, this.callback );
    },

    _init: function () {
      /**
       * Set index number for grid elements.
       */
      var index = 0;
      this.$elements.each( function () {
        $(this).data( 'grid-index', index++ );
      });
    },

    getRowItems: function ( row ) {
      return this.$elements.filter( ':nth-child(' + this.gridRows + 'n + 1)' );
    },

    /**
     * Sets grid size depending on screen size.
     */
    _decideGrid: function () {
      var screenWidth = $window.width();
      var screenHeight = $window.height();

      if ( screenWidth <= 540 ) {
        this.gridColumns = 2;
        this.gridRows = 2;
      } else if ( screenWidth <= 900 ) {
        this.gridColumns = 3;
        this.gridRows = 3;
      } else {
        this.gridColumns = this.options.columns;
        this.gridRows = this.options.rows;
      }
    },

    calculateSizes: function () {
      var parentHeight = this.$parent.height(),
          parentWidth  = this.$parent.width(),
          gutterHeight = this.options.gutterHeight,
          gutterWidth  = this.options.gutterWidth;

      this.sizes = {
        itemHeight:   Math.floor( ( parentHeight - ( ( this.gridRows - 1 ) * gutterHeight ) ) / this.gridRows ),
        itemWidth:    Math.floor( ( parentWidth - ( ( this.gridColumns - 1 ) * gutterWidth ) ) / this.gridColumns )
      }
    },

    calculateItemPosition: function ( $item ) {
      var index   = $item.data( 'grid-index' );

      var position = {
        topIndex:   index % this.gridRows,
        leftIndex:  Math.ceil( (index + 1) / this.gridRows ) - 1,
        top:        0,
        left:       0
      }

      if ( index == 0 ) {
        return position;
      }

      position.top = this.sizes.itemHeight * position.topIndex + ( this.options.gutterHeight * position.topIndex ) ;
      position.left = this.sizes.itemWidth * position.leftIndex + ( this.options.gutterWidth * position.leftIndex ) ;

      return position;
    },

    disable: function () {

      if ( true === this._disabled ) {
        return;
      }

      this.$elements.css({
          top: '',
          left: '',
          width: '',
          height: '',
          position: 'relative'
      });
      this._disabled = true;

    },

    enable: function () {

      if ( false === this._disabled ) {
        return;
      }

      this.$elements.css({
        position: 'absolute'
      })
      this._disabled = false;

    },

    layout : function ( $items, callback ) {

      if ( this._disabled ) {
        return;
      }

      var self = this;
      var gridWidth;

      $items.each( function ( i ) {

        var pos = self.calculateItemPosition( $( this ) );

        $( this ).css({
          top: pos.top,
          left: pos.left,
          width: self.sizes.itemWidth,
          height: self.sizes.itemHeight
        });

        if ( i == $items.length - 1 ) {
          gridWidth = pos.left + self.sizes.itemWidth + self.options.gutterWidth;
        }

      });

      this.$grid.css( 'width', gridWidth );

      if ( typeof callback == 'function' ) {
        callback.call( self );
      }
    },

    _fadeIn: function () {

      var self = this;

      setTimeout( function () {

        self.$grid.animate( {
          opacity: 1
        }, 1000 );

      }, 100 );


    }

  }

  $.fn.grid = function( options, callback ) {
   this.data( 'grid', new $.Grid( options, this, callback ) );
   return this;
  }

}( window, jQuery, jQuery(window) ) );