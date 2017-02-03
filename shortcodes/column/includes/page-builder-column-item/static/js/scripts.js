(function(fwe) {
	fwe.on('fw-builder:' + 'page-builder' + ':register-items', function(builder) {
		var PageBuilderColumnItem,
			PageBuilderColumnItemView,
			PageBuilderColumnItemViewWidthChanger,
			triggerEvent = function(itemModel, event, eventData) {
				event = 'fw:builder-type:{builder-type}:item-type:{item-type}:'
					.replace('{builder-type}', builder.get('type'))
					.replace('{item-type}', itemModel.get('type'))
					+ event;

				var data = {
					modal: itemModel.view ? itemModel.view.modal : null,
					item: itemModel,
					itemView: itemModel.view,
					shortcode: itemModel.get('shortcode'),
					builder: builder
				};

				fwEvents.trigger(event, eventData ? _.extend(eventData, data) : data);
			},
			getEventName = function(itemModel, event) {
				return 'fw:builder-type:{builder-type}:item-type:{item-type}:'
					.replace('{builder-type}', builder.get('type'))
					.replace('{item-type}', itemModel.get('type'))
					+ event;
			};

		PageBuilderColumnItemViewWidthChanger = FwBuilderComponents.ItemView.WidthChanger.extend({
			widths: itemData().item_widths
		});

		PageBuilderColumnItemView = builder.classes.ItemView.extend({
			initialize: function(options) {
				this.defaultInitialize();

				this.initOptions = options;
				this.initOptions.templateData = this.initOptions.templateData || {};
				this.initOptions.modalOptions = this.initOptions.modalOptions || {};

				this.widthChangerView = new PageBuilderColumnItemViewWidthChanger({
					model: this.model,
					view: this,
					modelAttribute: 'width'
				});
			},
			template: _.template(
				'<div class="pb-item-type-column pb-item <% if (hasOptions) { print(' + '"has-options"' + ')} %>">' +
				/**/'<div class="panel fw-row">' +
				/**//**/'<div class="panel-left fw-col-xs-6">' +
				/**//**//**/'<div class="width-changer"></div>' +
				/**//**/'</div>' +
				/**//**/'<div class="panel-right fw-col-xs-6">' +
				/**//**//**/'<div class="controls">' +

				/**//**//**//**/'<% if (hasOptions) { %>' +
				/**//**//**//**/'<i class="dashicons dashicons-admin-generic edit-options" data-hover-tip="<%- edit %>"></i>' +
				/**//**//**//**/'<% } %>' +

				/**//**//**//**/'<i class="dashicons dashicons-admin-page column-item-clone" data-hover-tip="<%- duplicate %>"></i>' +
				/**//**//**//**/'<i class="dashicons dashicons-no column-item-delete" data-hover-tip="<%- remove %>"></i>' +
				/**//**//**//**/'<i class="dashicons dashicons-arrow-down column-item-collapse" data-hover-tip="<%- collapse %>"></i>' +
				/**//**//**/'</div>' +
				/**//**/'</div>' +
				/**/'</div>' +
				/**/'<div class="builder-items"></div>' +
				'</div>'
			),
			render: function() {
				this.defaultRender(this.initOptions.templateData);

				this.$('.width-changer').append(this.widthChangerView.$el);
				this.widthChangerView.delegateEvents();

				this.$el[this.model.get('fw-collapse') ? 'addClass' : 'removeClass']('pb-item-column-collapsed');

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
				'click .column-item-delete': 'removeItem',
				'click .column-item-collapse': 'collapseItem'
			},
			lazyInitModal: function () {
				this.lazyInitModal = function (){};

				if (_.isEmpty(this.initOptions.modalOptions)) {
					return;
				}

				var eventData = {modalSettings: {buttons: []}};

				/**
				 * eventData.modalSettings can be changed by reference
				 */
				triggerEvent(this.model, 'options-modal:settings', eventData);

				this.modal = new fw.OptionsModal({
					title: itemData().l10n.title,
					options: this.initOptions.modalOptions,
					values: this.model.get('atts'),
					size: this.initOptions.modalSize,
					headerElements: itemData().header_elements
				}, eventData.modalSettings);

				this.listenTo(this.modal, 'change:values', function (modal, values) {
					this.model.set('atts', values);
				});

				this.listenTo(this.modal, {
					'open': function(){
						fwEvents.trigger(getEventName(this.model, 'options-modal:open'), {
							modal: this.modal,
							item: this.model,
							itemView: this
						});
					},
					'render': function(){
						fwEvents.trigger(getEventName(this.model, 'options-modal:render'), {
							modal: this.modal,
							item: this.model,
							itemView: this
						});
					},
					'close': function(){
						fwEvents.trigger(getEventName(this.model, 'options-modal:close'), {
							modal: this.modal,
							item: this.model,
							itemView: this
						});
					},
					'change:values': function(){
						fwEvents.trigger(getEventName(this.model, 'options-modal:change:values'), {
							modal: this.modal,
							item: this.model,
							itemView: this
						});
					}
				});
			},
			editOptions: function (e) {
				e.stopPropagation();

				this.lazyInitModal();

				if (!this.modal) {
					return;
				}

				var flow = {cancelModalOpening: false};

				/**
				 * Trigger before-open model just like we do this for
				 * item-simple shortcodes.
				 *
				 * http://bit.ly/1KY6tpP
				 */
				fwEvents.trigger('fw:page-builder:shortcode:column:modal:before-open', {
					modal: this.modal,
					model: this.model,
					builder: builder,
					flow: flow
				});

				if (! flow.cancelModalOpening) {
					this.modal.open();
				}
			},
			cloneItem: function(e) {
				e.stopPropagation();

				var index = this.model.collection.indexOf(this.model),
					attributes = this.model.toJSON(),
					_items = attributes['_items'],
					clonedColumn;

				delete attributes['_items'];

				clonedColumn = new PageBuilderColumnItem(attributes);

				triggerEvent(clonedColumn, 'clone-item:before');

				this.model.collection.add(clonedColumn, {at: index + 1});
				clonedColumn.get('_items').reset(_items);
			},
			removeItem: function(e) {
				e.stopPropagation();

				this.remove();
				this.model.collection.remove(this.model);
			},
			collapseItem: function(e) {
				e.stopPropagation();
				this.model.set('fw-collapse', !this.model.get('fw-collapse'));
			}
		});

		PageBuilderColumnItem = builder.classes.Item.extend({
			defaults: {
				type: 'column'
			},
			restrictedTypes: itemData().restrictedTypes,
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
					modalOptions: itemData().options,
					modalSize: itemData().popup_size,
					templateData: {
						hasOptions: !! itemData().options,
						edit : itemData().l10n.edit,
						duplicate : itemData().l10n.duplicate,
						remove : itemData().l10n.remove,
						collapse: itemData().l10n.collapse,
					}
				});

				this.defaultInitialize();
			},
			allowIncomingType: function(type) {
				var data = {
					allow: _.indexOf(this.restrictedTypes, type) === -1,
					type: type,
					model: this
				};

				// in this event you can change data.allow by reference
				fwEvents.trigger('fw:builder:page-builder:column:filter:allow-incomming-type', data);

				return data.allow;
			}
		});

		builder.registerItemClass(PageBuilderColumnItem);
	});

	function itemData () {
		// return fw.unysonShortcodesData()['column'];
		return page_builder_item_type_column_data;
	}
})(fwEvents);
