<?php

class Mailform_Js {
	
	// attachment addon property
	private $jpg                   = 1;
	private $png                   = 1;
	private $gif                   = 1;
	private $zip                   = 0;
	private $pdf                   = 0;
	private $doc                   = 0;
	private $xls                   = 0;
	private $upload_max_size       = 2000000;
	
	
	// scroll addon property
	private $scroll_amount         = 70;
	
	
	// label addon property
	private $required_text         = 'Required';
	private $optional_text         = 'Optional';
	
	
	
	
	// PHP public construct
	public function __construct() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' );
			include( dirname( __FILE__ ) .'/../addon/attachment/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/scroll/scroll-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/scroll/scroll-config.php' );
			include( dirname( __FILE__ ) .'/../addon/scroll/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/label/label-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/label/label-config.php' );
			include( dirname( __FILE__ ) .'/../addon/label/config-include.php' );
		}
		
		
		header( 'Content-Type: application/javascript' );
		
		
		echo <<<EOM

/*--------------------------------------------------------------------------
	
	Script Name    : Responsive Mailform
	Author         : FIRSTSTEP - Motohiro Tani
	Author URL     : https://www.1-firststep.com
	Create Date    : 2014/03/25
	Version        : 8.0.1
	Last Update    : 2021/12/03
	
--------------------------------------------------------------------------*/


