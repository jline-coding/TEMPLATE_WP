( function () {
	'use strict';

	const runtime = window.csmfRuntime || {};
	const instances = new WeakMap();
	const escapeSelector = window.CSS && CSS.escape ? CSS.escape : ( value ) => String( value ).replace( /[^a-zA-Z0-9_-]/g, '\\$&' );

	const asArray = ( value ) => Array.isArray( value ) ? value : [ value ];
	const stringValue = ( value ) => value == null ? '' : String( value ).trim();
	const lengthOf = ( value ) => Array.from( String( value ) ).length;

	function fieldElements( form, name ) {
		const safe = escapeSelector( name );
		return Array.from( form.querySelectorAll( `[name="${ safe }"], [name="${ safe }[]"]` ) );
	}

	function fieldValue( form, name ) {
		const elements = fieldElements( form, name ).filter( ( element ) => ! element.disabled );
		const values = [];
		elements.forEach( ( element ) => {
			if ( ( element.type === 'checkbox' || element.type === 'radio' ) && ! element.checked ) return;
			if ( element.type === 'file' ) {
				if ( element.files && element.files[ 0 ] ) values.push( element.files[ 0 ].name );
				return;
			}
			if ( element.tagName === 'SELECT' && element.multiple ) {
				Array.from( element.selectedOptions ).forEach( ( option ) => values.push( option.value ) );
				return;
			}
			values.push( element.value );
		} );
		return values.length > 1 ? values : ( values[ 0 ] || '' );
	}

	function includesText( haystack, needle ) {
		return String( haystack ).includes( String( needle ) );
	}

	function conditionMatches( form, condition ) {
		const raw = fieldValue( form, condition.field );
		const values = asArray( raw ).map( stringValue );
		const actual = values.join( ',' );
		const expected = stringValue( condition.value );
		switch ( condition.operator ) {
			case 'equals': return values.includes( expected );
			case 'not_equals': return ! values.includes( expected );
			case 'contains': return includesText( actual, expected );
			case 'not_contains': return ! includesText( actual, expected );
			case 'in':
			case 'not_in': {
				const options = expected.split( /[\r\n,]+/ ).map( ( value ) => value.trim() );
				const matched = values.some( ( value ) => options.includes( value ) );
				return condition.operator === 'in' ? matched : ! matched;
			}
			case 'empty': case 'unchecked': return actual.trim() === '';
			case 'not_empty': case 'checked': return actual.trim() !== '';
			case 'greater': return actual !== '' && Number( actual ) > Number( expected );
			case 'greater_equal': return actual !== '' && Number( actual ) >= Number( expected );
			case 'less': return actual !== '' && Number( actual ) < Number( expected );
			case 'less_equal': return actual !== '' && Number( actual ) <= Number( expected );
			case 'regex':
				try { return expected.length <= 500 && new RegExp( expected, 'u' ).test( actual ); } catch ( error ) { return false; }
			default: return false;
		}
	}

	function groupMatches( form, rule ) {
		const conditions = Array.isArray( rule.conditions ) ? rule.conditions : [];
		if ( ! conditions.length ) return false;
		const results = conditions.map( ( condition ) => conditionMatches( form, condition ) );
		return rule.relation === 'any' ? results.some( Boolean ) : results.every( Boolean );
	}

	function targetContainers( form, rule ) {
		const placeholders = Array.from( form.querySelectorAll( `.smf-placeholder[data-name="${ escapeSelector( rule.target ) }"]` ) );
		if ( rule.scope === 'field' ) return placeholders;
		return [ ...new Set( placeholders.map( ( placeholder ) => placeholder.closest( '.smf-item' ) || placeholder ) ) ];
	}

	function clearElement( element ) {
		if ( element.type === 'checkbox' || element.type === 'radio' ) element.checked = false;
		else if ( element.type === 'file' ) element.value = '';
		else if ( element.tagName === 'SELECT' ) Array.from( element.options ).forEach( ( option ) => { option.selected = false; } );
		else element.value = '';
	}

	function setVisible( form, rule, visible, clearHidden ) {
		targetContainers( form, rule ).forEach( ( container ) => {
			const wasHidden = container.classList.contains( 'csmf-is-hidden' );
			container.classList.toggle( 'csmf-is-hidden', ! visible );
			container.hidden = ! visible;
			container.setAttribute( 'aria-hidden', visible ? 'false' : 'true' );
			container.querySelectorAll( 'input, select, textarea, button' ).forEach( ( element ) => {
				if ( ! visible ) {
					if ( ! element.disabled ) element.dataset.csmfDisabled = '1';
					element.disabled = true;
					if ( clearHidden && ! wasHidden ) clearElement( element );
				} else if ( element.dataset.csmfDisabled === '1' ) {
					element.disabled = false;
					delete element.dataset.csmfDisabled;
				}
			} );
		} );
	}

	function applyConditions( instance ) {
		const states = new Map();
		( instance.config.fieldRules || [] ).filter( ( rule ) => rule.enabled ).forEach( ( rule ) => {
			const matched = groupMatches( instance.form, rule );
			const visible = rule.action === 'hide_when' ? ! matched : matched;
			const state = states.get( rule.target ) || { visible: true, rules: [] };
			state.visible = state.visible && visible;
			state.rules.push( rule );
			states.set( rule.target, state );
		} );
		states.forEach( ( state ) => state.rules.forEach( ( rule ) => setVisible( instance.form, rule, state.visible, instance.config.clearHidden ) ) );
	}

	function defaultMessage( type, param ) {
		const messages = {
			required: '必須項目を入力してください。', email: '有効なメールアドレスを入力してください。',
			tel_jp: '有効な電話番号を入力してください。', postal_jp: '郵便番号を正しく入力してください（例：100-0001）。',
			hiragana: 'ひらがなで入力してください。', katakana: 'カタカナで入力してください。',
			url: '有効なURLを入力してください。', numeric: '数値で入力してください。', regex: '入力形式が正しくありません。',
			equals_field: '入力内容が一致していません。', different_field: '異なる内容を入力してください。',
		};
		if ( type === 'min_length' ) return `${ param }文字以上で入力してください。`;
		if ( type === 'max_length' ) return `${ param }文字以内で入力してください。`;
		if ( type === 'min' ) return `${ param }以上の値を入力してください。`;
		if ( type === 'max' ) return `${ param }以下の値を入力してください。`;
		return messages[ type ] || '入力内容を確認してください。';
	}

	function validationPasses( form, rule ) {
		const raw = fieldValue( form, rule.field );
		const value = Array.isArray( raw ) ? raw.join( ',' ) : stringValue( raw );
		if ( rule.type !== 'required' && value === '' ) return true;
		switch ( rule.type ) {
			case 'required': return value !== '';
			case 'email': return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( value );
			case 'tel_jp': return /^(?:\+81|0)[0-9\-() ]{8,18}$/.test( value );
			case 'postal_jp': return /^\d{3}-?\d{4}$/.test( value );
			case 'hiragana': return /^[ぁ-ゖー\s]+$/u.test( value );
			case 'katakana': return /^[ァ-ヺー\s]+$/u.test( value );
			case 'url': try { return [ 'http:', 'https:' ].includes( new URL( value ).protocol ); } catch ( error ) { return false; }
			case 'numeric': return value !== '' && Number.isFinite( Number( value ) );
			case 'min_length': return lengthOf( value ) >= Number( rule.param );
			case 'max_length': return lengthOf( value ) <= Number( rule.param );
			case 'min': return value !== '' && Number( value ) >= Number( rule.param );
			case 'max': return value !== '' && Number( value ) <= Number( rule.param );
			case 'regex': try { return new RegExp( rule.param, 'u' ).test( value ); } catch ( error ) { return false; }
			case 'equals_field': return value === stringValue( fieldValue( form, rule.param ) );
			case 'different_field': return value !== stringValue( fieldValue( form, rule.param ) );
			default: return true;
		}
	}

	function errorContainer( form, field ) {
		return form.querySelector( `.smf-placeholder[data-name="${ escapeSelector( field ) }"]` );
	}

	function renderErrors( instance, field, errors, force ) {
		const container = errorContainer( instance.form, field );
		if ( ! container || container.closest( '.csmf-is-hidden' ) ) return true;
		const id = `csmf-error-${ instance.formId }-${ field.replace( /[^a-zA-Z0-9_-]/g, '-' ) }`;
		container.querySelectorAll( '.csmf-error-messages' ).forEach( ( node ) => node.remove() );
		const controls = fieldElements( instance.form, field );
		controls.forEach( ( control ) => {
			control.removeAttribute( 'aria-invalid' );
			const described = ( control.getAttribute( 'aria-describedby' ) || '' ).split( /\s+/ ).filter( ( value ) => value && value !== id );
			if ( described.length ) control.setAttribute( 'aria-describedby', described.join( ' ' ) ); else control.removeAttribute( 'aria-describedby' );
		} );
		if ( ! errors.length || ( ! force && instance.config.hideErrorEmpty && stringValue( fieldValue( instance.form, field ) ) === '' ) ) return true;
		const box = document.createElement( 'div' );
		box.className = 'csmf-error-messages'; box.id = id; box.setAttribute( 'role', 'alert' );
		const list = document.createElement( 'ul' );
		errors.forEach( ( message ) => { const item = document.createElement( 'li' ); item.textContent = message; list.appendChild( item ); } );
		box.appendChild( list ); container.appendChild( box );
		controls.forEach( ( control ) => {
			control.setAttribute( 'aria-invalid', 'true' );
			control.setAttribute( 'aria-describedby', `${ control.getAttribute( 'aria-describedby' ) || '' } ${ id }`.trim() );
		} );
		return false;
	}

	function validateField( instance, field, force = false ) {
		const rules = ( instance.config.validations || [] ).filter( ( rule ) => rule.enabled && rule.field === field );
		const errors = rules.filter( ( rule ) => ! validationPasses( instance.form, rule ) ).map( ( rule ) => rule.message || defaultMessage( rule.type, rule.param ) );
		const fileState = instance.fileStates?.get( field ) || instance.imageStates?.get( field );
		if ( fileState && fileState.error ) errors.push( fileState.error );
		if ( fileState && fileState.pending && force ) errors.push( runtime.i18n?.checkingImage || '画像・ファイルを確認しています。' );
		return renderErrors( instance, field, [ ...new Set( errors ) ], force );
	}

	function validateForm( instance ) {
		const fields = new Set( ( instance.config.validations || [] ).filter( ( rule ) => rule.enabled ).map( ( rule ) => rule.field ) );
		( instance.config.uploads || [] ).filter( ( rule ) => rule.enabled ).forEach( ( rule ) => fields.add( rule.field ) );
		let valid = true;
		fields.forEach( ( field ) => { if ( ! validateField( instance, field, true ) ) valid = false; } );
		return valid;
	}

	function validateFile( instance, input ) {
		const name = ( input.name || '' ).replace( /\[\]$/, '' );
		const rule = ( instance.config.uploads || [] ).find( ( item ) => item.enabled && item.field === name );
		if ( ! rule ) return;
		if ( ! instance.fileStates ) instance.fileStates = new Map();
		const file = input.files && input.files[ 0 ];
		if ( ! file ) {
			instance.fileStates.delete( name );
			if ( rule.required ) {
				instance.fileStates.set( name, { pending: false, error: runtime.i18n?.fixErrors || 'ファイルを選択してください。' } );
			}
			validateField( instance, name );
			return;
		}

		const state = { pending: false, error: '' };
		instance.fileStates.set( name, state );
		const extensions = rule.extensions.split( ',' ).map( ( value ) => value.trim().toLowerCase() );
		const extension = ( file.name.split( '.' ).pop() || '' ).toLowerCase();
		const t = ( key, fallback ) => runtime.i18n?.[ key ] || fallback;

		if ( ! extensions.includes( extension ) ) {
			state.error = ( t( 'fileInvalidType', '許可されたファイル形式（%s）を選択してください。' ) ).replace( '%s', extensions.join( ' / ' ) );
		} else if ( file.size > Number( rule.max_mb ) * 1024 * 1024 ) {
			state.error = ( t( 'fileMaxSize', 'ファイルサイズは %sMB 以下にしてください。' ) ).replace( '%s', rule.max_mb );
		}

		if ( state.error ) {
			validateField( instance, name );
			return;
		}

		// If the selected file is an image and dimension constraints exist, inspect dimensions
		const isImage = file.type.startsWith( 'image/' ) || [ 'jpg', 'jpeg', 'png', 'webp', 'gif', 'avif' ].includes( extension );
		const hasDimensions = Boolean( rule.min_width || rule.max_width || rule.min_height || rule.max_height );

		if ( isImage && hasDimensions ) {
			state.pending = true;
			const image = new Image();
			const url = URL.createObjectURL( file );
			image.onload = () => {
				if ( rule.min_width && image.width < rule.min_width ) {
					state.error = ( t( 'imageMinWidth', '画像の横幅は %dpx 以上にしてください。' ) ).replace( '%d', rule.min_width );
				} else if ( rule.max_width && image.width > rule.max_width ) {
					state.error = ( t( 'imageMaxWidth', '画像の横幅は %dpx 以下にしてください。' ) ).replace( '%d', rule.max_width );
				} else if ( rule.min_height && image.height < rule.min_height ) {
					state.error = ( t( 'imageMinHeight', '画像の高さは %dpx 以上にしてください。' ) ).replace( '%d', rule.min_height );
				} else if ( rule.max_height && image.height > rule.max_height ) {
					state.error = ( t( 'imageMaxHeight', '画像の高さは %dpx 以下にしてください。' ) ).replace( '%d', rule.max_height );
				}
				state.pending = false;
				URL.revokeObjectURL( url );
				validateField( instance, name );
			};
			image.onerror = () => {
				state.error = t( 'imageInvalid', '画像ファイルを読み込めません。' );
				state.pending = false;
				URL.revokeObjectURL( url );
				validateField( instance, name );
			};
			image.src = url;
		} else {
			state.pending = false;
			validateField( instance, name );
		}
	}

	function applyUploadAttributes( instance ) {
		( instance.config.uploads || [] ).filter( ( rule ) => rule.enabled ).forEach( ( rule ) => {
			fieldElements( instance.form, rule.field ).filter( ( input ) => input.type === 'file' ).forEach( ( input ) => {
				input.accept = rule.extensions.split( ',' ).map( ( extension ) => `.${ extension.trim() }` ).join( ',' );
			} );
		} );
	}

	function bind( instance ) {
		if ( instance.bound ) return; instance.bound = true;
		instance.form.addEventListener( 'input', ( event ) => {
			applyConditions( instance );
			if ( instance.config.realtime && [ 'input', 'blur_input' ].includes( instance.config.validateOn ) && event.target.name ) validateField( instance, event.target.name.replace( /\[\]$/, '' ) );
		}, true );
		instance.form.addEventListener( 'change', ( event ) => {
			applyConditions( instance );
			if ( event.target.type === 'file' ) validateFile( instance, event.target );
			if ( instance.config.realtime && event.target.name ) validateField( instance, event.target.name.replace( /\[\]$/, '' ) );
		}, true );
		instance.form.addEventListener( 'focusout', ( event ) => {
			if ( instance.config.realtime && [ 'blur', 'blur_input' ].includes( instance.config.validateOn ) && event.target.name ) validateField( instance, event.target.name.replace( /\[\]$/, '' ) );
		}, true );
		instance.form.addEventListener( 'submit', ( event ) => {
			applyConditions( instance );
			if ( ! validateForm( instance ) ) {
				event.preventDefault(); event.stopImmediatePropagation();
				if ( instance.config.focusFirstError ) instance.form.querySelector( '[aria-invalid="true"]' )?.focus();
			}
		}, true );
		[ 'smf.input', 'smf.back', 'smf.invalid' ].forEach( ( name ) => instance.form.addEventListener( name, () => setTimeout( () => refresh( instance ), 0 ) ) );
	}

	function refresh( instance ) {
		applyUploadAttributes( instance ); applyConditions( instance );
	}

	async function loadConfiguration( form ) {
		const formIdInput = form.querySelector( '[name="snow-monkey-forms-meta[formid]"]' );
		const hashInput = form.querySelector( '[name="snow-monkey-forms-meta[form_hash]"]' );
		const sourceInput = form.querySelector( '[name="snow-monkey-forms-meta[source_post_id]"]' );
		if ( ! formIdInput || ! hashInput || ! runtime.endpoint ) return;
		const signature = `${ formIdInput.value }:${ hashInput.value }`;
		const current = instances.get( form );
		if ( current && current.signature === signature ) { refresh( current ); return; }
		try {
			const url = new URL( `${ runtime.endpoint }${ encodeURIComponent( formIdInput.value ) }` );
			url.searchParams.set( 'form_hash', hashInput.value ); url.searchParams.set( 'source_post_id', sourceInput?.value || '0' );
			const response = await fetch( url.toString(), { credentials: 'same-origin', headers: { Accept: 'application/json' } } );
			if ( ! response.ok ) throw new Error( `HTTP ${ response.status }` );
			const config = await response.json(); if ( ! config.enabled ) return;
			const instance = { form, config, formId: formIdInput.value, signature, imageStates: new Map(), bound: current?.bound || false };
			instances.set( form, instance ); bind( instance ); refresh( instance ); form.classList.add( 'csmf-ready' );
		} catch ( error ) {
			if ( window.console ) console.warn( '[Custom Snow Monkey Forms]', runtime.i18n?.configurationError, error );
		}
	}

	function observeForm( form ) {
		loadConfiguration( form );
		let scheduled = false;
		new MutationObserver( () => {
			if ( scheduled ) return; scheduled = true;
			window.requestAnimationFrame( () => { scheduled = false; loadConfiguration( form ); } );
		} ).observe( form, { childList: true, subtree: true } );
	}

	function boot() { document.querySelectorAll( '.snow-monkey-form' ).forEach( observeForm ); }
	if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', boot ); else boot();
}() );
