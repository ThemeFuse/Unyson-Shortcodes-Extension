(function($, localized){
	var eventsNamespace = '.templates-section',
		loadingId = 'fw-builder-templates-type-section',
		modal,
		lazyInitModal = function () {
			lazyInitModal = function (){};

			modal = new fw.OptionsModal({
				title: localized.l10n.save_template,
				options: [
					{
						'template_name': {
							'type': 'text',
							'label': localized.l10n.template_name
						}
					}
				],
				values: ''
			});
		};

	fwEvents.on('fw:option-type:builder:templates:init', function(data){
		var loading = data.tooltipLoading,
			builder = data.builder,
			tooltipHideCallback = data.tooltipHideCallback,
			tooltipRefreshCallback = data.tooltipRefreshCallback;

		data.$elements.find('.fw-builder-templates-type-section')
			.off(eventsNamespace)
			.on('click'+ eventsNamespace, 'a[data-load-template]', function(){
				var templateId = $(this).attr('data-load-template');

				loading.show();

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: ajaxurl,
					data: {
						'action': 'fw_builder_templates_section_load',
						'builder_type': builder.get('type'),
						'template_id': templateId
					}
				})
					.done(function(json){
						loading.hide();

						if (!json.success) {
							console.error('Failed to load builder template', json);
							return;
						}

						if (JSON.stringify(builder.rootItems) === json.data.json) {
							console.log('Loaded value is the same as current');
						} else {
							builder.rootItems.add(JSON.parse(json.data.json));
						}

						tooltipHideCallback();

						// scroll to the bottom of the builder
						setTimeout(function(){
							var $builderOption = builder.$input.closest('.fw-option-type-builder'),
								$scrollParent = $builderOption.scrollParent();

							if ($scrollParent.get(0) === document || $scrollParent.get(0) === document.body) {
								$scrollParent = $(window);
							}

							$scrollParent.scrollTop(
								$builderOption.offset().top
								+
								$builderOption.outerHeight()
								-
								$scrollParent.height()
							);
						}, 100);
					})
					.fail(function(xhr, status, error){
						loading.hide();

						console.error('Ajax error', error);
					});
			})
			.on('click'+ eventsNamespace, 'a[data-delete-template]', function(){
				var templateId = $(this).attr('data-delete-template');

				loading.show();

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: ajaxurl,
					data: {
						'action': 'fw_builder_templates_section_delete',
						'builder_type': builder.get('type'),
						'template_id': templateId
					}
				})
					.done(function(json){
						loading.hide();

						if (!json.success) {
							console.error('Failed to delete builder template', json);
							return;
						}

						tooltipRefreshCallback();
					})
					.fail(function(xhr, status, error){
						loading.hide();

						console.error('Ajax error', error);
					});
			});
	});

	fwEvents.on('fw:page-builder:shortcode:section:controls', function(data){
		data.$controls.prepend(
			$('<i class="fw-shortcode-section-save dashicons dashicons-download"></i>')
				.attr('data-hover-tip', localized.l10n.save_template_tooltip)
				.on('click', function(e){
					e.stopPropagation();
					e.preventDefault();

					lazyInitModal();

					// reset previous values
					modal.set('values', {}, {silent: true});

					// remove previous listener
					modal.off('change:values');

					modal.on('change:values', function (modal, values) {
						fw.loading.show(loadingId);

						$.ajax({
							type: 'post',
							dataType: 'json',
							url: ajaxurl,
							data: {
								'action': 'fw_builder_templates_section_save',
								'template_name': values.template_name,
								'section_json': JSON.stringify(data.model),
								'builder_type': data.builder.get('type')
							}
						})
							.done(function (json) {
								fw.loading.hide(loadingId);

								if (!json.success) {
									console.error('Failed to save builder template', json);
									return;
								}
							})
							.fail(function (xhr, status, error) {
								fw.loading.hide(loadingId);

								console.error('Ajax save error', error);
							});
					});

					modal.open();
				})
		);
	});
})(jQuery, _fw_option_type_builder_templates_section);