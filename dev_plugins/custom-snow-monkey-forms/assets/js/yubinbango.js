/**
 * YubinBango - Japanese Postal Code to Address Auto-fill Library
 * Optimized Edition (Clean Scope, Dynamic Forms, Event Dispatching & JS API)
 *
 * @license MIT / Custom Open Source
 * @see https://github.com/yubinbango/yubinbango
 */
( function ( root, factory ) {
	'use strict';
	if ( typeof define === 'function' && define.amd ) {
		define( [], factory );
	} else if ( typeof module === 'object' && module.exports ) {
		module.exports = factory();
	} else {
		root.YubinBango = factory();
	}
}( typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : this, function () {
	'use strict';

	const DATA_URL = 'https://yubinbango.github.io/yubinbango-data/data';
	const cache = Object.create( null );
	const pendingRequests = Object.create( null );

	const PREFECTURES = [
		null,
		'北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
		'茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
		'新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
		'静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県',
		'奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県',
		'徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
		'熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
	];

	const VALID_COUNTRIES = [ 'japan', 'jp', 'jpn', '日本' ];

	const ADDRESS_FIELDS = [
		'p-region-id',
		'p-region',
		'p-locality',
		'p-street-address',
		'p-extended-address'
	];

	/**
	 * Normalize Japanese Full-width numbers & remove hyphens/spaces.
	 * @param {string} value
	 * @returns {string} 7-digit postal code string or empty string
	 */
	function normalizePostalCode( value ) {
		if ( ! value ) return '';
		const half = String( value ).replace( /[０-９]/g, function ( ch ) {
			return String.fromCharCode( ch.charCodeAt( 0 ) - 65248 );
		} );
		const digits = half.replace( /\D/g, '' );
		return digits.length === 7 ? digits : '';
	}

	/**
	 * Format address result object.
	 */
	function createAddressResult( regionId, locality, street, extended ) {
		const rId = regionId ? String( regionId ) : '';
		const rName = PREFECTURES[ Number( rId ) ] || '';
		return {
			regionId: rId,
			region: rName,
			locality: locality || '',
			street: street || '',
			extended: extended || '',
			// Backwards compatibility with legacy YubinBango object keys
			k: rId,
			l: locality || '',
			m: street || '',
			o: extended || ''
		};
	}

	/**
	 * Fetch postal data using JSONP with memory cache and deduplication.
	 * @param {string} prefix 3-digit prefix
	 * @param {Function} callback
	 */
	function fetchPostalData( prefix, callback ) {
		if ( cache[ prefix ] ) {
			callback( cache[ prefix ] );
			return;
		}

		if ( pendingRequests[ prefix ] ) {
			pendingRequests[ prefix ].push( callback );
			return;
		}

		pendingRequests[ prefix ] = [ callback ];

		const script = document.createElement( 'script' );
		script.type = 'text/javascript';
		script.charset = 'UTF-8';
		script.async = true;
		script.src = DATA_URL + '/' + prefix + '.js';

		const cleanup = function () {
			if ( script.parentNode ) {
				script.parentNode.removeChild( script );
			}
		};

		window.$yubin = function ( data ) {
			cache[ prefix ] = data || {};
			const queue = pendingRequests[ prefix ] || [];
			delete pendingRequests[ prefix ];
			cleanup();
			for ( let i = 0; i < queue.length; i++ ) {
				try {
					queue[ i ]( cache[ prefix ] );
				} catch ( err ) {
					if ( window.console && console.error ) {
						console.error( '[YubinBango] Callback error:', err );
					}
				}
			}
		};

		script.onerror = function () {
			cleanup();
			const queue = pendingRequests[ prefix ] || [];
			delete pendingRequests[ prefix ];
			for ( let i = 0; i < queue.length; i++ ) {
				queue[ i ]( {} );
			}
		};

		( document.head || document.documentElement ).appendChild( script );
	}

	/**
	 * Core Postal Code Search
	 */
	class Core {
		constructor( postalCode, callback ) {
			const normalized = normalizePostalCode( postalCode );
			if ( ! normalized ) {
				if ( typeof callback === 'function' ) {
					callback( createAddressResult() );
				}
				return;
			}

			const prefix = normalized.substring( 0, 3 );
			fetchPostalData( prefix, function ( data ) {
				const item = data && data[ normalized ];
				if ( item && item.length >= 2 ) {
					const result = createAddressResult( item[ 0 ], item[ 1 ], item[ 2 ], item[ 3 ] );
					if ( typeof callback === 'function' ) {
						callback( result );
					}
				} else if ( typeof callback === 'function' ) {
					callback( createAddressResult() );
				}
			} );
		}
	}

	/**
	 * Microformats DOM Parser & Event Binder
	 */
	class MicroformatDom {
		constructor() {
			this.scan();
			this.observe();
		}

		/**
		 * Scan and bind all .h-adr containers in the document or given scope.
		 * @param {HTMLElement|Document} [context]
		 */
		scan( context ) {
			const root = context || document;
			const containers = root.querySelectorAll ? root.querySelectorAll( '.h-adr' ) : [];
			for ( let i = 0; i < containers.length; i++ ) {
				this.bindContainer( containers[ i ] );
			}
		}

		/**
		 * Bind event listeners to a specific .h-adr element.
		 * @param {HTMLElement} container
		 */
		bindContainer( container ) {
			if ( ! container || container._yubinbangoBound ) return;
			if ( ! this.isJapan( container ) ) return;

			container._yubinbangoBound = true;
			const postalInputs = container.querySelectorAll( '.p-postal-code' );
			if ( ! postalInputs.length ) return;

			const targetInput = postalInputs[ postalInputs.length - 1 ];
			let debounceTimer = null;

			const handleInput = () => {
				clearTimeout( debounceTimer );
				debounceTimer = setTimeout( () => {
					this.lookup( container );
				}, 120 );
			};

			targetInput.addEventListener( 'input', handleInput, false );
			targetInput.addEventListener( 'change', handleInput, false );
			targetInput.addEventListener( 'paste', handleInput, false );
			targetInput.addEventListener( 'keyup', handleInput, false );
		}

		/**
		 * Check if container is configured for Japan.
		 */
		isJapan( container ) {
			const countryEl = container.querySelector( '.p-country-name' );
			if ( ! countryEl ) return true; // Default to Japan if not specified
			const country = ( countryEl.value || countryEl.textContent || '' ).trim().toLowerCase();
			return country === '' || VALID_COUNTRIES.indexOf( country ) >= 0;
		}

		/**
		 * Collect postal code digits from all .p-postal-code inputs inside container.
		 */
		collectPostalCode( container ) {
			const inputs = container.querySelectorAll( '.p-postal-code' );
			let raw = '';
			for ( let i = 0; i < inputs.length; i++ ) {
				raw += inputs[ i ].value || '';
			}
			return normalizePostalCode( raw );
		}

		/**
		 * Perform lookup and fill address fields in container.
		 */
		lookup( container ) {
			const code = this.collectPostalCode( container );
			if ( ! code ) return;

			new Core( code, ( address ) => {
				if ( ! address.region && ! address.locality ) return;
				this.fillAddress( container, address );
			} );
		}

		/**
		 * Fill address into form fields and dispatch events for validation libraries.
		 */
		fillAddress( container, address ) {
			const valueMap = {
				'p-region-id': address.regionId,
				'p-region': address.region,
				'p-locality': address.locality,
				'p-street-address': address.street,
				'p-extended-address': address.extended
			};

			// Clear previous values first
			ADDRESS_FIELDS.forEach( ( className ) => {
				const elements = container.querySelectorAll( '.' + className );
				for ( let i = 0; i < elements.length; i++ ) {
					elements[ i ].value = '';
				}
			} );

			// Populate new values and trigger change/input events
			ADDRESS_FIELDS.forEach( ( className ) => {
				const val = valueMap[ className ] || '';
				const elements = container.querySelectorAll( '.' + className );
				for ( let i = 0; i < elements.length; i++ ) {
					const el = elements[ i ];
					el.value = val;

					// Dispatch native input & change events so realtime validators catch the update
					try {
						el.dispatchEvent( new Event( 'input', { bubbles: true } ) );
						el.dispatchEvent( new Event( 'change', { bubbles: true } ) );
					} catch ( e ) {
						const evt = document.createEvent( 'HTMLEvents' );
						evt.initEvent( 'change', true, true );
						el.dispatchEvent( evt );
					}
				}
			} );

			// Dispatch a custom completion event on the container
			try {
				container.dispatchEvent( new CustomEvent( 'yubinbango:completed', {
					bubbles: true,
					detail: { address: address }
				} ) );
			} catch ( e ) {
				// Old browser fallback
			}
		}

		/**
		 * Watch for dynamically added forms/elements in the document.
		 */
		observe() {
			if ( typeof MutationObserver === 'undefined' ) return;
			const observer = new MutationObserver( ( mutations ) => {
				for ( let i = 0; i < mutations.length; i++ ) {
					const addedNodes = mutations[ i ].addedNodes;
					for ( let j = 0; j < addedNodes.length; j++ ) {
						const node = addedNodes[ j ];
						if ( node.nodeType === 1 ) {
							if ( node.classList && node.classList.contains( 'h-adr' ) ) {
								this.bindContainer( node );
							} else if ( node.querySelectorAll ) {
								this.scan( node );
							}
						}
					}
				}
			} );

			observer.observe( document.body || document.documentElement, {
				childList: true,
				subtree: true
			} );
		}
	}

	// Instance holder for auto-initialization
	let domInstance = null;

	function init() {
		if ( ! domInstance ) {
			domInstance = new MicroformatDom();
		} else {
			domInstance.scan();
		}
		return domInstance;
	}

	// Auto init when DOM is ready
	if ( typeof document !== 'undefined' ) {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', init, false );
		} else {
			init();
		}
	}

	// Export public API
	const YubinBango = {
		Core: Core,
		MicroformatDom: MicroformatDom,
		init: init,
		scan: function ( context ) {
			if ( ! domInstance ) init();
			if ( domInstance ) domInstance.scan( context );
		},
		/**
		 * Programmatic Lookup API
		 * @param {string} postalCode (e.g. "100-0001" or "1000001")
		 * @param {Function} callback (addressResult) => void
		 */
		get: function ( postalCode, callback ) {
			return new Core( postalCode, callback );
		},
		fetch: function ( postalCode, callback ) {
			return new Core( postalCode, callback );
		}
	};

	return YubinBango;
} ) );