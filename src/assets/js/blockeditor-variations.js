(function (wp) {

    const wp_i18n = wp.i18n;
    
    // Custom i18n translation based on PHP wp_localize_script (No .mo file required)
    const customI18n = window.themeData?.i18n || {};
    const __ = (text, domain) => {
        return customI18n[text] || wp_i18n.__(text, domain);
    };

    /**
     * =========================================================================
     * CUSTOM BLOCK: original/table (Nâng cao giống Flexible Table)
     * Hệ thống Grid Math giúp tính toán Merge / Split ô tự động
     * Hỗ trợ Chọn Nhiều Ô (Multi-Select) để chỉnh Style chung
     * =========================================================================
     */
    const { useState, Fragment, createElement: el } = wp.element;
    const { TextControl, PanelBody, Button, ButtonGroup, ToolbarGroup, ToolbarButton, BaseControl, ColorPalette, SelectControl, RangeControl } = wp.components;
    const { RichText, InspectorControls, BlockControls } = wp.blockEditor;

    const FONT_FAMILIES = [
        { label: __('Mặc định', 'mytheme'), value: '' },
        { label: 'Noto Sans JP', value: '"Noto Sans JP", sans-serif' },
        { label: 'Fira Sans Condensed', value: '"Fira Sans Condensed", sans-serif' },
        { label: 'Fira Sans', value: '"Fira Sans", sans-serif' },
        { label: 'Shippori Mincho', value: '"Shippori Mincho", serif' },
        { label: 'Ubuntu', value: '"Ubuntu", sans-serif' },
    ];

    const FONT_WEIGHTS = [
        { label: __('Mặc định', 'mytheme'), value: '' },
        ...[100,200,300,400,500,600,700,800,900].map(v => ({ label: String(v), value: String(v) }))
    ];

    wp.blocks.registerBlockType('original/table', {
        title: __('Flexible Table (Original)', 'mytheme'),
        icon: 'grid-view',
        category: 'text',
        attributes: {
            rows: {
                type: 'array',
                default: [
                    { cells: [ { content: 'Cell 1', tag: 'td', rowspan: 1, colspan: 1, style: {} }, { content: 'Cell 2', tag: 'td', rowspan: 1, colspan: 1, style: {} } ] },
                    { cells: [ { content: 'Cell 3', tag: 'td', rowspan: 1, colspan: 1, style: {} }, { content: 'Cell 4', tag: 'td', rowspan: 1, colspan: 1, style: {} } ] }
                ]
            }
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const [selectedCells, setSelectedCells] = useState([]); // Array of { r, c }

            // Lấy ma trận lưới (Grid Math) để biết vị trí thực tế của từng ô
            const buildGrid = (rows) => {
                const grid = [];
                rows.forEach((row, r) => {
                    if (!grid[r]) grid[r] = [];
                    let colIndex = 0;
                    row.cells.forEach((cell, c) => {
                        while (grid[r][colIndex]) colIndex++; 
                        const startCol = colIndex;
                        for (let rs = 0; rs < (cell.rowspan || 1); rs++) {
                            for (let cs = 0; cs < (cell.colspan || 1); cs++) {
                                if (!grid[r + rs]) grid[r + rs] = [];
                                grid[r + rs][startCol + cs] = { r, c, cell };
                            }
                        }
                    });
                });
                return grid;
            };

            const grid = buildGrid(attributes.rows);
            
            const getVCol = (r, c) => {
                for (let i = 0; i < grid[r].length; i++) {
                    if (grid[r][i] && grid[r][i].r === r && grid[r][i].c === c) return i;
                }
                return -1;
            };

            const updateCellContent = (r, c, content) => {
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                newRows[r].cells[c].content = content;
                setAttributes({ rows: newRows });
            };

            const updateCellTag = (tag) => {
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                selectedCells.forEach(sel => {
                    newRows[sel.r].cells[sel.c].tag = tag;
                });
                setAttributes({ rows: newRows });
            };

            const updateCellStyle = (styleProps) => {
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                selectedCells.forEach(sel => {
                    if (!newRows[sel.r].cells[sel.c].style) newRows[sel.r].cells[sel.c].style = {};
                    // Loại bỏ thuộc tính nếu giá trị là undefined hoặc rỗng (để dọn rác CSS)
                    Object.keys(styleProps).forEach(key => {
                        if (styleProps[key] === undefined || styleProps[key] === '') {
                            delete newRows[sel.r].cells[sel.c].style[key];
                        } else {
                            newRows[sel.r].cells[sel.c].style[key] = styleProps[key];
                        }
                    });
                });
                setAttributes({ rows: newRows });
            };

            // ================== THAO TÁC GỘP & TÁCH Ô ==================
            const mergeRight = () => {
                if (selectedCells.length !== 1) return;
                const { r, c } = selectedCells[0];
                const vCol = getVCol(r, c);
                const targetCell = attributes.rows[r].cells[c];
                const rightVCol = vCol + (targetCell.colspan || 1);
                
                if (!grid[r] || !grid[r][rightVCol]) return alert(__('Không có ô bên phải để gộp!', 'mytheme'));
                const rightCellInfo = grid[r][rightVCol];
                if (rightCellInfo.r !== r) return alert(__('Ô bên phải thuộc hàng khác, không thể gộp!', 'mytheme'));
                
                const rightCell = rightCellInfo.cell;
                if ((targetCell.rowspan || 1) !== (rightCell.rowspan || 1)) return alert(__('Hai ô phải có cùng chiều cao (Rowspan) để gộp!', 'mytheme'));

                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                newRows[r].cells[c].colspan = (targetCell.colspan || 1) + (rightCell.colspan || 1);
                if (rightCell.content) newRows[r].cells[c].content += ' ' + rightCell.content;
                newRows[rightCellInfo.r].cells.splice(rightCellInfo.c, 1);
                
                setAttributes({ rows: newRows });
            };

            const mergeDown = () => {
                if (selectedCells.length !== 1) return;
                const { r, c } = selectedCells[0];
                const vCol = getVCol(r, c);
                const targetCell = attributes.rows[r].cells[c];
                const downVRow = r + (targetCell.rowspan || 1);
                
                if (!grid[downVRow] || !grid[downVRow][vCol]) return alert(__('Không có ô bên dưới để gộp!', 'mytheme'));
                const downCellInfo = grid[downVRow][vCol];
                const downCell = downCellInfo.cell;
                
                if ((targetCell.colspan || 1) !== (downCell.colspan || 1)) return alert(__('Hai ô phải có cùng chiều rộng (Colspan) để gộp!', 'mytheme'));
                if (getVCol(downCellInfo.r, downCellInfo.c) !== vCol) return alert(__('Ô bên dưới không căn lề khớp với ô này!', 'mytheme'));

                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                newRows[r].cells[c].rowspan = (targetCell.rowspan || 1) + (downCell.rowspan || 1);
                if (downCell.content) newRows[r].cells[c].content += '<br>' + downCell.content;
                newRows[downCellInfo.r].cells.splice(downCellInfo.c, 1);
                
                setAttributes({ rows: newRows });
            };

            const splitCell = () => {
                if (selectedCells.length !== 1) return;
                const { r, c } = selectedCells[0];
                const targetCell = attributes.rows[r].cells[c];
                const rs = targetCell.rowspan || 1;
                const cs = targetCell.colspan || 1;
                if (rs === 1 && cs === 1) return;
                
                const vCol = getVCol(r, c);
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                
                newRows[r].cells[c].rowspan = 1;
                newRows[r].cells[c].colspan = 1;
                
                for (let i = 0; i < cs - 1; i++) {
                    newRows[r].cells.splice(c + 1, 0, { content: '', tag: targetCell.tag, rowspan: 1, colspan: 1, style: {} });
                }
                
                for (let i = 1; i < rs; i++) {
                    const targetR = r + i;
                    let insertIdx = 0;
                    for (let col = 0; col < vCol; col++) {
                        if (grid[targetR][col] && grid[targetR][col].r === targetR) {
                            insertIdx = Math.max(insertIdx, grid[targetR][col].c + 1);
                        }
                    }
                    for (let k = 0; k < cs; k++) {
                        newRows[targetR].cells.splice(insertIdx + k, 0, { content: '', tag: targetCell.tag, rowspan: 1, colspan: 1, style: {} });
                    }
                }
                setAttributes({ rows: newRows });
                setSelectedCells([{ r, c }]); // Maintain selection on the top-left cell
            };

            const mergeSelected = () => {
                if (selectedCells.length <= 1) return;
                
                const cellsData = selectedCells.map(sel => {
                    const cell = attributes.rows[sel.r].cells[sel.c];
                    return {
                        r: sel.r,
                        c: sel.c,
                        vRow: sel.r,
                        vCol: getVCol(sel.r, sel.c),
                        rs: cell.rowspan || 1,
                        cs: cell.colspan || 1,
                        content: cell.content
                    };
                });

                const minR = Math.min(...cellsData.map(c => c.vRow));
                const maxR = Math.max(...cellsData.map(c => c.vRow + c.rs - 1));
                const minC = Math.min(...cellsData.map(c => c.vCol));
                const maxC = Math.max(...cellsData.map(c => c.vCol + c.cs - 1));

                const targetArea = (maxR - minR + 1) * (maxC - minC + 1);
                const selectedArea = cellsData.reduce((sum, c) => sum + (c.rs * c.cs), 0);

                if (targetArea !== selectedArea) {
                    return alert(__('Vùng chọn phải tạo thành một hình chữ nhật hoàn chỉnh để có thể gộp!', 'mytheme'));
                }

                const topLeft = cellsData.find(c => c.vRow === minR && c.vCol === minC);
                if (!topLeft) return alert(__('Lỗi thuật toán tìm ô gốc!', 'mytheme'));

                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                
                cellsData.sort((a, b) => a.vRow - b.vRow || a.vCol - b.vCol);
                const combinedContent = cellsData.map(c => c.content).filter(Boolean).join('<br>');
                
                newRows[topLeft.r].cells[topLeft.c].rowspan = maxR - minR + 1;
                newRows[topLeft.r].cells[topLeft.c].colspan = maxC - minC + 1;
                newRows[topLeft.r].cells[topLeft.c].content = combinedContent;

                cellsData.sort((a, b) => b.r - a.r || b.c - a.c);
                cellsData.forEach(c => {
                    if (!(c.r === topLeft.r && c.c === topLeft.c)) {
                        newRows[c.r].cells.splice(c.c, 1);
                    }
                });

                setAttributes({ rows: newRows });
                setSelectedCells([{ r: topLeft.r, c: topLeft.c }]);
            };

            // ================== THÊM / XÓA HÀNG CỘT ==================
            const addRow = (offset) => {
                if (selectedCells.length !== 1) return;
                const { r } = selectedCells[0];
                const index = r + offset;
                const cols = grid[0].length;
                const newRow = { cells: Array(cols).fill(0).map(() => ({ content: '', tag: 'td', rowspan: 1, colspan: 1, style: {} })) };
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                newRows.splice(index, 0, newRow);
                setAttributes({ rows: newRows });
                setSelectedCells([]);
            };

            const addColumn = (offset) => {
                if (selectedCells.length !== 1) return;
                const { r, c } = selectedCells[0];
                const vColIndex = getVCol(r, c) + offset;
                
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                newRows.forEach((row, rowIdx) => {
                    let insertIdx = 0;
                    let shouldInsert = true;
                    if (grid[rowIdx][vColIndex]) {
                        const cellInfo = grid[rowIdx][vColIndex];
                        const cellVCol = getVCol(cellInfo.r, cellInfo.c);
                        if (cellVCol < vColIndex && cellVCol + (cellInfo.cell.colspan || 1) > vColIndex) {
                            if (cellInfo.r === rowIdx) { 
                                newRows[rowIdx].cells[cellInfo.c].colspan = (newRows[rowIdx].cells[cellInfo.c].colspan || 1) + 1;
                            }
                            shouldInsert = false;
                        } else {
                            insertIdx = cellInfo.r === rowIdx ? cellInfo.c : newRows[rowIdx].cells.length;
                        }
                    } else {
                        insertIdx = newRows[rowIdx].cells.length;
                    }
                    if (shouldInsert) {
                        newRows[rowIdx].cells.splice(insertIdx, 0, { content: '', tag: 'td', rowspan: 1, colspan: 1, style: {} });
                    }
                });
                setAttributes({ rows: newRows });
                setSelectedCells([]);
            };

            const deleteRow = () => {
                if (selectedCells.length !== 1) return;
                const { r } = selectedCells[0];
                if (attributes.rows.length <= 1) return alert(__('Không thể xóa hàng cuối cùng!', 'mytheme'));
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                newRows.splice(r, 1);
                setAttributes({ rows: newRows });
                setSelectedCells([]);
            };

            const deleteColumn = () => {
                if (selectedCells.length !== 1) return;
                const { r, c } = selectedCells[0];
                const vColIndex = getVCol(r, c);
                if (grid[0].length <= 1) return alert(__('Không thể xóa cột cuối cùng!', 'mytheme'));
                const newRows = JSON.parse(JSON.stringify(attributes.rows));
                newRows.forEach((row, rowIdx) => {
                    if (grid[rowIdx][vColIndex]) {
                        const cellInfo = grid[rowIdx][vColIndex];
                        if (cellInfo.r === rowIdx) {
                            if ((cellInfo.cell.colspan || 1) > 1) {
                                newRows[rowIdx].cells[cellInfo.c].colspan -= 1;
                            } else {
                                newRows[rowIdx].cells.splice(cellInfo.c, 1);
                            }
                        }
                    }
                });
                setAttributes({ rows: newRows });
                setSelectedCells([]);
            };

            // ================== CHỌN NHIỀU Ô NHANH ==================
            const selectAll = () => {
                const all = [];
                attributes.rows.forEach((row, r) => {
                    row.cells.forEach((cell, c) => all.push({ r, c }));
                });
                setSelectedCells(all);
            };

            const selectRow = () => {
                if (selectedCells.length !== 1) return;
                const r = selectedCells[0].r;
                const rowCells = [];
                const seen = new Set();
                for (let i = 0; i < grid[r].length; i++) {
                    if (grid[r][i]) {
                        const key = grid[r][i].r + '-' + grid[r][i].c;
                        if (!seen.has(key)) {
                            seen.add(key);
                            rowCells.push({ r: grid[r][i].r, c: grid[r][i].c });
                        }
                    }
                }
                setSelectedCells(rowCells);
            };

            const selectCol = () => {
                if (selectedCells.length !== 1) return;
                const vCol = getVCol(selectedCells[0].r, selectedCells[0].c);
                const colCells = [];
                const seen = new Set();
                for (let r = 0; r < grid.length; r++) {
                    if (grid[r][vCol]) {
                        const key = grid[r][vCol].r + '-' + grid[r][vCol].c;
                        if (!seen.has(key)) {
                            seen.add(key);
                            colCells.push({ r: grid[r][vCol].r, c: grid[r][vCol].c });
                        }
                    }
                }
                setSelectedCells(colCells);
            };

            // TOOLBAR UI
            const blockControls = selectedCells.length > 0 ? el(BlockControls, {},
                el(ToolbarGroup, {},
                    selectedCells.length === 1 && el(Fragment, {},
                        el(ToolbarButton, { icon: 'table-row-before', title: __('Thêm hàng lên trên', 'mytheme'), onClick: () => addRow(0) }),
                        el(ToolbarButton, { icon: 'table-row-after', title: __('Thêm hàng xuống dưới', 'mytheme'), onClick: () => addRow(1) }),
                        el(ToolbarButton, { icon: 'table-col-before', title: __('Thêm cột bên trái', 'mytheme'), onClick: () => addColumn(0) }),
                        el(ToolbarButton, { icon: 'table-col-after', title: __('Thêm cột bên phải', 'mytheme'), onClick: () => addColumn(1) }),
                        el(ToolbarButton, { icon: 'trash', title: __('Xóa hàng này', 'mytheme'), onClick: () => deleteRow() }),
                        el(ToolbarButton, { icon: 'trash', title: __('Xóa cột này', 'mytheme'), onClick: () => deleteColumn() })
                    ),
                    selectedCells.length === 1 && el(ToolbarButton, { icon: 'stretch-wide', title: __('Chọn cả hàng', 'mytheme'), onClick: selectRow }),
                    selectedCells.length === 1 && el(ToolbarButton, { icon: 'menu-alt', title: __('Chọn cả cột', 'mytheme'), onClick: selectCol }),
                    el(ToolbarButton, { icon: 'grid-view', title: __('Chọn tất cả', 'mytheme'), onClick: selectAll })
                )
            ) : null;

            // INSPECTOR UI
            let activeStyle = {};
            let isMerged = false;
            let activeTag = 'td';
            if (selectedCells.length > 0) {
                // Lấy style của ô đầu tiên trong danh sách đang chọn làm mẫu hiển thị
                const firstCell = attributes.rows[selectedCells[0].r].cells[selectedCells[0].c];
                activeStyle = firstCell.style || {};
                activeTag = firstCell.tag || 'td';
                if ((firstCell.rowspan || 1) > 1 || (firstCell.colspan || 1) > 1) isMerged = true;
            }

            const inspectorControls = selectedCells.length > 0 ? el(InspectorControls, {},
                selectedCells.length === 1 && el(PanelBody, { title: __('Công cụ chọn nhanh', 'mytheme'), initialOpen: true },
                    el(ButtonGroup, { style: { display: 'flex', flexWrap: 'wrap', gap: '5px' } },
                        el(Button, { variant: 'secondary', onClick: selectRow, style: {flex: 1} }, __('Chọn Hàng', 'mytheme')),
                        el(Button, { variant: 'secondary', onClick: selectCol, style: {flex: 1} }, __('Chọn Cột', 'mytheme')),
                        el(Button, { variant: 'primary', onClick: selectAll, style: {width: '100%'} }, __('Chọn Tất Cả (Toàn Bảng)', 'mytheme'))
                    )
                ),
                selectedCells.length === 1 && el(PanelBody, { title: __('Thao tác Bảng (Merge / Split)', 'mytheme'), initialOpen: true },
                    el(ButtonGroup, { style: { display: 'flex', flexWrap: 'wrap', gap: '5px' } },
                        el(Button, { variant: 'secondary', onClick: mergeRight }, __('Gộp sang Phải', 'mytheme')),
                        el(Button, { variant: 'secondary', onClick: mergeDown }, __('Gộp xuống Dưới', 'mytheme')),
                        isMerged && el(Button, { variant: 'primary', isDestructive: true, onClick: splitCell, style: {width: '100%'} }, __('Tách Ô (Split)', 'mytheme'))
                    )
                ),
                selectedCells.length > 1 && el(PanelBody, { title: __('Gộp Nhiều Ô (Multi-Merge)', 'mytheme'), initialOpen: true },
                    el(Button, { variant: 'primary', onClick: mergeSelected, style: {width: '100%'} }, __('Gộp Tất Cả Các Ô Đã Chọn', 'mytheme'))
                ),
                el(PanelBody, { title: __('Thiết lập Style chung (' + selectedCells.length + ' ô đang chọn)', 'mytheme'), initialOpen: true },
                    el(BaseControl, { label: __('Màu Chữ (Text Color)', 'mytheme') },
                        el(ColorPalette, {
                            value: activeStyle.color,
                            onChange: (v) => updateCellStyle({ color: v })
                        })
                    ),
                    el(BaseControl, { label: __('Màu Nền (Background Color)', 'mytheme') },
                        el(ColorPalette, {
                            value: activeStyle.backgroundColor,
                            onChange: (v) => updateCellStyle({ backgroundColor: v })
                        })
                    ),
                    el(SelectControl, {
                        label: __('Loại Ô (Thẻ HTML)', 'mytheme'),
                        value: activeTag,
                        options: [
                            { label: __('Dữ liệu (TD)', 'mytheme'), value: 'td' },
                            { label: __('Tiêu đề (TH)', 'mytheme'), value: 'th' }
                        ],
                        onChange: (v) => updateCellTag(v)
                    }),
                    el(SelectControl, {
                        label: __('Căn lề ngang (Text Align)', 'mytheme'),
                        value: activeStyle.textAlign || '',
                        options: [
                            { label: __('Mặc định', 'mytheme'), value: '' },
                            { label: __('Trái (Left)', 'mytheme'), value: 'left' },
                            { label: __('Giữa (Center)', 'mytheme'), value: 'center' },
                            { label: __('Phải (Right)', 'mytheme'), value: 'right' },
                            { label: __('Đều 2 bên (Justify)', 'mytheme'), value: 'justify' }
                        ],
                        onChange: (v) => updateCellStyle({ textAlign: v })
                    }),
                    el(SelectControl, {
                        label: __('Căn lề dọc (Vertical Align)', 'mytheme'),
                        value: activeStyle.verticalAlign || '',
                        options: [
                            { label: __('Mặc định', 'mytheme'), value: '' },
                            { label: __('Trên (Top)', 'mytheme'), value: 'top' },
                            { label: __('Giữa (Middle)', 'mytheme'), value: 'middle' },
                            { label: __('Dưới (Bottom)', 'mytheme'), value: 'bottom' }
                        ],
                        onChange: (v) => updateCellStyle({ verticalAlign: v })
                    }),
                    el(SelectControl, {
                        label: __('Font Family', 'mytheme'),
                        value: activeStyle.fontFamily || '',
                        options: FONT_FAMILIES,
                        onChange: (v) => updateCellStyle({ fontFamily: v })
                    }),
                    el(SelectControl, {
                        label: __('Font Weight', 'mytheme'),
                        value: activeStyle.fontWeight || '',
                        options: FONT_WEIGHTS,
                        onChange: (v) => updateCellStyle({ fontWeight: v })
                    }),
                    el(RangeControl, {
                        label: __('Font Size (px)', 'mytheme'),
                        min: 10, max: 100,
                        value: activeStyle.fontSize ? parseInt(activeStyle.fontSize) : 0,
                        onChange: (v) => updateCellStyle({ fontSize: v ? v + 'px' : undefined })
                    }),
                    el(TextControl, {
                        label: __('Line Height (vd: 1.5)', 'mytheme'),
                        value: activeStyle.lineHeight || '',
                        onChange: (v) => updateCellStyle({ lineHeight: v })
                    }),
                    el(RangeControl, {
                        label: __('Độ dày Viền (Border Width px)', 'mytheme'),
                        min: 0, max: 20,
                        value: activeStyle.borderWidth ? parseInt(activeStyle.borderWidth) : 0,
                        onChange: (v) => updateCellStyle({ borderWidth: v ? v + 'px' : undefined })
                    }),
                    el(SelectControl, {
                        label: __('Kiểu Viền (Border Style)', 'mytheme'),
                        value: activeStyle.borderStyle || 'solid',
                        options: [
                            { label: 'Solid (Nét liền)', value: 'solid' },
                            { label: 'Dashed (Nét đứt)', value: 'dashed' },
                            { label: 'Dotted (Chấm bi)', value: 'dotted' },
                            { label: 'None (Không viền)', value: 'none' }
                        ],
                        onChange: (v) => updateCellStyle({ borderStyle: v })
                    }),
                    el(BaseControl, { label: __('Màu Viền (Border Color)', 'mytheme') },
                        el(ColorPalette, {
                            value: activeStyle.borderColor,
                            onChange: (v) => updateCellStyle({ borderColor: v })
                        })
                    )
                )
            ) : null;

            // RENDER TABLE
            const tableUI = el('table', { className: 'wp-block-original-table wp-block-table', style: { borderCollapse: 'collapse', width: '100%', border: '1px solid #ccc' } },
                el('tbody', {},
                    attributes.rows.map((row, r) => 
                        el('tr', { key: r },
                            row.cells.map((cell, c) => {
                                const isSelected = selectedCells.some(sel => sel.r === r && sel.c === c);
                                const hasCustomBorder = cell.style && (cell.style.borderWidth || cell.style.borderColor);
                                const cellStyle = { 
                                    ...(cell.style || {}),
                                    border: isSelected ? '2px solid var(--wp-admin-theme-color, #007cba)' : (hasCustomBorder ? `${cell.style.borderWidth || '1px'} ${cell.style.borderStyle || 'solid'} ${cell.style.borderColor || '#ccc'}` : '1px solid #ccc'),
                                    padding: '10px',
                                    minWidth: '50px'
                                };
                                return el(cell.tag || 'td', {
                                    key: c,
                                    rowSpan: cell.rowspan > 1 ? cell.rowspan : undefined,
                                    colSpan: cell.colspan > 1 ? cell.colspan : undefined,
                                    style: cellStyle,
                                    onClick: (e) => {
                                        if (e.ctrlKey || e.metaKey || e.shiftKey) {
                                            if (isSelected) {
                                                setSelectedCells(selectedCells.filter(sel => !(sel.r === r && sel.c === c)));
                                            } else {
                                                setSelectedCells([...selectedCells, { r, c }]);
                                            }
                                        } else {
                                            setSelectedCells([{ r, c }]);
                                        }
                                    }
                                },
                                    el(RichText, {
                                        tagName: 'div',
                                        value: cell.content,
                                        onChange: (v) => updateCellContent(r, c, v),
                                        placeholder: __('Nhập nội dung...', 'mytheme')
                                    })
                                )
                            })
                        )
                    )
                )
            );

            return el(Fragment, {}, blockControls, inspectorControls, tableUI);
        },
        save: function(props) {
            const { attributes } = props;
            return el('table', { className: 'wp-block-original-table wp-block-table' },
                el('tbody', {},
                    attributes.rows.map((row, r) => 
                        el('tr', { key: r },
                            row.cells.map((cell, c) => 
                                el(cell.tag || 'td', {
                                    key: c,
                                    rowSpan: cell.rowspan > 1 ? cell.rowspan : undefined,
                                    colSpan: cell.colspan > 1 ? cell.colspan : undefined,
                                    style: {
                                        ...(cell.style || {}),
                                        border: (cell.style && (cell.style.borderWidth || cell.style.borderColor)) ? `${cell.style.borderWidth || '1px'} ${cell.style.borderStyle || 'solid'} ${cell.style.borderColor || '#ccc'}` : undefined
                                    }
                                },
                                    el(wp.blockEditor.RichText.Content, {
                                        value: cell.content
                                    })
                                )
                            )
                        )
                    )
                )
            );
        }
    });

})(window.wp);