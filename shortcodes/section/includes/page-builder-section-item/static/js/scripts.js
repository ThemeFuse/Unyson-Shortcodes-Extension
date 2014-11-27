(function (fwe, _, itemData) {
	fwe.one('fw-builder:' + 'page-builder' + ':register-items', function (builder) {
		var PageBuilderSectionItem,
			PageBuilderSectionItemView;

		PageBuilderSectionItemView = builder.classes.ItemView.extend({
			initialize: function (options) {
				this.defaultInitialize();

				this.templateData = options.templateData;
				if (options.modalOptions) {
					this.modal = new fw.OptionsModal({
						title: 'Section',
						options: options.modalOptions,
						values: this.model.get('atts'),
						size: 'medium'
					});

					this.listenTo(this.modal, 'change:values', function (modal, values) {
						this.model.set('atts', values);
					});
				}
			},
			template: _.template(
				'<div class="pb-item-type-column pb-item custom-section">' +
					'<div class="panel fw-row">' +
						'<div class="panel-left fw-col-xs-6">' +
							'<div class="column-title">Section</div>' +
						'</div>' +
						'<div class="panel-right fw-col-xs-6">' +
							'<div class="controls">' +

								'<% if (hasOptions) { %>' +
								'<i class="dashicons dashicons-welcome-write-blog edit-options"></i>' +
								'<%  } %>' +

								'<i class="dashicons dashicons-admin-page custom-section-clone"></i>' +
								'<i class="dashicons dashicons-no custom-section-delete"></i>' +
							'</div>' +
						'</div>' +
					'</div>' +
					'<div class="builder-items"></div>' +
				'</div>'
			),
			render: function () {
				this.defaultRender(this.templateData);
			},
			events: {
				'click .edit-options': 'editOptions',
				'click .custom-section-clone': 'cloneItem',
				'click .custom-section-delete': 'removeItem'
			},
			cloneItem: function () {
				var index = this.model.collection.indexOf(this.model),
					attributes = this.model.toJSON(),
					_items = attributes['_items'],
					clonedColumn;

				delete attributes['_items'];

				clonedColumn = new PageBuilderSectionItem(attributes);
				this.model.collection.add(clonedColumn, {at: index + 1});
				clonedColumn.get('_items').reset(_items);
			},
			removeItem: function () {
				this.remove();
				this.model.collection.remove(this.model);
			},
			editOptions: function (e) {
				e.stopPropagation();
				if (!this.modal) {
					return;
				}
				this.modal.open();
			}
		});

		PageBuilderSectionItem = builder.classes.Item.extend({
			defaults: {
				type: 'section'
			},
			initialize: function() {
				this.view = new PageBuilderSectionItemView({
					id: 'page-builder-item-' + this.cid,
					model: this,
					modalOptions: itemData.options,
					templateData: {
						hasOptions: !!itemData.options
					}
				});

				this.defaultInitialize();
			},
			allowIncomingType: function (type) {
				return 'section' !== type;
			},
			allowDestinationType: function (type) {
				return 'column' !== type;
			}
		});

		builder.registerItemClass(PageBuilderSectionItem);
	});
})(fwEvents, _, page_builder_item_type_section_data);

