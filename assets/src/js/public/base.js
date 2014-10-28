var WP_Generous = (function( $ ) {
	var wpg = this;

	this.init = function() {
		$(function() {
			wpg.ready();
		});

		return wpg;
	};

	this.ready = function() {
		wpg.pagination = new pagination();
	};

	this.pagination = function() {
		var p = this,
				loading = false,
				el = {
					sliders: '.generous-sliders',
					container: '.generous-pagination',
					button: '.generous-load-more',
				};

		this.init = function() {
			p.loadMore = new p.loadMore();
		};

		this.loadMore = function() {
			var lm = this,
					$button;

			this.init = function() {
				lm.prepare();
			};

			this.prepare = function() {
				if( $( el.button, el.container ).length > 0 ) {
					$button = $( el.button, el.container );
					$button.addClass('no-ajaxy');
					$button.on( 'click', function( e ) {
						e.preventDefault();
						lm.get( $(this).attr('href') );
					});
				}
			};

			this.get = function( url ) {
				if( false === loading ) {
					loading = true;

					$.get( url, function( html ) {
						lm.checkSliders( html );
						lm.checkButton( html );

						loading = false;
					});
				}
			};

			this.checkSliders = function( html ) {
				var $sliders = $( el.sliders, html );

				if( $sliders.length > 0 && $sliders.children().length > 0 ) {
					lm.appendSliders( $sliders.html() );
					lm.refreshSliders();
				}
			};

			this.checkButton = function( html ) {
				if( $( el.button, html ).length > 0 ) {
					lm.refreshButton( $( el.button, html ).attr('href') );
				} else {
					lm.refreshButton( false );
				}
			};

			this.appendSliders = function( sliders ) {
				$( el.sliders ).append( sliders );
			};

			this.refreshSliders = function () {
				Generous.setup();
			};

			this.refreshButton = function( url ) {
				if ( false !== url ) {
					$button.attr( 'href', url );
				} else {
					$button.hide();
				}
			};

			this.init();
		};

		this.init();
	};

	return this.init();
})( jQuery );