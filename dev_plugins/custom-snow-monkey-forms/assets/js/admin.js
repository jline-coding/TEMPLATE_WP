( function () {
	'use strict';

	const data = window.csmfAdmin || {};
	const app = document.getElementById( 'csmf-admin-app' );
	const form = document.getElementById( 'csmf-settings-form' );
	if ( ! app || ! form ) return;

	const config = data.config || {};
	const fields = Array.isArray( data.fields ) ? data.fields : [];
	const strings = data.i18n || {};
	const t = ( key, fallback = '' ) => strings[ key ] || fallback;
	const uid = () => window.crypto?.randomUUID?.() || `csmf-${ Date.now() }-${ Math.random().toString( 16 ).slice( 2 ) }`;

	function node( tag, attrs = {}, children = [] ) {
		const element = document.createElement( tag );
		Object.entries( attrs ).forEach( ( [ key, value ] ) => {
			if ( key === 'class' ) element.className = value;
			else if ( key === 'text' ) element.textContent = value;
			else if ( key === 'checked' ) element.checked = Boolean( value );
			else if ( key === 'value' ) element.value = value ?? '';
			else if ( key.startsWith( 'data-' ) ) element.setAttribute( key, value );
			else element[ key ] = value;
		} );
		( Array.isArray( children ) ? children : [ children ] ).filter( Boolean ).forEach( ( child ) => element.append( typeof child === 'string' ? document.createTextNode( child ) : child ) );
		return element;
	}

	function optionList( options, selected ) {
		return Object.entries( options ).map( ( [ value, label ] ) => node( 'option', { value, text: label, selected: value === selected } ) );
	}

	function selectControl( key, options, value, label ) {
		const select = node( 'select', { 'data-key': key }, optionList( options, value ) );
		return node( 'label', { class: 'csmf-control' }, [ node( 'span', { text: label } ), select ] );
	}

	function textControl( key, value, label, type = 'text', placeholder = '' ) {
		return node( 'label', { class: 'csmf-control' }, [ node( 'span', { text: label } ), node( 'input', { type, value: value ?? '', placeholder, 'data-key': key } ) ] );
	}

	function textareaControl( key, value, label, placeholder = '' ) {
		return node( 'label', { class: 'csmf-control csmf-control--wide' }, [ node( 'span', { text: label } ), node( 'textarea', { value: value ?? '', placeholder, rows: 2, 'data-key': key } ) ] );
	}

	function checkControl( key, checked, label, help = '' ) {
		return node( 'label', { class: 'csmf-check' }, [ node( 'input', { type: 'checkbox', checked, 'data-key': key } ), node( 'span', {}, [ node( 'strong', { text: label } ), help ? node( 'small', { text: help } ) : null ] ) ] );
	}

	function fieldOptions( selected, onlyFiles = false ) {
		const list = onlyFiles ? fields.filter( ( item ) => item.type === 'file' ) : fields;
		const options = { '': t( 'select_field' ) };
		list.forEach( ( item ) => { options[ item.name ] = item.label; } );
		if ( selected && ! options[ selected ] ) options[ selected ] = `${ selected } (${ t( 'field_not_detected' ) })`;
		return options;
	}

	function emptyState( text ) { return node( 'div', { class: 'csmf-empty', text } ); }

	function panel( id, title, description ) {
		return node( 'section', { class: `csmf-panel ${ id === 'general' ? 'is-active' : '' }`, 'data-panel': id }, [
			node( 'div', { class: 'csmf-panel__head' }, [ node( 'h2', { text: title } ), node( 'p', { text: description } ) ] ),
			node( 'div', { class: 'csmf-panel__body' } ),
		] );
	}

	function addButton( label, callback ) {
		const button = node( 'button', { type: 'button', class: 'button button-secondary', text: `＋ ${ label }` } );
		button.addEventListener( 'click', callback ); return button;
	}

	function card( section, title, id ) {
		const body = node( 'div', { class: 'csmf-rule__body' } );
		const remove = node( 'button', { type: 'button', class: 'button-link-delete', text: t( 'delete' ) } );
		const result = node( 'article', { class: 'csmf-rule', 'data-section': section, 'data-id': id || uid() }, [
			node( 'header', { class: 'csmf-rule__header' }, [ node( 'h3', { text: title } ), remove ] ), body,
		] );
		remove.addEventListener( 'click', () => { result.remove(); markDirty(); updateEmptyStates(); } );
		return { card: result, body };
	}

	function conditionRow( condition = {} ) {
		const row = node( 'div', { class: 'csmf-condition', 'data-condition': '1' } );
		row.append(
			selectControl( 'field', fieldOptions( condition.field ), condition.field || '', t( 'condition_field' ) ),
			selectControl( 'operator', data.operators || {}, condition.operator || 'equals', t( 'operator' ) ),
			textControl( 'value', condition.value || '', t( 'compare_value' ), 'text', t( 'compare_value_hint' ) )
		);
		const remove = node( 'button', { type: 'button', class: 'button-link-delete csmf-condition__remove', text: t( 'delete_condition' ) } );
		remove.addEventListener( 'click', () => row.remove() ); row.appendChild( remove );
		return row;
	}

	function conditionBuilder( conditions = [] ) {
		const wrap = node( 'div', { class: 'csmf-conditions' } );
		const list = node( 'div', { class: 'csmf-conditions__list' } );
		conditions.forEach( ( condition ) => list.appendChild( conditionRow( condition ) ) );
		const add = addButton( t( 'add_condition' ), () => list.appendChild( conditionRow() ) );
		wrap.append( node( 'h4', { text: t( 'conditions' ) } ), list, add ); return wrap;
	}

	function validationCard( rule = {} ) {
		const built = card( 'validations', t( 'validation_rule' ), rule.id );
		built.body.append(
			checkControl( 'enabled', rule.enabled !== false, t( 'enabled' ) ),
			selectControl( 'field', fieldOptions( rule.field ), rule.field || '', t( 'target_field' ) ),
			selectControl( 'type', data.types || {}, rule.type || 'required', t( 'validation_type' ) ),
			textControl( 'param', rule.param || '', t( 'parameter' ), 'text', t( 'parameter_hint' ) ),
			textareaControl( 'message', rule.message || '', t( 'error_message' ), t( 'error_message_hint' ) )
		); return built.card;
	}

	function fieldRuleCard( rule = {} ) {
		const built = card( 'field_rules', t( 'display_rule' ), rule.id );
		built.body.append(
			checkControl( 'enabled', rule.enabled !== false, t( 'enabled' ) ),
			selectControl( 'target', fieldOptions( rule.target ), rule.target || '', t( 'visibility_target' ) ),
			selectControl( 'action', { show_when: t( 'show_when' ), hide_when: t( 'hide_when' ) }, rule.action || 'show_when', t( 'action' ) ),
			selectControl( 'scope', { item: t( 'scope_item' ), field: t( 'scope_field' ) }, rule.scope || 'item', t( 'target_scope' ) ),
			selectControl( 'relation', { all: t( 'relation_all' ), any: t( 'relation_any' ) }, rule.relation || 'all', t( 'relation' ) ),
			conditionBuilder( rule.conditions || [] )
		); return built.card;
	}

	function recipientCard( rule = {} ) {
		const built = card( 'recipient_rules', t( 'recipient_rule' ), rule.id );
		built.body.append(
			checkControl( 'enabled', rule.enabled !== false, t( 'enabled' ) ),
			textControl( 'label', rule.label || '', t( 'admin_label' ), 'text', t( 'admin_label_hint' ) ),
			textControl( 'priority', rule.priority ?? 10, t( 'priority' ), 'number'),
			selectControl( 'relation', { all: t( 'relation_all' ), any: t( 'relation_any' ) }, rule.relation || 'all', t( 'relation' ) ),
			conditionBuilder( rule.conditions || [] ),
			textareaControl( 'to', rule.to || '', t( 'to_required' ), t( 'to_hint' ) ),
			textareaControl( 'cc', rule.cc || '', 'Cc', t( 'comma_hint' ) ),
			textareaControl( 'bcc', rule.bcc || '', 'Bcc', t( 'comma_hint' ) ),
			textControl( 'reply_to', rule.reply_to || '', 'Reply-To', 'text', '{email}'),
			textControl( 'subject_prefix', rule.subject_prefix || '', t( 'subject_prefix' ), 'text', t( 'subject_hint' ) )
		); return built.card;
	}

	function uploadCard( rule = {} ) {
		const built = card( 'uploads', t( 'upload_rule' ), rule.id );
		built.body.append(
			checkControl( 'enabled', rule.enabled !== false, t( 'enabled' ) ),
			selectControl( 'field', fieldOptions( rule.field, true ), rule.field || '', t( 'file_field' ) ),
			checkControl( 'required', Boolean( rule.required ), t( 'image_required' ) ),
			textControl( 'extensions', rule.extensions || 'jpg,jpeg,png,webp,pdf', t( 'extensions' ), 'text', 'jpg,jpeg,png,webp,pdf,docx,xlsx,zip,csv,txt' ),
			textControl( 'max_mb', rule.max_mb ?? 5, t( 'max_mb' ), 'number' ),
			textControl( 'min_width', rule.min_width || 0, t( 'min_width' ), 'number' ),
			textControl( 'max_width', rule.max_width || 0, t( 'max_width' ), 'number' ),
			textControl( 'min_height', rule.min_height || 0, t( 'min_height' ), 'number' ),
			textControl( 'max_height', rule.max_height || 0, t( 'max_height' ), 'number' ),
			checkControl( 'attach_admin', rule.attach_admin !== false, t( 'attach_admin' ), t( 'attach_admin_help' ) ),
			checkControl( 'attach_reply', Boolean( rule.attach_reply ), t( 'attach_reply' ), t( 'attach_reply_help' ) )
		); return built.card;
	}

	const panels = {
		general: panel( 'general', t( 'general_title' ), t( 'general_description' ) ),
		validation: panel( 'validation', t( 'validation_title' ), t( 'validation_description' ) ),
		conditions: panel( 'conditions', t( 'conditions_title' ), t( 'conditions_description' ) ),
		recipients: panel( 'recipients', t( 'recipients_title' ), t( 'recipients_description' ) ),
		uploads: panel( 'uploads', t( 'uploads_title' ), t( 'uploads_description' ) ),
		diagnostics: panel( 'diagnostics', t( 'diagnostics_title' ), t( 'diagnostics_description' ) ),
	};
	Object.values( panels ).forEach( ( item ) => app.appendChild( item ) );

	const generalBody = panels.general.querySelector( '.csmf-panel__body' );
	generalBody.append(
		node( 'div', { class: 'csmf-settings-grid' }, [
			checkControl( 'enabled', config.enabled !== false, t( 'enable_form' ) ),
			checkControl( 'realtime', config.realtime !== false, t( 'enable_realtime' ) ),
			checkControl( 'hide_error_empty', config.hide_error_empty !== false, t( 'hide_error_empty' ) ),
			checkControl( 'focus_first_error', config.focus_first_error !== false, t( 'focus_first_error' ) ),
			checkControl( 'clear_hidden', config.clear_hidden !== false, t( 'clear_hidden' ), t( 'clear_hidden_help' ) ),
			checkControl( 'delete_on_uninstall', Boolean( config.delete_on_uninstall ), t( 'delete_uninstall' ) ),
			selectControl( 'validate_on', { blur_input: t( 'timing_blur_input' ), input: t( 'timing_input' ), blur: t( 'timing_blur' ) }, config.validate_on || 'blur_input', t( 'validation_timing' ) ),
			selectControl( 'route_mode', { first_match: t( 'route_first' ), merge_all: t( 'route_merge' ) }, config.route_mode || 'first_match', t( 'route_mode' ) ),
		] )
	);

	function rulePanel( panelElement, section, items, factory, addLabel ) {
		const body = panelElement.querySelector( '.csmf-panel__body' );
		const list = node( 'div', { class: 'csmf-rule-list', 'data-list': section } );
		items.forEach( ( item ) => list.appendChild( factory( item ) ) );
		body.append( list, addButton( addLabel, () => { list.appendChild( factory() ); markDirty(); updateEmptyStates(); } ) );
	}

	rulePanel( panels.validation, 'validations', config.validations || [], validationCard, t( 'add_validation' ) );
	rulePanel( panels.conditions, 'field_rules', config.field_rules || [], fieldRuleCard, t( 'add_display' ) );
	rulePanel( panels.recipients, 'recipient_rules', config.recipient_rules || [], recipientCard, t( 'add_recipient' ) );
	rulePanel( panels.uploads, 'uploads', config.uploads || [], uploadCard, t( 'add_upload' ) );

	const diagnostics = panels.diagnostics.querySelector( '.csmf-panel__body' );
	diagnostics.append( node( 'div', { class: 'csmf-diagnostics' }, [
		node( 'div', {}, [ node( 'strong', { text: t( 'addon' ) } ), node( 'span', { text: t( 'active' ) } ) ] ),
		node( 'div', {}, [ node( 'strong', { text: t( 'detected_fields' ) } ), node( 'span', { text: String( fields.length ) } ) ] ),
		node( 'div', {}, [ node( 'strong', { text: t( 'browser' ) } ), node( 'span', { text: navigator.userAgent } ) ] ),
	] ) );
	const fieldTable = node( 'table', { class: 'widefat striped csmf-field-table' }, [ node( 'thead', {}, node( 'tr', {}, [ node( 'th', { text: 'name' } ), node( 'th', { text: t( 'type' ) } ) ] ) ) ] );
	const tbody = node( 'tbody' ); fields.forEach( ( field ) => tbody.appendChild( node( 'tr', {}, [ node( 'td', { text: field.name } ), node( 'td', { text: field.type } ) ] ) ) ); fieldTable.appendChild( tbody ); diagnostics.appendChild( fieldTable );

	function readControls( container, includeConditions = false ) {
		const result = {};
		Array.from( container.querySelectorAll( ':scope > [data-key], :scope > .csmf-control > [data-key], :scope > .csmf-check > [data-key]' ) ).forEach( ( control ) => {
			result[ control.dataset.key ] = control.type === 'checkbox' ? control.checked : control.value;
		} );
		if ( includeConditions ) result.conditions = Array.from( container.querySelectorAll( '[data-condition]' ) ).map( ( row ) => readControls( row ) );
		return result;
	}

	function collect() {
		const result = readControls( generalBody.querySelector( '.csmf-settings-grid' ) );
		[ 'validations', 'field_rules', 'recipient_rules', 'uploads' ].forEach( ( section ) => {
			result[ section ] = Array.from( app.querySelectorAll( `[data-section="${ section }"]` ) ).map( ( item ) => ( { id: item.dataset.id, ...readControls( item.querySelector( '.csmf-rule__body' ), [ 'field_rules', 'recipient_rules' ].includes( section ) ) } ) );
		} );
		return result;
	}

	function markDirty() { document.querySelector( '.csmf-save-status' ).textContent = t( 'dirty' ); }
	function updateEmptyStates() {
		app.querySelectorAll( '.csmf-rule-list' ).forEach( ( list ) => {
			list.querySelector( '.csmf-empty' )?.remove();
			if ( ! list.querySelector( '.csmf-rule' ) ) list.appendChild( emptyState( t( 'empty_rules' ) ) );
		} );
	}

	app.addEventListener( 'input', markDirty ); app.addEventListener( 'change', markDirty );
	form.addEventListener( 'submit', () => { document.getElementById( 'csmf-config-json' ).value = JSON.stringify( collect() ); document.querySelector( '.csmf-save-status' ).textContent = t( 'saving' ); } );
	document.querySelectorAll( '.nav-tab[data-tab]' ).forEach( ( tab ) => tab.addEventListener( 'click', () => {
		document.querySelectorAll( '.nav-tab[data-tab]' ).forEach( ( item ) => item.classList.toggle( 'nav-tab-active', item === tab ) );
		app.querySelectorAll( '.csmf-panel' ).forEach( ( item ) => item.classList.toggle( 'is-active', item.dataset.panel === tab.dataset.tab ) );
	} ) );
	const selector = document.getElementById( 'csmf-form-selector' ); selector?.addEventListener( 'change', () => { window.location.href = `${ selector.dataset.baseUrl }&form_id=${ encodeURIComponent( selector.value ) }`; } );
	const languageSelector = document.getElementById( 'csmf-admin-language' ); languageSelector?.addEventListener( 'change', () => languageSelector.form.submit() );
	updateEmptyStates();
}() );
