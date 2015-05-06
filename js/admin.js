jQuery(document).ready(function($){
	var htmlOutput = '<div id="pts_settings_wrap" style="display: none;">'+
		'<h1>Settings</h1>'+
		'<table id="pts_formtable">'+
			'<tr>'+
				'<td class="pts_cellname">'+
					'Layout Style'+
				'</td>'+
				'<td class="pts_celloption">'+
					'<select id="pts_layoutstyle">'+
						'<option value="simple">Simple</option>'+
						'<option value="modern">Modern</option>'+
					'</select>'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<td colspan="3">'+
					'<hr class="pts_divider">'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<td colspan="3">'+
					'<h3 class="pts_subheader">Modern Minimum Height</h3>'+
					'<p class="pts_paragraph">For the modern layout style, you can set a minimum height in order that each pricing tier looks modular.</p>'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<td class="pts_cellname">'+
					'Minimum Height'+
				'</td>'+
				'<td class="pts_celloption">'+
					'<input id="pts_minheight" type="text" />'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<td colspan="3">'+
					'<button id="pts_insertpt" class="button button-primary button-large">Insert Pricing Table</button>'+
					'<button id="pts_insertcancel" class="button button-default button-large">Cancel</button>'+
				'</td>'+
			'</tr>'+
		'</table>'+
	'</div>';

	$('body').append( htmlOutput );

	$('#pts_sbtn').click(function(e){
		tb_show( "Pricing Table Shortcode", "#TB_inline?width=753&height=550&inlineId=pts_settings_wrap" );
		e.preventDefault();
	});

	$('#pts_insertcancel').click(function(){
		tb_remove();
	});

	$('#pts_insertpt').click(function(){
		var layoutStyle = $('#pts_layoutstyle').val();
		var minHeight = $('#pts_minheight').val();
		var ptsAttributes;

		if( layoutStyle == 'simple' ){
			ptsAttributes = ' layoutStyle="simple"';
		} else if( layoutStyle == 'modern' ){
			if( minHeight == '' ){
				ptsAttributes = ' layoutStyle="modern"';
			} else {
				ptsAttributes = ' layoutStyle="modern"'+
				' minHeight="'+ minHeight +'"';
			}
		}

		var shortcodeOutput = '[pts'+
		ptsAttributes + ']';

		send_to_editor( shortcodeOutput );
		tb_remove();
	});
});
