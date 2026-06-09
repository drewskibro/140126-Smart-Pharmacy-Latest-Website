/**
 * Smart Pharmacy Eligibility Checker — frontend behaviour.
 *
 * State machine + AJAX integration. Loaded only on pages that
 * render the [smart_pharmacy_eligibility] shortcode (see
 * SPE_Shortcode::enqueue_assets).
 *
 * AJAX contract (server-side handlers in includes/class-ajax.php):
 *   tc_eligibility_save_partial  -- early capture
 *   tc_eligibility_save          -- final submission (rules + add-to-cart)
 *   tc_eligibility_ineligible    -- audit log a client-side fail
 *
 * State lives in `state.data` + `state.history`. The assessment_id
 * received from the early-capture call is stashed both in `state.data`
 * and in the SPE_CONFIG.cookie cookie so a page reload mid-flow can
 * be tied back to the partial row.
 */
( function () {
	'use strict';

	var root = document.querySelector( '.spe-app' );
	if ( ! root ) { return; }

	var cfg = window.SPE_CONFIG || {};

	var state = {
		currentScreen: '1',
		history: [],
		data: {
			agreementChecks: [false, false, false, false, false],
			prevMeds: [],
			selectedTreatment: 'wegovy',
			selectedDose: '0.25mg',
		},
	};

	/* -------------------------------------------------------------
	 * Screen navigation
	 * ----------------------------------------------------------- */

	function showScreen( id ) {
		root.querySelectorAll( '.screen' ).forEach( function ( el ) {
			el.classList.remove( 'active' );
		} );
		var target = root.querySelector( '#screen-' + id );
		if ( target ) {
			target.classList.add( 'active' );
			state.currentScreen = String( id );
			window.scrollTo( 0, 0 );
		}
	}

	function goTo( id ) {
		state.history.push( state.currentScreen );
		showScreen( id );
	}

	function goBack() {
		var prev = state.history.pop();
		if ( prev ) { showScreen( prev ); }
	}

	function showIneligible( reason ) {
		var el = root.querySelector( '#ineligible-reason' );
		if ( el ) { el.textContent = reason; }
		showScreen( 'ineligible' );
		// Audit log if we have an assessment id stashed.
		if ( state.data.assessmentId ) {
			ajax( 'tc_eligibility_ineligible', {
				assessmentId: state.data.assessmentId,
				reason: reason,
			} );
		}
	}

	/* -------------------------------------------------------------
	 * AJAX helpers
	 * ----------------------------------------------------------- */

	function ajax( action, payload ) {
		var body = new FormData();
		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce || '' );
		Object.keys( payload || {} ).forEach( function ( k ) {
			var v = payload[ k ];
			if ( v !== null && typeof v === 'object' ) {
				body.append( k, JSON.stringify( v ) );
			} else {
				body.append( k, v );
			}
		} );

		return fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} ).then( function ( r ) { return r.json(); } );
	}

	function ajaxPayload( action ) {
		// Final-submit endpoint expects the full payload as a JSON
		// blob under `payload` so nested arrays survive.
		var body = new FormData();
		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce || '' );
		body.append( 'payload', JSON.stringify( buildPayload() ) );
		return fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} ).then( function ( r ) { return r.json(); } );
	}

	function buildPayload() {
		return {
			assessmentId: state.data.assessmentId || '',
			firstName: state.data.firstName || '',
			lastName: state.data.lastName || '',
			email: state.data.email || '',
			phone: state.data.phone || '',
			dob: state.data.dob || '',
			ageBand: state.data.age || '',
			ethnicity: state.data.ethnicity || '',
			sex: state.data.sex || '',
			pregnant: state.data.pregnant || '',
			breastfeeding: state.data.breastfeeding || '',
			conceive: state.data.conceive || '',
			weightKg: parseFloat( state.data.weight || 0 ),
			heightCm: parseFloat( state.data.height || 0 ),
			bmi: parseFloat( state.data.bmi || 0 ),
			bariatricRecent: state.data.bariatricRecent || '',
			addressLine1: state.data.addressLine1 || '',
			addressLine2: state.data.addressLine2 || '',
			city: state.data.city || '',
			postcode: state.data.postcode || '',
			country: state.data.country || 'United Kingdom',
			selectedTreatment: state.data.selectedTreatment,
			selectedDose: state.data.selectedDose,
		};
	}

	/* -------------------------------------------------------------
	 * Screen 1: agreement
	 * ----------------------------------------------------------- */

	root.querySelectorAll( '.agreement-checkbox' ).forEach( function ( cb ) {
		cb.addEventListener( 'change', function () {
			var idx = parseInt( cb.dataset.index, 10 );
			state.data.agreementChecks[ idx ] = cb.checked;
			var allChecked = state.data.agreementChecks.every( Boolean );
			var btn = root.querySelector( '#agree-continue' );
			if ( btn ) { btn.disabled = ! allChecked; }
		} );
	} );

	bind( '#agree-continue', 'click', function () {
		if ( state.data.agreementChecks.every( Boolean ) ) {
			goTo( '1b' );
		}
	} );

	/* -------------------------------------------------------------
	 * Screen 1b: early capture (name / email / phone)
	 * ----------------------------------------------------------- */

	bind( '#early-continue', 'click', function () {
		var fn = val( '#early-first-name' ),
		    ln = val( '#early-last-name' ),
		    em = val( '#early-email' ),
		    ph = val( '#early-phone' );
		var err = root.querySelector( '#early-form-error' );

		if ( ! fn || ! ln || ! em || ! ph ) {
			return showError( err, 'Please fill in all fields' );
		}
		if ( ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( em ) ) {
			return showError( err, 'Please enter a valid email address' );
		}
		var digits = ph.replace( /\D/g, '' );
		if ( digits.length < 10 || digits.length > 11 ) {
			return showError( err, 'Please enter a valid UK mobile number' );
		}
		err.style.display = 'none';

		state.data.firstName = fn;
		state.data.lastName  = ln;
		state.data.fullName  = fn + ' ' + ln;
		state.data.email     = em;
		state.data.phone     = ph;

		ajax( 'tc_eligibility_save_partial', { payload: JSON.stringify( buildPayload() ) } )
			.then( function ( res ) {
				if ( res && res.success && res.data && res.data.assessment_id ) {
					state.data.assessmentId = res.data.assessment_id;
					if ( res.data.nonce ) { cfg.nonce = res.data.nonce; }
					document.cookie = ( cfg.cookie || 'tc_eligibility_data' ) + '=' +
						encodeURIComponent( JSON.stringify( { assessment_id: state.data.assessmentId } ) ) +
						';path=/;max-age=3600;samesite=lax';
				}
			} )
			.catch( function () { /* progress regardless -- next steps work */ } );

		goTo( '4' );
	} );

	/* -------------------------------------------------------------
	 * Screen 4: age (gates 18-74 only)
	 * ----------------------------------------------------------- */

	on( 'input[name="age"]', 'change', function ( e ) {
		state.data.age = e.target.value;
		if ( e.target.value === 'under-18' ) {
			return showIneligible( "Our weight loss plan isn't suitable for people under 18 years old." );
		}
		if ( e.target.value === '75-over' ) {
			return showIneligible( "Our weight loss plan isn't suitable for people over 75 years old." );
		}
		goTo( '5' );
	} );

	/* -------------------------------------------------------------
	 * Screen 5: ethnicity
	 * ----------------------------------------------------------- */

	on( 'input[name="ethnicity"]', 'change', function ( e ) {
		state.data.ethnicity = e.target.value;
		goTo( '6' );
	} );

	/* -------------------------------------------------------------
	 * Screen 6: sex assigned at birth
	 * ----------------------------------------------------------- */

	root.querySelectorAll( '[data-set-sex]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			state.data.sex = btn.dataset.setSex;
			if ( 'female' === state.data.sex ) {
				goTo( '6b' );
			} else {
				goTo( '7' );
			}
		} );
	} );

	/* -------------------------------------------------------------
	 * Screen 6b: female screening
	 * ----------------------------------------------------------- */

	[ 'pregnant', 'breastfeeding', 'conceive' ].forEach( function ( name ) {
		on( 'input[name="' + name + '"]', 'change', function ( e ) {
			state.data[ name ] = e.target.value;

			// Highlight the active label visually.
			root.querySelectorAll( 'input[name="' + name + '"]' ).forEach( function ( r ) {
				r.closest( '.radio-button-label' ).classList.toggle( 'is-active', r.checked );
			} );

			var allAnswered = !! ( state.data.pregnant && state.data.breastfeeding && state.data.conceive );
			var btn = root.querySelector( '#female-screening-continue' );
			if ( btn ) { btn.disabled = ! allAnswered; }
		} );
	} );

	bind( '#female-screening-continue', 'click', function () {
		if ( 'yes' === state.data.pregnant || 'yes' === state.data.breastfeeding || 'yes' === state.data.conceive ) {
			return showIneligible( 'For safety reasons, weight loss medications cannot be prescribed during pregnancy, when planning to become pregnant, or while breastfeeding.' );
		}
		goTo( '7' );
	} );

	/* -------------------------------------------------------------
	 * Screen 7: weight
	 * ----------------------------------------------------------- */

	on( 'input[name="weight-unit"]', 'change', function () {
		var kg = root.querySelector( '#weight-kg-input' );
		var st = root.querySelector( '#weight-st-inputs' );
		var sel = root.querySelector( 'input[name="weight-unit"]:checked' );
		if ( sel && sel.value === 'kg' ) {
			kg.style.display = 'block'; st.style.display = 'none';
		} else {
			kg.style.display = 'none'; st.style.display = 'flex';
		}
	} );

	bind( '#weight-next', 'click', function () {
		var err  = root.querySelector( '#weight-error' );
		var unit = root.querySelector( 'input[name="weight-unit"]:checked' ).value;
		var kg   = 0;
		if ( 'kg' === unit ) {
			kg = parseFloat( val( '#weight-kg-input' ) );
			if ( ! kg || kg < 40 || kg > 250 ) {
				return showError( err, 'Please enter a valid weight (40-250 kg)' );
			}
		} else {
			var stones = parseInt( val( '#weight-stone' ), 10 ) || 0;
			var pounds = parseInt( val( '#weight-pounds' ), 10 ) || 0;
			var totalLb = ( stones * 14 ) + pounds;
			if ( totalLb < 84 || totalLb > 560 ) {
				return showError( err, 'Please enter a valid weight (6st - 40st)' );
			}
			kg = totalLb * 0.453592;
		}
		err.style.display = 'none';
		state.data.weight = kg.toFixed( 1 );
		goTo( '8' );
	} );

	/* -------------------------------------------------------------
	 * Screen 8: height -> BMI calculation
	 * ----------------------------------------------------------- */

	on( 'input[name="height-unit"]', 'change', function () {
		var cm  = root.querySelector( '#height-cm-input' );
		var ft  = root.querySelector( '#height-ft-inputs' );
		var sel = root.querySelector( 'input[name="height-unit"]:checked' );
		if ( sel && sel.value === 'cm' ) {
			cm.style.display = 'block'; ft.style.display = 'none';
		} else {
			cm.style.display = 'none'; ft.style.display = 'flex';
		}
	} );

	bind( '#height-next', 'click', function () {
		var err = root.querySelector( '#height-error' );
		var unit = root.querySelector( 'input[name="height-unit"]:checked' ).value;
		var cm = 0;
		if ( 'cm' === unit ) {
			cm = parseFloat( val( '#height-cm-input' ) );
			if ( ! cm || cm < 120 || cm > 230 ) {
				return showError( err, 'Please enter a valid height (120-230 cm)' );
			}
		} else {
			var feet = parseInt( val( '#height-feet' ), 10 ) || 0;
			var inch = parseInt( val( '#height-inches' ), 10 ) || 0;
			var totIn = ( feet * 12 ) + inch;
			if ( totIn < 48 || totIn > 90 ) {
				return showError( err, "Please enter a valid height (4'0\" - 7'6\")" );
			}
			cm = totIn * 2.54;
		}
		err.style.display = 'none';

		state.data.height = cm.toFixed( 1 );
		var w = parseFloat( state.data.weight || 0 );
		var bmi = w / Math.pow( cm / 100, 2 );
		state.data.bmi = bmi.toFixed( 1 );

		// Render BMI result.
		var disp = root.querySelector( '#bmi-display' );
		var cat  = root.querySelector( '#bmi-category' );
		if ( disp ) { disp.textContent = state.data.bmi; }
		if ( cat ) {
			var c = '-';
			if ( bmi < 18.5 ) c = 'Underweight';
			else if ( bmi < 25 ) c = 'Healthy weight';
			else if ( bmi < 30 ) c = 'Overweight';
			else if ( bmi < 35 ) c = 'Obese (Class I)';
			else if ( bmi < 40 ) c = 'Obese (Class II)';
			else c = 'Obese (Class III)';
			cat.textContent = c;
		}

		goTo( '8b' );
	} );

	/* -------------------------------------------------------------
	 * Screen 8b: BMI eligibility gate
	 * ----------------------------------------------------------- */

	bind( '#bmi-continue', 'click', function () {
		var bmi = parseFloat( state.data.bmi || 0 );
		var isAsian = state.data.ethnicity && state.data.ethnicity.indexOf( 'asian' ) !== -1;
		var min = isAsian ? 23 : 27;
		if ( bmi < min ) {
			return showIneligible(
				'Based on your BMI of ' + state.data.bmi + ', weight loss medication is not clinically appropriate at this time. A BMI of ' + min + ' or above is required' + ( isAsian ? ' (adjusted for South Asian ethnicity)' : '' ) + '.'
			);
		}
		goTo( '10a' );
	} );

	/* -------------------------------------------------------------
	 * Screen 10a: bariatric in last 6 months (fail) or proceed
	 * ----------------------------------------------------------- */

	root.querySelectorAll( '[data-set-bariatric]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			state.data.bariatricRecent = btn.dataset.setBariatric;
			if ( 'yes' === btn.dataset.setBariatric ) {
				return showIneligible( 'Weight loss medication is not suitable within 6 months of bariatric surgery.' );
			}
			goTo( '18' );
		} );
	} );

	/* -------------------------------------------------------------
	 * Screen 18: DOB
	 * ----------------------------------------------------------- */

	bind( '#dob-next', 'click', function () {
		var dob = val( '#dob' );
		if ( ! dob ) { return alert( 'Please enter your date of birth' ); }
		var d = new Date( dob );
		if ( d > new Date() ) { return alert( 'Date of birth cannot be in the future' ); }
		var age = new Date().getFullYear() - d.getFullYear();
		if ( age < 18 ) { return alert( 'You must be at least 18 years old.' ); }
		state.data.dob = dob;
		goTo( '19' );
	} );

	// Keep the "Completing as" line live as we collect details.
	setInterval( function () {
		var el = root.querySelector( '#completing-as' );
		if ( el && state.data.fullName && state.data.email ) {
			el.textContent = state.data.fullName + ' · ' + state.data.email;
		}
	}, 500 );

	/* -------------------------------------------------------------
	 * Screen 19: address
	 * ----------------------------------------------------------- */

	bind( '#address-next', 'click', function () {
		var err = root.querySelector( '#address-error' );
		var a1 = val( '#address-line1' );
		var a2 = val( '#address-line2' );
		var city = val( '#city' );
		var pc = val( '#postcode' ).toUpperCase();
		var country = val( '#country' );

		if ( ! a1 || ! city || ! pc ) {
			return showError( err, 'Please fill in all required fields' );
		}
		if ( ! /^[A-Z]{1,2}\d{1,2}[A-Z]?\s?\d[A-Z]{2}$/i.test( pc ) ) {
			return showError( err, 'Please enter a valid UK postcode' );
		}
		err.style.display = 'none';

		state.data.addressLine1 = a1;
		state.data.addressLine2 = a2;
		state.data.city         = city;
		state.data.postcode     = pc;
		state.data.country      = country;

		goTo( '21' );
	} );

	/* -------------------------------------------------------------
	 * Screen 21: treatment selection + submit
	 * ----------------------------------------------------------- */

	root.querySelectorAll( '[data-select-treatment]' ).forEach( function ( card ) {
		card.addEventListener( 'click', function () {
			state.data.selectedTreatment = card.dataset.selectTreatment;
			state.data.selectedDose = 'wegovy' === card.dataset.selectTreatment ? '0.25mg' : '2.5mg';
			root.querySelectorAll( '[data-select-treatment]' ).forEach( function ( c ) {
				c.classList.toggle( 'selected', c === card );
			} );
		} );
	} );

	bind( '#submit-button', 'click', function () {
		var btn = this;
		btn.disabled = true;
		btn.textContent = 'Submitting...';

		ajaxPayload( 'tc_eligibility_save' )
			.then( function ( res ) {
				if ( ! res || ! res.success ) {
					showScreen( 'confirmed' );
					return;
				}
				if ( 'ineligible' === res.data.status ) {
					return showIneligible( res.data.reason || 'No suitable treatment.' );
				}
				if ( res.data.nonce ) { cfg.nonce = res.data.nonce; }

				showScreen( 'confirmed' );
				if ( res.data.checkoutUrl ) {
					setTimeout( function () {
						window.location.href = res.data.checkoutUrl;
					}, 1200 );
				}
			} )
			.catch( function () {
				showScreen( 'confirmed' );
			} );
	} );

	bind( '#review-button', 'click', function () {
		state.history = [];
		state.currentScreen = '1';
		showScreen( '1' );
	} );

	/* -------------------------------------------------------------
	 * Back buttons (delegated)
	 * ----------------------------------------------------------- */

	root.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-action="back"]' );
		if ( btn ) {
			e.preventDefault();
			goBack();
		}
	} );

	/* -------------------------------------------------------------
	 * Helpers
	 * ----------------------------------------------------------- */

	function bind( sel, evt, fn ) {
		var el = root.querySelector( sel );
		if ( el ) { el.addEventListener( evt, fn ); }
	}
	function on( sel, evt, fn ) {
		root.querySelectorAll( sel ).forEach( function ( el ) { el.addEventListener( evt, fn ); } );
	}
	function val( sel ) {
		var el = root.querySelector( sel );
		return el ? ( el.value || '' ).trim() : '';
	}
	function showError( el, msg ) {
		if ( ! el ) { return; }
		el.textContent = msg;
		el.style.display = 'block';
	}
} )();
