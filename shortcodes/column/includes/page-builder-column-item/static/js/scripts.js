(function(fwe, _, itemData) {
	fwe.on('fw-builder:' + 'page-builder' + ':register-items', function(builder) {
		var PageBuilderColumnItem,
			PageBuilderColumnItemView,
			PageBuilderColumnItemViewWidthChanger;

		PageBuilderColumnItemViewWidthChanger = FwBuilderComponents.ItemView.WidthChanger.extend({
			widths: itemData.item_widths
		});

		PageBuilderColumnItemView = builder.classes.ItemView.extend({
			initialize: function(options) {
				this.defaultInitialize();

				this.templateData = options.templateData;
				this.widthChangerView = new PageBuilderColumnItemViewWidthChanger({
					model: this.model,
					view: this,
					modelAttribute: 'width'
				});

				if (options.modalOptions) {
					this.modal = new fw.OptionsModal({
						title: itemData.l10n.title,
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
								'<i class="dashicons dashicons-admin-generic edit-options" data-hover-tip="<%- edit %>"></i>' +
								'<% } %>' +

								'<i class="dashicons dashicons-admin-page column-item-clone" data-hover-tip="<%- duplicate %>"></i>' +
								'<i class="dashicons dashicons-no column-item-delete" data-hover-tip="<%- remove %>"></i>' +
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

				/**
				 * Other scripts can append/prepend other control $elements
				 */
				fwEvents.trigger('fw:page-builder:shortcode:column:controls', {
					$controls: this.$('.controls:first'),
					model: this.model,
					builder: builder
				});
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
				if (
					!this.get('width')
					&&
					(typeof opts != 'undefined' && typeof opts.$thumb != 'undefined')
				) {
					this.set('width', opts.$thumb.find('.item-data').attr('data-width'));
				}

				this.view = new PageBuilderColumnItemView({
					id: 'page-builder-item-'+ this.cid,
					model: this,
					modalOptions: itemData.options,
					modalSize: itemData.popup_size,
					templateData: {
						hasOptions: !!itemData.options,
                        edit : itemData.l10n.edit,
                        duplicate : itemData.l10n.duplicate,
                        remove : itemData.l10n.remove
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
