(function(fwe, _, itemData) {
	fwe.one('fw-builder:' + 'page-builder' + ':register-items', function(builder) {
		var PageBuilderColumnItem,
			PageBuilderColumnItemView;

		PageBuilderColumnItemView = builder.classes.ItemView.extend({
			initialize: function(options) {
				this.defaultInitialize();

				this.templateData = options.templateData;
				this.widthChangerView = new FwBuilderComponents.ItemView.WidthChanger({
					model: this.model,
					view: this,
					modelAttribute: 'width'
				});

				if (options.modalOptions) {
					this.modal = new fw.OptionsModal({
						title: 'Column', // TODO: make translatable
						options: options.modalOptions,
						values: this.model.get('atts'),
						size: options.modalSize
					});

					this.listenTo(this.modal, 'change:values', function (modal, values) {
						this.model.set('atts', values);
					});
				}
			},
			template: _.template(
				'<div class="pb-item-type-column pb-item <% if (hasOptions) { print(' + '"has-options"' + ')} %>">' +
					'<div class="panel fw-row">' +
						'<div class="panel-left fw-col-xs-6">' +
							'<div class="width-changer"></div>' +
						'</div>' +
						'<div class="panel-right fw-col-xs-6">' +
							'<div class="controls">' +

								'<% if (hasOptions) { %>' +
								'<i class="dashicons dashicons-edit edit-options"></i>' +
								'<%  } %>' +

								'<i class="dashicons dashicons-admin-page column-item-clone"></i>' +
								'<i class="dashicons dashicons-no column-item-delete"></i>' +
							'</div>' +
						'</div>' +
					'</div>' +
					'<div class="builder-items"></div>' +
				'</div>'
			),
			render: function() {
				this.defaultRender(this.templateData);

				this.$('.width-changer').append(this.widthChangerView.$el);
				this.widthChangerView.delegateEvents();
			},
			events: {
				'click': 'editOptions',
				'click .edit-options': 'editOptions',
				'click .column-item-clone': 'cloneItem',
				'click .column-item-delete': 'removeItem'
			},
			editOptions: function (e) {
				e.stopPropagation();

				if (!this.modal) {
					return;
				}
				this.modal.open();
			},
			cloneItem: function(e) {
				e.stopPropagation();

				var index = this.model.collection.indexOf(this.model),
					attributes = this.model.toJSON(),
					_items = attributes['_items'],
					clonedColumn;

				delete attributes['_items'];

				clonedColumn = new PageBuilderColumnItem(attributes);
				this.model.collection.add(clonedColumn, {at: index + 1});
				clonedColumn.get('_items').reset(_items);
			},
			removeItem: function(e) {
				e.stopPropagation();

				this.remove();
				this.model.collection.remove(this.model);
			}
		});

		PageBuilderColumnItem = builder.classes.Item.extend({
			defaults: {
				type: 'column'
			},
			restrictedTypes: itemData.restrictedTypes,
			initialize: function(atts, opts) {
				var width = this.get('width') || opts.$thumb.find('.item-data').attr('data-width');

				if (!this.get('width')) {
					this.set('width', width);
				}

				this.view = new PageBuilderColumnItemView({
					id: 'page-builder-item-'+ this.cid,
					model: this,
					modalOptions: itemData.options,
					modalSize: itemData.popup_size,
					templateData: {
						hasOptions: !!itemData.options
					}
				});

				this.defaultInitialize();
			},
			allowIncomingType: function(type) {
				return _.indexOf(this.restrictedTypes, type) === -1;
			}
		});

		builder.registerItemClass(PageBuilderColumnItem);
	});
})(fwEvents, _, page_builder_item_type_column_data);
