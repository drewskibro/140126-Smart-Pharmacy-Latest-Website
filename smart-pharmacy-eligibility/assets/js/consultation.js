/*
 * P-med consultation form behaviour.
 *
 * Vanilla JS (no jQuery), matching the eligibility checker. Collects the
 * answers keyed by each field's data-key, does a light client-side
 * required check for UX, then POSTs to admin-ajax. Server-side
 * validation in SPE_Consultation_Ajax is the source of truth — this
 * just saves the customer a round-trip on obvious omissions.
 */
( function () {
	'use strict';

	var cfg = window.SPE_CONSULT_CONFIG || {};

	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	ready( function () {
		var root = document.querySelector( '.spe-consult' );
		if ( ! root ) {
			return;
		}
		var form = root.querySelector( '.spe-consult__form' );
		if ( ! form ) {
			return;
		}

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			submit( root, form );
		} );
	} );

	/**
	 * Read one field's value based on its data-type.
	 */
	function readField( field ) {
		var type = field.getAttribute( 'data-type' );

		if ( type === 'radio' ) {
			var checked = field.querySelector( 'input[type="radio"]:checked' );
			return checked ? checked.value : '';
		}
		if ( type === 'checkbox' ) {
			var boxes = field.querySelectorAll( 'input[type="checkbox"]:checked' );
			return Array.prototype.map.call( boxes, function ( b ) {
				return b.value;
			} );
		}
		if ( type === 'select' ) {
			var sel = field.querySelector( 'select' );
			return sel ? sel.value : '';
		}
		var input = field.querySelector( 'textarea, input' );
		return input ? input.value.trim() : '';
	}

	function isEmpty( value ) {
		if ( Array.isArray( value ) ) {
			return value.length === 0;
		}
		return String( value ).trim() === '';
	}

	function setError( field, message ) {
		var el = field.querySelector( '.spe-consult__error' );
		if ( ! el ) {
			return;
		}
		if ( message ) {
			el.textContent = message;
			el.hidden = false;
			field.classList.add( 'spe-consult__field--error' );
		} else {
			el.textContent = '';
			el.hidden = true;
			field.classList.remove( 'spe-consult__field--error' );
		}
	}

	function submit( root, form ) {
		var fields = Array.prototype.slice.call( form.querySelectorAll( '.spe-consult__field' ) );
		var answers = {};
		var contact = {};
		var firstInvalid = null;

		fields.forEach( function ( field ) {
			setError( field, '' );
			var key = field.getAttribute( 'data-key' );
			var contactKey = field.getAttribute( 'data-contact' );
			var required = field.getAttribute( 'data-required' ) === '1';
			var value = readField( field );

			if ( contactKey ) {
				contact[ contactKey ] = value;
			} else if ( key ) {
				answers[ key ] = value;
			}

			var msg = '';
			if ( required && isEmpty( value ) ) {
				msg = 'This is required.';
			} else if ( contactKey === 'email' && !isEmpty( value ) && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( value ) ) {
				msg = 'Please enter a valid email address.';
			}
			if ( msg ) {
				setError( field, msg );
				if ( ! firstInvalid ) {
					firstInvalid = field;
				}
			}
		} );

		var formError = form.querySelector( '.spe-consult__form-error' );
		if ( formError ) {
			formError.hidden = true;
		}

		if ( firstInvalid ) {
			firstInvalid.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			return;
		}

		var productEl = form.querySelector( '.spe-consult__product' );
		var payload = {
			answers: answers,
			contact: contact,
			product_id: productEl ? parseInt( productEl.value, 10 ) || 0 : 0
		};

		var button = form.querySelector( '.spe-consult__submit' );
		var original = button ? button.textContent : '';
		if ( button ) {
			button.disabled = true;
			button.textContent = 'Submitting…';
		}

		var body = new FormData();
		body.append( 'action', cfg.action || 'spe_consultation_submit' );
		body.append( 'nonce', cfg.nonce || '' );
		body.append( 'payload', JSON.stringify( payload ) );

		fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( r ) {
				return r.json().then( function ( json ) {
					return { ok: r.ok, json: json };
				} );
			} )
			.then( function ( res ) {
				if ( res.ok && res.json && res.json.success ) {
					onSuccess( root, form, res.json.data || {} );
					return;
				}
				onError( form, fields, res.json && res.json.data ? res.json.data : {} );
			} )
			.catch( function () {
				onError( form, fields, { message: 'Something went wrong. Please try again.' } );
			} )
			.then( function () {
				if ( button ) {
					button.disabled = false;
					button.textContent = original;
				}
			} );
	}

	function onError( form, fields, data ) {
		if ( data.fields ) {
			var firstInvalid = null;
			fields.forEach( function ( field ) {
				var key = field.getAttribute( 'data-key' ) || field.getAttribute( 'data-contact' );
				if ( key && data.fields[ key ] ) {
					setError( field, data.fields[ key ] );
					if ( ! firstInvalid ) {
						firstInvalid = field;
					}
				}
			} );
			if ( firstInvalid ) {
				firstInvalid.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
		}
		var formError = form.querySelector( '.spe-consult__form-error' );
		if ( formError ) {
			formError.textContent = data.message || 'Please check your answers and try again.';
			formError.hidden = false;
		}
	}

	function onSuccess( root, form, data ) {
		if ( data.redirect ) {
			window.location.href = data.redirect;
			return;
		}
		form.hidden = true;
		var panel = root.querySelector( '.spe-consult__confirmation' );
		if ( panel ) {
			var msg = panel.querySelector( '.spe-consult__confirmation-msg' );
			if ( msg ) {
				msg.textContent = data.message || 'Your consultation has been submitted.';
			}
			panel.hidden = false;
			panel.scrollIntoView( { behavior: 'smooth', block: 'center' } );
		}
	}
} )();
