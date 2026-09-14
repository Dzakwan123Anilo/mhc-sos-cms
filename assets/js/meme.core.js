(function (window, $) {
	'use strict';

	function safeIcon(value) {
		return String(value || '')
			.split(/\s+/)
			.filter(function (item) { return /^fa-[a-z0-9-]+$/i.test(item); })
			.join(' ');
	}

	function safeLocalUrl(value, fallback) {
		try {
			var url = new URL(String(value || ''), window.location.href);
			if ((url.protocol === 'http:' || url.protocol === 'https:') && url.origin === window.location.origin) {
				return url.href;
			}
		} catch (error) {
			// Fall through to the inert URL below.
		}
		return fallback;
	}

	function renderTitle(parameters) {
		var title = $('#title-mb-meme').empty();
		$('<span>').addClass('fa ' + safeIcon(parameters.icon)).appendTo(title);
		$('<strong>').text(parameters.title || '').appendTo(title);
	}

	function renderText(parameters) {
		var content = $('#content-mb-meme').empty();
		$('<p>').text(parameters.body || '').appendTo(content);
		if (String(parameters.desc || '').trim() !== '') {
			$('<p>').text(parameters.desc).appendTo(content);
		}
	}

	function closeButton(label) {
		return $('<button>', {
			type: 'button',
			'class': 'btn btn-default btn-lg',
			text: label
		}).css('margin-left', '20px').on('click', function () {
			$('#mb-custom-meme').hide();
		});
	}

	function prepareBox(className, topMargin) {
		var box = $('#mb-custom-meme');
		box.find('.mb-container').toggleClass('topm_200', topMargin);
		box.removeClass('message-box-danger message-box-success message-box-info message-box-warning')
			.addClass(className);
		return box;
	}

	window.MEME = {
		information: function (parameters) {
			var box = prepareBox('message-box-info', false);
			renderTitle(parameters);
			renderText(parameters);
			$('#btn-mb-meme').empty().append(closeButton('Tutup'));
			playAudio('alert');
			box.show();
			return false;
		},

		confirm: function (parameters) {
			var box = prepareBox('message-box-warning', false);
			renderTitle(parameters);
			renderText(parameters);
			$('#btn-mb-meme').empty()
				.append($('<a>', {
					href: safeLocalUrl(parameters.url, '#'),
					'class': 'btn btn-success btn-lg',
					text: 'Ya'
				}))
				.append(closeButton('Tidak'));
			playAudio('alert');
			box.show();
			return false;
		},

		modal: function (parameters) {
			var box = prepareBox('message-box-success', true);
			var width = Math.min(1600, Math.max(200, parseInt(parameters.size[0], 10) || 700));
			var height = Math.min(1200, Math.max(200, parseInt(parameters.size[1], 10) || 400));
			var content = $('#content-mb-meme').empty();

			renderTitle(parameters);
			$('<iframe>', {
				src: safeLocalUrl(parameters.url, 'about:blank'),
				width: width,
				height: height,
				frameborder: 0,
				referrerpolicy: 'no-referrer',
				title: parameters.title || 'Preview'
			}).attr('sandbox', '').appendTo(content);
			$('<p>').text(parameters.body || '').appendTo(content);
			$('#btn-mb-meme').empty().append(closeButton('Tutup'));
			playAudio('alert');
			box.show();
			return false;
		}
	};

	$(document).ready(function () {
		$('.act_confirm').on('click', function (event) {
			event.preventDefault();
			window.MEME.confirm({
				url: $(this).attr('href'),
				body: $(this).attr('data-body') || 'Apakah Anda Yakin ?',
				title: $(this).attr('data-title') || 'Confirm',
				desc: $(this).attr('data-desc') || '',
				icon: $(this).attr('data-icon') || 'fa-warning'
			});
		});

		$('.act_info').on('click', function (event) {
			event.preventDefault();
			window.MEME.information({
				body: $(this).attr('data-body') || 'Information',
				title: $(this).attr('data-title') || 'Information',
				desc: $(this).attr('data-desc') || '',
				icon: $(this).attr('data-icon') || 'fa-info'
			});
		});

		$('.act_modal').on('click', function (event) {
			event.preventDefault();
			var size = ($(this).attr('data-size') || $(this).attr('rel') || '700|400').split('|');
			window.MEME.modal({
				body: $(this).attr('data-body') || '',
				url: $(this).attr('href'),
				size: size,
				title: $(this).attr('data-title') || $(this).attr('title') || 'Data',
				icon: $(this).attr('data-icon') || 'fa-camera-retro'
			});
		});
	});
}(window, jQuery));
