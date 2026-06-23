<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function palaplast_enqueue_styles() {
	$should_enqueue = function_exists( 'is_product' ) && is_product();

	if ( ! $should_enqueue && is_singular() ) {
		$post = get_post();
		if ( $post instanceof WP_Post ) {
			$should_enqueue = has_shortcode( $post->post_content, 'palaplast_technical_sheets_list' ) || has_shortcode( $post->post_content, 'palaplast_pricelists_list' ) || has_shortcode( $post->post_content, 'palaplast_certificates_list' );
		}
	}

	if ( ! $should_enqueue ) {
		return;
	}

	wp_enqueue_style( 'palaplast', PALAPLAST_PLUGIN_URL . 'assets/css/palaplast-frontend.css', array(), PALAPLAST_VERSION );

	wp_enqueue_script( 'jquery-core' );
	wp_add_inline_script( 'jquery-core', palaplast_get_scripts() );
}

function palaplast_enqueue_admin_assets( $hook_suffix ) {
	$screen                = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$is_certificate_editor = in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) && $screen && 'palaplast_cert' === $screen->post_type;
	$is_product_editor     = in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) && $screen && 'product' === $screen->post_type;
	$is_plugin_page        = in_array( $hook_suffix, array( 'woocommerce_page_palaplast-dashboard', 'woocommerce_page_palaplast-technical-sheets', 'woocommerce_page_palaplast-pricelists', 'woocommerce_page_palaplast-variation-colors', 'woocommerce_page_palaplast-job-settings' ), true );

	if ( ! $is_plugin_page && ! $is_certificate_editor && ! $is_product_editor ) {
		return;
	}

	wp_enqueue_style( 'palaplast-admin', PALAPLAST_PLUGIN_URL . 'assets/css/palaplast-admin.css', array(), PALAPLAST_VERSION );

	if ( 'woocommerce_page_palaplast-technical-sheets' === $hook_suffix ) {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_add_inline_script(
			'jquery-ui-sortable',
			<<<'JS'
jQuery(function($){$('.palaplast-sortable-sheets').sortable({axis:'y',handle:'.palaplast-sort-handle',items:'> tr',placeholder:'palaplast-sortable-placeholder',helper:function(e,tr){var originals=tr.children();var helper=tr.clone();helper.children().each(function(index){$(this).width(originals.eq(index).width());});return helper;},update:function(){var tbody=$(this);tbody.find('tr').each(function(){var input=$(this).find('input[type=hidden]');input.appendTo($(this).find('.palaplast-sort-handle-column'));});}});});
JS
		);
	}

	if ( 'woocommerce_page_palaplast-variation-colors' === $hook_suffix ) {
		wp_enqueue_script( 'wp-util' );
		return;
	}

	if ( $is_product_editor ) {
		return;
	}

	if ( 'woocommerce_page_palaplast-pricelists' === $hook_suffix ) {
		$selection_title = __( 'Select Pricelist PDF', 'palaplast' );
	} elseif ( $is_certificate_editor ) {
		$selection_title = __( 'Select Certificate PDF', 'palaplast' );
	} else {
		$selection_title = __( 'Select Technical Sheet PDF', 'palaplast' );
	}

	wp_enqueue_media();
	wp_add_inline_script(
		'jquery-core',
		"jQuery(function($){var pdfFrame,logoFrame;$('.palaplast-select-pdf').on('click',function(e){e.preventDefault();if(pdfFrame){pdfFrame.open();return;}pdfFrame=wp.media({title:'" . esc_js( $selection_title ) . "',button:{text:'" . esc_js( __( 'Use PDF', 'palaplast' ) ) . "'},library:{type:'application/pdf'},multiple:false});pdfFrame.on('select',function(){var attachment=pdfFrame.state().get('selection').first().toJSON();$('#palaplast_attachment_id').val(attachment.id);$('.palaplast-selected-file').text(attachment.filename || attachment.url);});pdfFrame.open();});$('.palaplast-remove-pdf').on('click',function(e){e.preventDefault();$('#palaplast_attachment_id').val('');$('.palaplast-selected-file').text('" . esc_js( __( 'No file selected.', 'palaplast' ) ) . "');});$('.palaplast-select-brand-logo').on('click',function(e){e.preventDefault();if(logoFrame){logoFrame.open();return;}logoFrame=wp.media({title:'" . esc_js( __( 'Select Brand Logo', 'palaplast' ) ) . "',button:{text:'" . esc_js( __( 'Use Logo', 'palaplast' ) ) . "'},library:{type:'image'},multiple:false});logoFrame.on('select',function(){var attachment=logoFrame.state().get('selection').first().toJSON();$('#palaplast_brand_logo_id').val(attachment.id);$('.palaplast-selected-brand-logo').text(attachment.filename || attachment.url);});logoFrame.open();});$('.palaplast-remove-brand-logo').on('click',function(e){e.preventDefault();$('#palaplast_brand_logo_id').val('');$('.palaplast-selected-brand-logo').text('" . esc_js( __( 'No logo selected.', 'palaplast' ) ) . "');});});"
	);
}

function palaplast_get_scripts() {
	return <<<'JS'
jQuery(function($){$(document).on('click','.palaplast-table .palaplast-copy-code',function(){var button=this;var value=button.getAttribute('data-copy-value');if(!value){return;}var onCopied=function(){button.classList.add('is-copied');var textEl=button.querySelector('.palaplast-copy-code__text');if(textEl){textEl.textContent='Copied';}clearTimeout(button._palaplastCopyTimer);button._palaplastCopyTimer=setTimeout(function(){button.classList.remove('is-copied');if(textEl){textEl.textContent='Copy';}},1200);};if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(value).then(onCopied).catch(function(){var fallback=$('<textarea>').val(value).css({position:'fixed',opacity:0}).appendTo('body');fallback[0].select();try{document.execCommand('copy');onCopied();}catch(e){}fallback.remove();});return;}var fallback=$('<textarea>').val(value).css({position:'fixed',opacity:0}).appendTo('body');fallback[0].select();try{document.execCommand('copy');onCopied();}catch(e){}fallback.remove();});});
JS;
}
