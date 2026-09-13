$(document).ready(function(){
	$('.act_confirm').each(function(){
		$(this).click(function(event){
			event.preventDefault();
			MEME.confirm({
				url: $(this).attr('href'),
				body: $(this).attr('data-body') === undefined ? 'Apakah Anda Yakin ?' : $(this).attr('data-body'),
				title: $(this).attr('data-title') === undefined ? 'Confirm' : $(this).attr('data-title'),
				desc: $(this).attr('data-desc') === undefined ? '' : $(this).attr('data-desc'),
				icon: $(this).attr('data-icon') === undefined ? 'fa-warning' : $(this).attr('data-icon')
			});
		});
	});

	$('.act_info').each(function(){
		$(this).click(function(event){
			event.preventDefault();
			MEME.information({
				body: $(this).attr('data-body') === undefined ? 'Information' : $(this).attr('data-body'),
				title: $(this).attr('data-title') === undefined ? 'Information' : $(this).attr('data-title'),
				icon: $(this).attr('data-icon') === undefined ? 'fa-info' : $(this).attr('data-icon'),
				desc: $(this).attr('data-desc') === undefined ? '' : $(this).attr('data-desc')
			});
		});
	});

	$('.act_modal').each(function(){
		$(this).click(function(event){
			event.preventDefault();
			MEME.modal({
				body: $(this).attr('data-body') === undefined ? '' : $(this).attr('data-body'),
				url: $(this).attr('href'),
				size: ($(this).attr('data-size') === undefined ? ($(document).height() - 50) + '|400' : $(this).attr('data-size')).split('|'),
				title: $(this).attr('data-title') === undefined ? 'Data' : $(this).attr('data-title'),
				icon: $(this).attr('data-icon') === undefined ? 'fa-camera-retro' : $(this).attr('data-icon')
			});
		});
	});
});

MEME = {
	textValue: function(value){
		return value === null || value === undefined ? '' : String(value);
	},
	internalUrl: function(value){
		var target = typeof value === 'string' ? $.trim(value) : '';
		if(target === '' || /^\/\//.test(target) || /[\u0000-\u001f\u007f\\\\]/.test(target)){
			return '';
		}
		var link = document.createElement('a');
		link.href = target;
		if(link.protocol !== window.location.protocol || link.host !== window.location.host || link.username || link.password){
			return '';
		}
		return link.href;
	},
	setTitle: function(params){
		var icon = /^fa-[a-z0-9-]+$/i.test(MEME.textValue(params.icon)) ? params.icon : 'fa-info';
		$('#title-mb-meme').empty()
			.append($('<span>').addClass('fa').addClass(icon))
			.append($('<strong>').text(MEME.textValue(params.title)));
	},
	setContent: function(params){
		var content = $('#content-mb-meme').empty();
		content.append($('<p>').text(MEME.textValue(params.body)));
		if($.trim(MEME.textValue(params.desc)) !== ''){
			content.append($('<p>').text(MEME.textValue(params.desc)));
		}
	},
	closeButton: function(label){
		return $('<button>', {
			type: 'button',
			'class': 'btn btn-default btn-lg'
		}).css('margin-left', '20px').text(label).on('click', function(){
			$('#mb-custom-meme').hide();
		});
	},
	appendTextRow: function(table, values){
		var row = $('<tr>');
		$.each(values, function(index, value){
			row.append($('<td>').text(MEME.textValue(value)));
		});
		table.append(row);
		return row;
	},
	fillSelect: function(select, items, placeholder, valueKey, labelBuilder, selectedValue){
		select.empty().append($('<option>').val('').text(placeholder));
		$.each(items || [], function(index, item){
			var value = MEME.textValue(item[valueKey]);
			var option = $('<option>').val(value).text(MEME.textValue(labelBuilder(item)));
			if(MEME.textValue(selectedValue) === value){
				option.prop('selected', true);
			}
			select.append(option);
		});
	},
	prepareMessage: function(params, type){
		var box = $('#mb-custom-meme');
		box.removeClass('message-box-danger message-box-success message-box-info message-box-warning')
			.addClass(type);
		MEME.setTitle(params);
		MEME.setContent(params);
		return box;
	},
	information: function(params){
		var box = MEME.prepareMessage(params, 'message-box-info');
		box.find('.mb-container').removeClass('topm_200');
		$('#btn-mb-meme').empty().append(MEME.closeButton('Tutup'));
		playAudio('alert');
		box.show();
		return false;
	},
	confirm: function(params){
		var box = MEME.prepareMessage(params, 'message-box-warning');
		var target = MEME.internalUrl(params.url);
		var buttons = $('#btn-mb-meme').empty();
		box.find('.mb-container').removeClass('topm_200');
		if(target !== ''){
			buttons.append($('<a>', {
				href: target,
				'class': 'btn btn-success btn-lg'
			}).text('Ya'));
		}
		buttons.append(MEME.closeButton('Tidak'));
		playAudio('alert');
		box.show();
		return false;
	},
	modal: function(params){
		var box = MEME.prepareMessage(params, 'message-box-success');
		var content = $('#content-mb-meme').empty();
		var target = MEME.internalUrl(params.url);
		var width = Math.max(200, Math.min(1600, parseInt(params.size[0], 10) || 800));
		var height = Math.max(200, Math.min(1200, parseInt(params.size[1], 10) || 400));
		box.find('.mb-container').addClass('topm_200');
		if(target !== ''){
			content.append($('<iframe>', {
				src: target,
				width: width,
				height: height,
				frameborder: 0,
				title: MEME.textValue(params.title)
			}));
		}
		content.append($('<p>').text(MEME.textValue(params.body)));
		if($.trim(MEME.textValue(params.desc)) !== ''){
			content.append($('<p>').text(MEME.textValue(params.desc)));
		}
		$('#btn-mb-meme').empty().append(MEME.closeButton('Tutup'));
		playAudio('alert');
		box.show();
		return false;
	}
};
