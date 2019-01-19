jQuery(document).ready(function($){
  const $loadContainer = $('.js-load-ajax');
  const $ajaxButton = $('.js-call-ajax');

  function callAjax(e) {
    e.preventDefault();

    const ajaxAction = 'backend_ajax_action';
    const nonceValue = $('#sample_ajax_nonce').val();

    if ( ! $loadContainer.hasClass('is-loading') ) {
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          'action': ajaxAction,
          'nonce': nonceValue
        },
        beforeSend: function() {
          $loadContainer.addClass('is-loading');
        },
        success: function( dataOut ) {
          const message = dataOut.data.response;

          $loadContainer.html(message);
        },
        complete: function( data ) {
          $loadContainer.removeClass('is-loading');
        },
        error: function( jqXHR, textStatus, errorThrown ) {
          console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
        }
      });
    }
  }

  $ajaxButton.on('click', callAjax);
});