(function( $ ) {
	
	// global variable init
	var mailform_dt    = $( 'form#mail_form dl dt' );
	var confirm_window = 1;
	var rm_token       = '';
	var scroll_amount  = {$this->scroll_amount};
	var required_text  = '{$this->required_text}';
	var optional_text  = '{$this->optional_text}';
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/writing-time/variable-init.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/writing-time/variable-init.js' );
		}
		
		
		
		
		echo <<<EOM

	
	
	
	
	// function resize
	function resize() {
		
		$( '.loading-layer' ).css({
			'width': $( window ).width() + 'px',
			'height': window.innerHeight + 'px'
		});
		
	}
	
	
	
	
	// function slice_method
	function slice_method( el ) {
		
		var dt      = el.parents( 'dd' ).prev( 'dt' );
		var dt_name = dt.html().replace( /<span.*<\/span>/gi, '' );
		dt_name     = dt_name.replace( /<i.*<\/i>/gi, '' );
		dt_name     = dt_name.replace( /<br>|<br \/>/gi, '' );
		return dt_name;
		
	}
	
	
	
	
	// function error_span
	function error_span( e, dt, comment, bool ) {
		
		if ( bool === true ) {
			var m = e.parents( 'dd' ).find( 'span.error_blank' ).text( dt + ' is ' + comment + ' not entered' );
		} else {
			var m = e.parents( 'dd' ).find( 'span.error_blank' ).text( '' );
		}
		
	}
	
	
	
	
	// function compare_method
	function compare_method( s, e ) {
		
		if ( s > e ) {
			return e;
		} else {
			return s;
		}
		
	}
	
	
	
	
	// function hidden_append
	function hidden_append( name, value ) {
		
		$( '<input />' )
			.attr({
				type: 'hidden',
				id: name,
				name: name,
				value: value
			})
			.appendTo( $( 'p#form_submit' ) );
		
	}
	
	
	
	
	// function token_get
	function token_get() {
		
		var form = $( 'form#mail_form' );
		
		
		if ( form.length > 0 ) {
			$.ajax({
				type: form.attr( 'method' ),
				url: form.attr( 'action' ),
				cache: false,
				dataType: 'text',
				data: 'token_get=true&javascript_action=true',
				
				success: function( res ) {
					var response = res.split( ',' );
					if ( response[0] === 'token_success' ) {
						rm_token = response[1];
						setTimeout(function() {
							token_get();
						}, 900000 );
					} else {
						window.alert( 'Failed to get token.' );
					}
				},
				
				error: function( res ) {
					window.alert( 'Ajax communication failed.\\nPlease reload the page and try again.' );
				}
			});
		}
		
	}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/confirm/config-get.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/confirm/config-get.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/file-change-js.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/file-change-js.php' );
		}
		
		
		
		
		echo <<<EOM

	
	
	
	
	// function required_check
	function required_check() {
		
		var error        = 0;
		var scroll_point = $( 'body' ).height();
		
		
		for ( var i = 0; i < mailform_dt.length; i++ ) {
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'input' ).length && mailform_dt.eq(i).next( 'dd' ).hasClass( 'required' ) ) {
				
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'input' );
				var dt_name  = slice_method( elements.eq(0) );
				
				if ( elements.eq(0).attr( 'type' ) === 'radio' || elements.eq(0).attr( 'type' ) === 'checkbox' ) {
					
					var list_error = 0;
					for ( var j = 0; j < elements.length; j++ ) {
						if ( elements.eq(j).prop( 'checked' ) === false ) {
							list_error++;
						}
					}
					
					if ( list_error === elements.length ) {
						error_span( elements.eq(0), dt_name, 'selected', true );
						error++;
						scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
					} else {
						error_span( elements.eq(0), dt_name, '', false );
					}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/require-check.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/require-check.js' );
		}
		
		
		
		
		echo <<<EOM

					
				} else {
					
					var list_error = 0;
					for ( var j = 0; j < elements.length; j++ ) {
						if ( elements.eq(j).val() === '' ) {
							list_error++;
						}
					}
					
					if ( list_error !== 0 ) {
						error_span( elements.eq(0), dt_name, 'entered', true );
						error++;
						scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
					} else {
						error_span( elements.eq(0), dt_name, '', false );
					}
					
				}
			}
			
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'select' ).length && mailform_dt.eq(i).next( 'dd' ).hasClass( 'required' ) ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'select' );
				var dt_name  = slice_method( elements.eq(0) );
				
				var list_error = 0;
				for ( var j = 0; j < elements.length; j++ ) {
					if ( elements.eq(j).val() === '' ) {
						list_error++;
					}
				}
				
				if ( list_error !== 0 ) {
					error_span( elements.eq(0), dt_name, 'selected', true );
					error++;
					scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
				} else {
					error_span( elements.eq(0), dt_name, '', false );
				}
			}
			
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'textarea' ).length && mailform_dt.eq(i).next( 'dd' ).hasClass( 'required' ) ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'textarea' );
				var dt_name  = slice_method( elements.eq(0) );
				if ( elements.eq(0).val() === '' ) {
					error_span( elements.eq(0), dt_name, 'entered', true );
					error++;
					scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
				} else {
					error_span( elements.eq(0), dt_name, '', false );
				}
			}
			
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'input' ).length && mailform_dt.eq(i).next( 'dd' ).find( 'input' ).eq(0).attr( 'type' ) === 'email' ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'input' );
				var dt_name  = slice_method( elements.eq(0) );
				if ( elements.eq(0).val() !== '' && ! ( elements.eq(0).val().match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/) ) ) {
					elements.eq(0).parents( 'dd' ).find( 'span.error_format' ).text( 'Incorrect email format.' );
					error++;
					scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
				} else {
					elements.eq(0).parents( 'dd' ).find( 'span.error_format' ).text( '' );
				}
			}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/filetype-check.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/filetype-check.js' );
		}
		
		
		
		
		echo <<<EOM

		}
		
		
		if ( $( 'input[name="mail_address_confirm"]' ).length && $( 'input[name="mail_address"]' ).length ) {
			var element   = $( 'input[name="mail_address_confirm"]' );
			var element_2 = $( 'input[name="mail_address"]' );
			var dt_name   = slice_method( element );
			
			if ( element.val() !== '' && element.val() !== element_2.val() ) {
				element.parents( 'dd' ).find( 'span.error_match' ).text( 'Email addresses do not match.' );
				error++;
				scroll_point = compare_method( scroll_point, element.offset().top );
			} else {
				element.parents( 'dd' ).find( 'span.error_match' ).text( '' );
			}
		}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/agreement/agree-check.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/agreement/agree-check.js' );
		}
		
		
		
		
		echo <<<EOM

		
		
		
		
		if ( error === 0 ) {
			
			if ( confirm_window === 1 ) {
				if ( window.confirm( 'Are you sure you want to send?' ) ) {
					send_setup();
					order_set();
					send_method();
				}
			}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/confirm/confirm-window-set-js.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/confirm/confirm-window-set-js.php' );
		}
		
		
		
		
		echo <<<EOM

			
		} else {
			$( 'html, body' ).animate({
				scrollTop: scroll_point - scroll_amount
			}, 500 );
		}
		
	}
	
	
	
	
	// function send_setup
	function send_setup() {
		
		hidden_append( 'javascript_action', true );
		hidden_append( 'token', rm_token );
		
		var now_url = encodeURI( document.URL );
		hidden_append( 'now_url', now_url );
		
		var before_url = encodeURI( document.referrer );
		hidden_append( 'before_url', before_url );
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/writing-time/hidden-append.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/writing-time/hidden-append.js' );
		}
		
		
		
		
		echo <<<EOM

		
	}
	
	
	
	
	// function order_set
	function order_set() {
		
		var order_number = 0;
		for ( var i = 0; i < mailform_dt.length; i++ ) {
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'input' ).length ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'input' );
				var dt_name  = slice_method( elements.eq(0) );
				
				if ( elements.eq(0).attr( 'type' ) === 'radio' || elements.eq(0).attr( 'type' ) === 'checkbox' ) {
					
					var attr_name = elements.eq(0).attr( 'name' ).replace( /\[|\]/g, '' );
					var attr_type = elements.eq(0).attr( 'type' );
					order_number++;
					hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',false,' + dt_name );
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/order-set.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/order-set.js' );
		}
		
		
		
		
		echo <<<EOM

					
				} else {
					
					for ( var j = 0; j < elements.length; j++ ) {
						var attr_name = elements.eq(j).attr( 'name' );
						var attr_type = elements.eq(j).attr( 'type' );
						if ( j === 0 ) {
							var connect = 'false';
						} else {
							var connect = 'true';
						}
						order_number++;
						hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',' + connect + ',' + dt_name );
					}
					
				}
			}
			
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'select' ).length ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'select' );
				var dt_name  = slice_method( elements.eq(0) );
				
				for ( var j = 0; j < elements.length; j++ ) {
					var attr_name = elements.eq(j).attr( 'name' );
					var attr_type = 'select';
					if ( j === 0 ) {
						var connect = 'false';
					} else {
						var connect = 'true';
					}
					order_number++;
					hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',' + connect + ',' + dt_name );
				}
			}
			
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'textarea' ).length ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'textarea' );
				var dt_name  = slice_method( elements.eq(0) );
				var attr_name = elements.eq(0).attr( 'name' );
				var attr_type = 'textarea';
				order_number++;
				hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',false,' + dt_name );
			}
			
		}
		
		
		hidden_append( 'order_count', order_number );
		
	}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/confirm/cancel-click.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/confirm/cancel-click.js' );
		}
		
		
		
		
		echo <<<EOM

	
	
	
	
	// function send_method
	function send_method() {
		
		$( '<div>' )
			.addClass( 'loading-layer' )
			.appendTo( 'body' )
			.append( '<span class="loading"></span>' );
		
		setTimeout(function() {
			
			var form_data = new FormData( $( 'form#mail_form' ).get(0) );
			
			$.ajax({
				type: $( 'form#mail_form' ).attr( 'method' ),
				url: $( 'form#mail_form' ).attr( 'action' ),
				cache: false,
				dataType: 'html',
				data: form_data,
				contentType: false,
				processData: false,
				
				success: function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					var response = res.split( ',' );
					if ( response[0] === 'send_success' ) {
						window.location.href = response[1];
					} else {
						$( 'input#form_submit_button' ).nextAll( 'input' ).remove();
						response[1] = response[1].replace( /<br>|<br \/>/gi, "\\n" );
						window.alert( response[1] );
						ios_bugfix();
					}
				},
				
				error: function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					$( 'input#form_submit_button' ).nextAll( 'input' ).remove();
					window.alert( 'Communication failed.\\nPlease try again after reloading the page.' );
				}
			});
			
		}, 1000 );
		
	}
	
	
	
	
	// function ios_bugfix
	function ios_bugfix() {
		
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/ios-bugfix.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/ios-bugfix.js' );
		}
		
		
		
		
		echo <<<EOM

	}
	
	
	
	
	// page setting
	for ( var i = 0; i < mailform_dt.length; i++ ) {
		var dt_name  = slice_method( mailform_dt.eq(i).next( 'dd' ).find( 'input, select, textarea' ).eq(0) );
		var elements = mailform_dt.eq(i).next( 'dd' ).find( 'input, select, textarea' );
		if ( mailform_dt.eq(i).next( 'dd' ).hasClass( 'required' ) ) {
			mailform_dt.eq(i).addClass( 'required' );
			mailform_dt.eq(i).after( '<span class="required_label">' + required_text + '</span>' );
		} else {
			mailform_dt.eq(i).after( '<span class="required_label">' + optional_text + '</span>' );
		}
	}
	
	
	
	
	// window on load
	$( window ).on( 'load', function() {
		
		if ( typeof token_get === 'function' ) {
			token_get();
		}
		
		$( 'body' ).addClass( 'loaded' );
		
	});
	
	
	
	
	// window on resize
	$( window ).on( 'resize', function() {
		
		resize();
		
	});
	
	
	
	
	// form submit
	$( 'form#mail_form' ).on( 'submit', function( event ) {
		
		event.preventDefault();
		required_check();
		
	});
	
	
	
	
	// form reset
	$( 'input[type="reset"]' ).on( 'click', function() {
		
		$( 'span.error_blank, span.error_match, span.error_format' ).text( '' );
		
	});
	
	
	
	
	// required label setting
	if ( typeof required_label_setting === 'function' ) {
		required_label_setting();
	}
	
	
	
	
})( jQuery );


EOM;
		
		
	}
	
}

$Mailform_Js = new Mailform_Js;

?>