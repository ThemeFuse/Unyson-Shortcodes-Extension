( function ($) {
	$(document).ready(function () {

		function FwTableBuilder($tableBuilder) {
			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());

					if ($(this).hasClass('fw-table-cell-worksheet')) {
						$(this).addClass('fw-fix-top-border');
					}
				});
				return ui;
			};

			var _self = this,
				$table = $tableBuilder.find('.fw-table'),
				lastRow = parseInt($tableBuilder.find('.fw-table-last-row').val()),
				lastCol = parseInt($tableBuilder.find('.fw-table-last-col').val()),
				//todo: smth with
				worksheetNonInteractiveCellsSelector = '.fw-table-row:not(.button-row, .pricing-row, .switch-row, .fw-table-col-options, .fw-template-row, .fw-table-cols-delete) .fw-table-cell-worksheet',
				worksheetRowsSelector = '.fw-table-row:not(.fw-table-col-options, .fw-template-row, .fw-table-cols-delete)',
				$currentCell = false,
				isAllowedTabMove = true,
				colClassNames = $table.find('.fw-table-row:eq(0) .fw-table-cell:eq(1) .fw-table-builder-col-style').find('option').map(function () {
					var text = $(this).text(),
						obj = {};
						obj.value = this.value;
						obj.text = text;
				return obj;
				}).get(),
				rowClassNames = $table.find('.fw-table-row:eq(1) .fw-table-cell:eq(0) .fw-table-builder-row-style').find('option').map(function () {
					var text = $(this).text(),
						obj = {};
					obj.value = this.value;
					obj.text = text;
					return obj;
				}).get(),
				htmlWorksheetCell;

			var process = {
				updateTable: function() {
					var allowedRows = process.getAllowedRows(),
						allowedCols = process.getAllowedCols();

					var rowList = [], colList = [];
					for (var i in rowClassNames) {
						if (allowedRows.indexOf(rowClassNames[i].value) != -1 ){
							rowList.push(rowClassNames[i]);
						}
					}

					for (var i in colClassNames) {
						if (allowedCols.indexOf(colClassNames[i].value) != -1 ){
							colList.push(colClassNames[i]);
						}
					}

					var colHtml  = process.generateOptionsHtml(colList),
						rowHtml = process.generateOptionsHtml(rowList);

					if (typeof allowedCols === 'undefined' || typeof allowedRows === 'undefined' ) {
						$table.hide();
						return;
					}else{
						$table.show();
					}

					$table.find('select.fw-table-builder-row-style').each(function(){
						var value = $(this).val();
							$(this).html(rowHtml);
							//todo: regex check
							if ( allowedRows.indexOf(value) != -1 ) {
								$(this).val(value);
							} else {
								$(this).trigger('change');
							}
					});

					$table.find('select.fw-table-builder-col-style').each(function(){
						var value = $(this).val();
							$(this).html(colHtml);
							//todo: regex check
							if (  allowedCols.indexOf(value) != -1  ) {
								$(this).val(value);
							} else {
								$(this).trigger('change');
							}
					});

				},

				generateOptionsHtml: function(items){
					var html = '';
					for ( var i in items ) {
						html += '<option value="' + items[i].value + '">' + items[i].text + '</option>';
					}
					return html;
				},

				getAllowedCols: function(){
					var $viewChooser = $tableBuilder.find('#fw-edit-options-modal-table-header-optionstable_purpose'),
						allowedColumns = $viewChooser.data('allowed-cols');

					return allowedColumns[$viewChooser.val()];
				},

				getAllowedRows: function(){
					var $viewChooser = $tableBuilder.find('#fw-edit-options-modal-table-header-optionstable_purpose'),
						allowedRows = $viewChooser.data('allowed-rows');

					return allowedRows[$viewChooser.val()];
				},

				readTemplateCell: function(){
					_self.htmlWorksheetCell = $table.find('.fw-cell-template').data('worksheet-cell-template');
					_self.htmlHeaderCell = $table.find('.fw-cell-template').data('header-cell-template');
					$table.find('.fw-cell-template').remove();
				},

				initialize: function () {
					process.updateTable();
					process.tableBuilderEvents();
					process.readTemplateCell();
					process.reInitSortable();
				},

				onTabKeyUp: function (e) {
					var keyCode = e.keyCode || e.which;
					if (keyCode == 9) {
						isAllowedTabMove = true;
					}
				},

				onTabKeyDown: function (e) {
					var keyCode = e.keyCode || e.which;
					if (keyCode == 9) {
						if (isAllowedTabMove === true && $currentCell) {
							isAllowedTabMove = false;
							process.onTabPress(e);
						} else if ($currentCell) {
							isAllowedTabMove = false;
							e.stopPropagation();
							e.preventDefault();
						}
					}
				},

				onTabPress: function (e) {
					var $cells = $table.find(worksheetNonInteractiveCellsSelector),
						currentCellIndex = $cells.index($currentCell),
						order = e.shiftKey ? -1 : 1,
						$nextCell = $cells.filter(':eq(' + (currentCellIndex + order) + ')');

					if (!$nextCell.length) {
						$nextCell = order == 1 ? $cells.filter(':eq(0)') : $cells.filter(':last');
					}

					e.stopPropagation();
					e.preventDefault();

					process.cellTriggerManager(e, $nextCell)
				},

				cellTriggerManager: function (e, $cell) {
					e.stopPropagation();
					process.openEditor($cell);
					process.setCurrentCell($cell);
				},

				changeTableRowStyle: function () {
					var $select = $(this),
						newClass = $select.val(),
						classNames = rowClassNames.map(function (item) {
							return item.value;
						}).join(" "),
						$selectCell = $select.parent(),
						$row = $selectCell.parent();

					$row.removeClass(classNames).addClass(newClass);
					$selectCell.removeClass(classNames).addClass(newClass);

					$row.find('.fw-table-cell-worksheet').each(function () {
						if (jQuery(this).hasClass('desc-col')) {

							if ( newClass != 'default-row' || newClass != 'heading-row' ) {
								$(this).children('.fw-table-cell-content').removeClass('fw-active-content');
								$(this).children('.fw-table-cell-content.default-row').addClass('fw-active-content');
							}
							return;
						}

						$(this).children('.fw-table-cell-content').removeClass('fw-active-content');
						$(this).children('.fw-table-cell-content.' + newClass).addClass('fw-active-content');
					});

					process.trigger('row:styling:changed', {$elements: $row});

					return false;
				},

				/**
				 * Generate string of class names (from select values), which need tobe removed, after add new class for specific cells
				 */
				changeTableColumnStyle: function () {
					var $select = $(this),
						newClass = $select.val(),
						classNames = colClassNames.map(function (item) {
							return item.value;
						}).join(" "),
						$cell = $select.parent(),
						colId = parseInt($cell.data('col')),
						$elements = $table.find('[data-col=' + colId + ']');

					$elements.removeClass(classNames).addClass(newClass);
					process.trigger('column:styling:changed', { $elements: $elements });
					return false;
				},

				removeTableColumn: function () {
					var columns = $table.find('.fw-template-row .fw-table-cell');

					if (columns.length > 3) {
						var colId = parseInt($(this).closest('.fw-table-cell').data('col'));
						$table.find('.fw-table-cell[data-col=' + colId + ']').remove();
						process.trigger('column:removed');
					}

					return false;
				},

				removeTableRow: function () {
					var $row = $(this).closest('.fw-table-row');

					if (false === $(this).hasClass('empty-cell') && false === $row.hasClass('fw-template-row') && $table.find('.fw-table-row').length > 4) {
						$row.remove();
						process.trigger('row:removed');
					}

					return false;
				},

				addTableColumn: function () {
					var columns = $table.find('.fw-template-row .fw-table-cell');

					lastCol++;

					//max cols
					if (columns.length <= 6) {
						/**
						 * Clone worksheet (data cells) and insert it before last row's cell
						 */
						var $dropdownColCell = $table.find('.fw-table-row:eq(0) .fw-table-cell:eq(1)'),
							dropDownDefaultColValue = $dropdownColCell.find('select option:eq(0)').val(),
							$worksheetCellTemplate = $table.find('.fw-template-row .fw-table-cell:eq(1)'),
							$beforeDeleteRowCell = $table.find('.fw-table-row:not(.fw-table-row:eq(0), .fw-table-cols-delete) .fw-table-row-delete'),
							$insertedWorksheetCell = $worksheetCellTemplate.clone().addClass(dropDownDefaultColValue).insertBefore($beforeDeleteRowCell);

						$insertedWorksheetCell.attr('data-col', lastCol);

						$insertedWorksheetCell.each(function(){
							var $currentRow = $(this).parent(),
								rowId = $currentRow.data('row');
							if ( false === $(this).parent().hasClass('fw-template-row')) {
								$(this).html( _self.htmlWorksheetCell.split( '_template_key_row_' ).join( rowId ).split( '_template_key_col_' ).join( lastCol ) );
								process.changeTableRowStyle.apply( $currentRow.find('.fw-table-cell-options select') );
							}
						});

						/**
						 * Clone first cell with select and insert it before last row's cell
						 */
						var $lastEmptyCellFirstRow = $table.find('.fw-table-row:eq(0) .fw-table-row-delete'),
							clone2 = $dropdownColCell.clone().insertBefore($lastEmptyCellFirstRow);
							clone2.attr('data-col', lastCol);
							clone2.html( _self.htmlHeaderCell.split( '_template_key_col_' ).join( lastCol ) );

						/**
						 * Clone last row (row which consists with remove cols buttons) and insert it before last row's cell
						 */
						var deleteCellTemplate = $table.find('.fw-table-cols-delete .fw-table-cell:eq(1)'),
							$lastEmptyCellLastRow = $table.find('.fw-table-cols-delete .fw-table-cell:last'),
							clone3 = deleteCellTemplate.clone().insertBefore($lastEmptyCellLastRow);
						clone3.attr('data-col', lastCol);

						/**
						 * set column default style
						 */

						var allowedCols = process.getAllowedCols(),
							colList = [];
						for (var i in colClassNames) {
							if (allowedCols.indexOf(colClassNames[i].value) != -1 ){
								colList.push(colClassNames[i]);
							}
						}

						clone2.find('select.fw-table-builder-col-style').html(process.generateOptionsHtml(colList));
						process.changeTableColumnStyle.apply(clone2.find('select.fw-table-builder-col-style'));

						process.reinitOptions(clone2);
						process.reinitOptions($insertedWorksheetCell);
						process.trigger('column:added', {$elements: $insertedWorksheetCell});
					}

					return false;
				},

				addTableRow: function () {
					var $templateRow = $tableBuilder.find('.fw-template-row');
					lastRow++;

					var $insertedRow = $templateRow.clone().removeClass('fw-template-row').attr('data-row', lastRow).insertBefore($templateRow);

					$insertedRow.find('.fw-table-cell-worksheet').each(function(){
						var col = $(this).data('col');
						$(this).html( _self.htmlWorksheetCell.split( '_template_key_row_' ).join( lastRow ).split( '_template_key_col_' ).join( col ) );
					});

					$insertedRow.find('.fw-table-cell-options :input').each(function(){
						$(this).attr('name', $(this).attr('name').split( '_template_key_row_' ).join( lastRow ) );
						$(this).attr('id', $(this).attr('id').split( '_template_key_row_' ).join( lastRow ) );
					});

					process.reinitOptions($insertedRow);
					process.trigger('row:added', {$elements: $insertedRow});
					return false;
				},

				reinitOptions: function ($container) {
					$container.find('.fw-table-cell-content').on('click', function(e){
						process.cellTriggerManager(e, $(this).parents('.fw-table-cell'));
					});

					fwEvents.trigger('fw:options:init',
						{$elements: $container}
					);
					process.reInitSortable();
				},

				reInitSortable: function() {

					try {
						$table.sortable('destroy');
					} catch (e) {
						// happens when sortable was not initialized before
					}

					var isMobile = $(document.body).hasClass('mobile');

					$table.sortable({
						items: worksheetRowsSelector,
						handle: '.fw-table-cell',
						cursor: 'auto',
						placeholder: 'fw-table-row sortable-placeholder',
						delay: ( isMobile ? 200 : 0 ),
						distance: 2,
						tolerance: 'pointer',
						forcePlaceholderSize: true,
						axis: 'y',
						helper: fixHelper,
						stop: function(e, ui) {
							ui.item.find('.fw-table-cell-worksheet').removeClass('fw-fix-top-border');
						},
						start: function(e, ui){
							// Update the height of the placeholder to match the moving item.
							{
								var height = ui.item.outerHeight();

								height -= 2; // Subtract 2 for borders

								ui.placeholder.height(height);

							}
						}
					});

				},

				openEditor: function ($cell) {
					process.closeEditor();
					$cell.find('.fw-active-content .fw-cell-option-wrapper > *').trigger('activate');
					process.setCurrentCell($cell);
				},

				closeEditor: function () {
					if ($currentCell) {
						$currentCell.find('.fw-active-content .fw-cell-option-wrapper > *').trigger('deactivate');
					}
					process.setCurrentCell(false);
				},

				/**
				 * @param $cell type jQuery | boolean
				 */
				setCurrentCell: function ($cell) {
					$currentCell = $cell;
					process.trigger('current-cell:changed', {$element: $cell})
				},

				trigger: function (event, args) {
					$tableBuilder.trigger('fw:option-type:table-builder:' + event, args);
					return args;
				},

				tableBuilderEvents: function () {
					$table.find('.fw-table-cell-content').on('click', function(e){
						process.cellTriggerManager(e, $(this).parents('.fw-table-cell'));
					});

					$table.on('click', '.fw-table-cell-worksheet', function(e){
						process.cellTriggerManager(e, $(this));
					});

					$tableBuilder.find('#fw-edit-options-modal-table-header-optionstable_purpose').on('change', process.updateTable);

					$table.on('click', '.fw-table-col-delete-btn', process.removeTableColumn );
					$table.on('click', '.fw-table-row-delete-btn', process.removeTableRow);
					/*$table.on('change', 'select.fw-table-builder-col-style', process.changeTableColumnStyle);
					$table.on('change', 'select.fw-table-builder-row-style', process.changeTableRowStyle);*/

					$table.on('change', function (e) {
						jQuery(this).find('select.fw-table-builder-col-style').each(function () {
							process.changeTableColumnStyle.apply(this);
						});

						jQuery(this).find('select.fw-table-builder-row-style').each(function () {
							process.changeTableRowStyle.apply(this);
						});
					});

					$tableBuilder.on('fw:option-type:table-builder:column:added', function () {
						$table.find('select.fw-table-builder-row-style').each(function () {
							process.changeTableRowStyle.apply(this);
						});
					});

					$table.on('keydown', process.onTabKeyDown);
					$table.on('keyup', process.onTabKeyUp);
					$table.on('click', '.fw-table-add-column', process.addTableColumn);
					$table.on('click', '.fw-table-add-row', process.addTableRow);
					$tableBuilder.on('fw:option-type:table-builder:column:added fw:option-type:table-builder:column:removed fw:option-type:table-builder:row:added fw:option-type:table-builder:row:removed fw:option-type:table-builder:row:styling:changed fw:option-type:table-builder:column:styling:changed', process.closeEditor);

					fwEvents.on('fw:option-type:popup:change', process.changePopupButtonTitle)
				},

				changePopupButtonTitle: function (data) {
					var $popup = data.element;
					if ($popup.parents('.fw-table-cell-worksheet').length) {
						data.element.find('.content.button').text(localizeTableBuilder.msgEdit);
					}
				}
			};

			process.initialize();
		}

		fwEvents.on('fw:options:init', function (data) {
			data.$elements.find('.fw-option-type-table:not(.fw-option-initialized)').each(function () {
				new FwTableBuilder($(this));
			}).addClass('fw-option-initialized');
		});
	});
}(jQuery));
