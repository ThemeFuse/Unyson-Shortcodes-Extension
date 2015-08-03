(function (fwe, _, itemData) {
	fwe.on('fw-builder:' + 'page-builder' + ':register-items', function (builder) {
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
						size: options.modalSize
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
							'<div class="column-title"><%= title %></div>' +
						'</div>' +
						'<div class="panel-right fw-col-xs-6">' +
							'<div class="controls">' +

								'<% if (hasOptions) { %>' +
								'<i class="dashicons dashicons-admin-generic edit-options" data-hover-tip="<%- edit %>"></i>' +
								'<%  } %>' +

								'<i class="dashicons dashicons-admin-page custom-section-clone" data-hover-tip="<%- duplicate %>"></i>' +
								'<i class="dashicons dashicons-no custom-section-delete" data-hover-tip="<%- remove %>"></i>' +
							'</div>' +
						'</div>' +
					'</div>' +
					'<div class="builder-items"></div>' +
				'</div>'
			),
			render: function () {
				{
					var title = this.templateData.title,
						titleTemplate = itemData.title_template;

					if (titleTemplate && this.model.get('atts')) {
						try {
							title = _.template(
								jQuery.trim(titleTemplate),
								{
									o: this.model.get('atts'),
									title: title
								},
								{
									evaluate: /\{\{(.+?)\}\}/g,
									interpolate: /\{\{=(.+?)\}\}/g,
									escape: /\{\{-(.+?)\}\}/g
								}
							);
						} catch (e) {
							console.error('$cfg["page_builder"]["title_template"]', e.message);

							title = _.template('<%- title %>', {title: title});
						}
					} else {
						title = _.template('<%- title %>', {title: title});
					}
				}

				this.defaultRender(
					jQuery.extend({}, this.templateData, {title: title})
				);

				/**
				 * Other scripts can append/prepend other control $elements
				 */
				fwEvents.trigger('fw:page-builder:shortcode:section:controls', {
					$controls: this.$('.controls:first'),
					model: this.model,
					builder: builder
				});
			},
			events: {
				'click': 'editOptions',
				'click .edit-options': 'editOptions',
				'click .custom-section-clone': 'cloneItem',
				'click .custom-section-delete': 'removeItem'
			},
			editOptions: function (e) {
				e.stopPropagation();

				if (!this.modal) {
					return;
				}
				this.modal.open();
			},
			cloneItem: function (e) {
				e.stopPropagation();

				var index = this.model.collection.indexOf(this.model),
					attributes = this.model.toJSON(),
					_items = attributes['_items'],
					clonedColumn;

				delete attributes['_items'];

				clonedColumn = new PageBuilderSectionItem(attributes);
				this.model.collection.add(clonedColumn, {at: index + 1});
				clonedColumn.get('_items').reset(_items);
			},
			removeItem: function (e) {
				e.stopPropagation();

				this.remove();
				this.model.collection.remove(this.model);
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
					modalSize: itemData.popup_size,
					templateData: {
						hasOptions: !!itemData.options,
                        edit : itemData.l10n.edit,
                        duplicate : itemData.l10n.duplicate,
                        remove : itemData.l10n.remove,
						title: itemData.title
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

