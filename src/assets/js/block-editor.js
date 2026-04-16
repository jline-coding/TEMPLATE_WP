(function (wp) {

    const { addFilter } = wp.hooks;
    const { InspectorControls } = wp.blockEditor;
    const {
        PanelBody,
        RangeControl,
        SelectControl,
        ColorPalette,
        ToggleControl,
        BaseControl
    } = wp.components;

    const { createElement: el, Fragment } = wp.element;

    /* ================================
       フォント設定
    ================================= */

    const FONT_FAMILIES = [
        { label: 'デフォルト', value: '' },
        { label: 'Noto Sans JP', value: '"Noto Sans JP", sans-serif' },
        { label: 'Fira Sans Condensed', value: '"Fira Sans Condensed", sans-serif' },
        { label: 'Fira Sans', value: '"Fira Sans", sans-serif' },
        { label: 'Shippori Mincho', value: '"Shippori Mincho", serif' },
        { label: 'Ubuntu', value: '"Ubuntu", sans-serif' },
    ];

    const FONT_WEIGHTS = [
        { label: 'デフォルト', value: '' },
        ...[100,200,300,400,500,600,700,800,900].map(v => ({
            label: String(v),
            value: String(v)
        }))
    ];

    /* ================================
       スタイルパネル（TH / TD）
    ================================= */

    const renderTextStylePanel = ({
        panelTitle,
        style = {},
        setAttributes,
        attrKey
    }) => {

        const updateStyle = (newStyle) =>
            setAttributes({
                [attrKey]: Object.assign({}, style, newStyle)
            });

        const paddingMode = style.paddingMode || 'all';

        return el(
            PanelBody,
            { title: panelTitle, initialOpen: false },

            el(BaseControl,
                { label: 'テキストカラー' },
                el(ColorPalette, {
                    value: style.color,
                    onChange: (v) => updateStyle({ color: v })
                })
            ),

            el(BaseControl,
                { label: '背景色' },
                el(ColorPalette, {
                    value: style.backgroundColor,
                    onChange: (v) => updateStyle({ backgroundColor: v })
                })
            ),

            el(SelectControl, {
                label: 'フォントファミリー',
                value: style.fontFamily || '',
                options: FONT_FAMILIES,
                onChange: (v) => updateStyle({ fontFamily: v })
            }),

            el(SelectControl, {
                label: 'フォントウェイト',
                value: style.fontWeight || '',
                options: FONT_WEIGHTS,
                onChange: (v) => updateStyle({ fontWeight: v })
            }),

            el(RangeControl, {
                label: 'フォントサイズ（px）',
                min: 12,
                max: 200,
                value: style.fontSize ? parseInt(style.fontSize) : 0,
                onChange: (v) => updateStyle({ fontSize: v + 'px' })
            }),

            el(SelectControl, {
                label: '内側余白の設定方法',
                value: paddingMode,
                options: [
                    { label: '全体（4辺同じ）', value: 'all' },
                    { label: '上下 / 左右', value: 'vertical-horizontal' },
                    { label: '個別（4辺別々）', value: 'individual' }
                ],
                onChange: (v) => updateStyle({ paddingMode: v })
            }),

            paddingMode === 'all' && el(RangeControl, {
                label: '内側余白（全体・px）',
                min: 0,
                max: 100,
                value: style.padding ? parseInt(style.padding) : 0,
                onChange: (v) => updateStyle({ padding: v + 'px' })
            }),

            paddingMode === 'vertical-horizontal' && el(Fragment, {},
                el(RangeControl, {
                    label: '上下（Top / Bottom）',
                    min: 0,
                    max: 100,
                    value: style.paddingVertical ? parseInt(style.paddingVertical) : 0,
                    onChange: (v) => updateStyle({ paddingVertical: v + 'px' })
                }),
                el(RangeControl, {
                    label: '左右（Left / Right）',
                    min: 0,
                    max: 100,
                    value: style.paddingHorizontal ? parseInt(style.paddingHorizontal) : 0,
                    onChange: (v) => updateStyle({ paddingHorizontal: v + 'px' })
                })
            ),

            paddingMode === 'individual' && el(Fragment, {},
                ['Top','Right','Bottom','Left'].map((dir) =>
                    el(RangeControl, {
                        label: dir,
                        min: 0,
                        max: 100,
                        value: style['padding'+dir] ? parseInt(style['padding'+dir]) : 0,
                        onChange: (v) => updateStyle({ ['padding'+dir]: v + 'px' })
                    })
                )
            ),

            el(RangeControl, {
                label: '行間（line-height）',
                min: 1,
                max: 3,
                step: 0.1,
                value: style.lineHeight || 1.6,
                onChange: (v) => updateStyle({ lineHeight: v })
            }),

            el(ToggleControl, {
                label: '枠線を表示する',
                checked: style.borderEnabled || false,
                onChange: (v) => updateStyle({ borderEnabled: v })
            }),

            style.borderEnabled && el(BaseControl,
                { label: '枠線カラー' },
                el(ColorPalette, {
                    value: style.borderColor,
                    onChange: (v) => updateStyle({ borderColor: v })
                })
            ),

            style.borderEnabled && el(RangeControl, {
                label: '枠線の太さ（px）',
                min: 1,
                max: 10,
                value: style.borderWidth ? parseInt(style.borderWidth) : 1,
                onChange: (v) => updateStyle({ borderWidth: v + 'px' })
            })
        );
    };

    /* ================================
       列幅設定
    ================================= */

    const renderColumnWidthPanel = ({ attributes, setAttributes }) => {

        const columnCount =
            attributes.body?.[0]?.cells?.length || 0;

        const updateColumnWidth = (index, value) => {
            const newWidths = Object.assign({}, attributes.columnWidths || {});
            newWidths[index] = value + '%';
            setAttributes({ columnWidths: newWidths });
        };

        const clearColumnWidth = (index) => {
            const newWidths = Object.assign({}, attributes.columnWidths || {});
            delete newWidths[index];
            setAttributes({ columnWidths: newWidths });
        };

        return el(
            PanelBody,
            { title: '列幅設定', initialOpen: false },

            Array.from({ length: columnCount }).map((_, i) =>
                el(Fragment, {},
                    el(RangeControl, {
                        label: '列 ' + (i + 1) + ' の幅（%）',
                        min: 5,
                        max: 100,
                        value: attributes.columnWidths?.[i]
                            ? parseInt(attributes.columnWidths[i])
                            : 0,
                        onChange: (v) => updateColumnWidth(i, v)
                    }),
                    attributes.columnWidths?.[i] &&
                    el('button', {
                        className: 'components-button is-link is-destructive',
                        onClick: () => clearColumnWidth(i)
                    }, 'クリア')
                )
            )
        );
    };

    /* ================================
       属性追加
    ================================= */

    addFilter(
        'blocks.registerBlockType',
        'custom/table-attributes',
        function (settings, name) {

            if (name !== 'core/table') return settings;

            settings.attributes = Object.assign({}, settings.attributes, {
                thStyle: { type: 'object', default: {} },
                tdStyle: { type: 'object', default: {} },
                columnWidths: { type: 'object', default: {} }
            });

            return settings;
        }
    );

    /* ================================
       サイドバー追加
    ================================= */

    addFilter(
        'editor.BlockEdit',
        'custom/table-controls',
        function (BlockEdit) {

            return function (props) {

                if (props.name !== 'core/table') {
                    return el(BlockEdit, props);
                }

                const { attributes, setAttributes } = props;

                return el(
                    Fragment,
                    {},
                    el(BlockEdit, props),

                    el(
                        InspectorControls,
                        {},

                        renderTextStylePanel({
                            panelTitle: 'TH スタイル設定',
                            style: attributes.thStyle,
                            setAttributes,
                            attrKey: 'thStyle'
                        }),

                        renderTextStylePanel({
                            panelTitle: 'TD スタイル設定',
                            style: attributes.tdStyle,
                            setAttributes,
                            attrKey: 'tdStyle'
                        }),

                        renderColumnWidthPanel({
                            attributes,
                            setAttributes
                        })
                    )
                );
            };
        }
    );

    /* ================================
       エディター内プレビュー
    ================================= */
    addFilter(
        'editor.BlockListBlock',
        'custom/table-live-style',
        function (BlockListBlock) {

            return function (props) {

                if (props.name !== 'core/table') {
                    return el(BlockListBlock, props);
                }

                const { thStyle = {}, tdStyle = {}, columnWidths = {} } = props.attributes;

                let vars = {};

                const addVar = (name, value) => {
                    if (value !== undefined && value !== '') {
                        vars[`--${name}`] = value;
                    }
                };

                /* ================= NORMALIZE PADDING ================= */

                const applyPaddingVars = (prefix, style) => {

                    const mode = style.paddingMode || 'all';

                    let top, right, bottom, left;

                    if (mode === 'all') {
                        top = right = bottom = left = style.padding;
                    }

                    if (mode === 'vertical-horizontal') {
                        top = bottom = style.paddingVertical;
                        right = left = style.paddingHorizontal;
                    }

                    if (mode === 'individual') {
                        top = style.paddingTop;
                        right = style.paddingRight;
                        bottom = style.paddingBottom;
                        left = style.paddingLeft;
                    }

                    addVar(`${prefix}-padding-top`, top);
                    addVar(`${prefix}-padding-right`, right);
                    addVar(`${prefix}-padding-bottom`, bottom);
                    addVar(`${prefix}-padding-left`, left);
                };

                /* ================= TH ================= */

                addVar('th-bg', thStyle.backgroundColor);
                addVar('th-color', thStyle.color);
                addVar('th-font-size', thStyle.fontSize);
                addVar('th-line-height', thStyle.lineHeight);
                addVar('th-font-family', thStyle.fontFamily);
                addVar('th-font-weight', thStyle.fontWeight);

                if (thStyle.borderEnabled) {
                    addVar('th-border-width', thStyle.borderWidth || '1px');
                    addVar('th-border-color', thStyle.borderColor || '#000');
                }

                applyPaddingVars('th', thStyle);

                /* ================= TD ================= */

                addVar('td-bg', tdStyle.backgroundColor);
                addVar('td-color', tdStyle.color);
                addVar('td-font-size', tdStyle.fontSize);
                addVar('td-line-height', tdStyle.lineHeight);
                addVar('td-font-family', tdStyle.fontFamily);
                addVar('td-font-weight', tdStyle.fontWeight);

                if (tdStyle.borderEnabled) {
                    addVar('td-border-width', tdStyle.borderWidth || '1px');
                    addVar('td-border-color', tdStyle.borderColor || '#000');
                }

                applyPaddingVars('td', tdStyle);

                /* ================= COLUMN WIDTH (inject CSS) ================= */

                let columnCss = '';

                const wrapperClass = (props.wrapperProps && props.wrapperProps.className)
                    ? props.wrapperProps.className
                    : 'wp-block-table';

                const wrapperSelector = wrapperClass
                    .split(/\s+/)
                    .filter(c => c)
                    .map(c => `.${c}`)
                    .join('');

                Object.keys(columnWidths).forEach((index) => {
                    if (columnWidths[index]) {
                        const col = parseInt(index, 10) + 1;
                        columnCss += `${wrapperSelector} table td:nth-child(${col}), ${wrapperSelector} table th:nth-child(${col}){width:${columnWidths[index]};}\n`;
                    }
                });

                return el(Fragment, {},
                    columnCss && el('style', {}, columnCss),
                    el(BlockListBlock, {
                        ...props,
                        wrapperProps: {
                            ...props.wrapperProps,
                            style: {
                                ...props.wrapperProps?.style,
                                ...vars
                            }
                        }
                    })
                );
            };
        }
    );

    addFilter(
        'blocks.getSaveContent.extraProps',
        'custom/table-save-style',
        function (extraProps, blockType, attributes) {

            if (blockType.name !== 'core/table') {
                return extraProps;
            }

            const { thStyle = {}, tdStyle = {}, columnWidths = {} } = attributes;

            let vars = {};

            const addVar = (name, value) => {
                if (value !== undefined && value !== '') {
                    vars[`--${name}`] = value;
                }
            };

            const applyPaddingVars = (prefix, style) => {

                const mode = style.paddingMode || 'all';

                let top, right, bottom, left;

                if (mode === 'all') {
                    top = right = bottom = left = style.padding;
                }

                if (mode === 'vertical-horizontal') {
                    top = bottom = style.paddingVertical;
                    right = left = style.paddingHorizontal;
                }

                if (mode === 'individual') {
                    top = style.paddingTop;
                    right = style.paddingRight;
                    bottom = style.paddingBottom;
                    left = style.paddingLeft;
                }

                addVar(`${prefix}-padding-top`, top);
                addVar(`${prefix}-padding-right`, right);
                addVar(`${prefix}-padding-bottom`, bottom);
                addVar(`${prefix}-padding-left`, left);
            };

            /* TH */
            addVar('th-bg', thStyle.backgroundColor);
            addVar('th-color', thStyle.color);
            addVar('th-font-size', thStyle.fontSize);
            addVar('th-line-height', thStyle.lineHeight);
            addVar('th-font-family', thStyle.fontFamily);
            addVar('th-font-weight', thStyle.fontWeight);

            if (thStyle.borderEnabled) {
                addVar('th-border-width', thStyle.borderWidth || '1px');
                addVar('th-border-color', thStyle.borderColor || '#000');
            }

            applyPaddingVars('th', thStyle);

            /* TD */
            addVar('td-bg', tdStyle.backgroundColor);
            addVar('td-color', tdStyle.color);
            addVar('td-font-size', tdStyle.fontSize);
            addVar('td-line-height', tdStyle.lineHeight);
            addVar('td-font-family', tdStyle.fontFamily);
            addVar('td-font-weight', tdStyle.fontWeight);

            if (tdStyle.borderEnabled) {
                addVar('td-border-width', tdStyle.borderWidth || '1px');
                addVar('td-border-color', tdStyle.borderColor || '#000');
            }

            applyPaddingVars('td', tdStyle);

            Object.keys(columnWidths).forEach((index) => {
                if (columnWidths[index]) {
                }
            });

            extraProps.style = {
                ...extraProps.style,
                ...vars
            };

            return extraProps;
        }
    );

    addFilter(
        'blocks.getSaveElement',
        'custom/table-save-inline-css',
        function (element, blockType, attributes) {

            if (blockType.name !== 'core/table') {
                return element;
            }

            const { columnWidths = {} } = attributes;

            let css = '';

            Object.keys(columnWidths).forEach((index) => {
                if (columnWidths[index]) {
                    const col = parseInt(index, 10) + 1;

                    const elementClass = (element && element.props && element.props.className)
                        ? element.props.className
                        : 'wp-block-table';

                    const selector = elementClass
                        .split(/\s+/)
                        .filter(c => c)
                        .map(c => `.${c}`)
                        .join('');

                    css += `${selector} table td:nth-child(${col}), ${selector} table th:nth-child(${col}){width:${columnWidths[index]};}\n`;
                }
            });

            if (!css) return element;

            return el(Fragment, {}, el('style', {}, css), element);
        }
    );

})(window.wp);

