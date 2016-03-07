// JavaScript Document
/*
 * Function: fkComments
 * Purpose:  Comments Object
 * Returns:  -
 * Inputs:   object:oInit - initalisation options 
 */
/*
(function($) {	 
$.fn.fkComments = function( oInit ){
	
	alert(this.id);
	function leaveCommentOn(){
		   alert('Deja comment');
	}	
}  
})(jQuery);

$('#fk-commenter-1').fkComments();*/

var Lang = {
		leaveAComment : 'Deja un comentario...',
		nameRequired : '<div>-El nombre es requerido</div>',
		emailRequired : '<div>-El correo no es v&aacute;lido</div>',
		commentRequired : '<div>-El comentario es requerido</div>'
	};

function sendFkComment(idObj, pUrl, Code, idTabVal){
	var cmt = $('#leave-comment-' + idObj).val();
	var name = $('#name-user-' + idObj).val();
	var email = $('#email-user-' + idObj).val();
	var web = $('#web-user-' + idObj).val();
	var err = 0;
	var errMsg = '';

	if ($('#name-user-' + idObj).val() == '') {
		$('#name-user-' + idObj).addClass('alert');
		errMsg += Lang.nameRequired;
		err++;
	}

	

	if (cmt == '') {
		$('#leave-comment-' + idObj).addClass('alert');
		errMsg += Lang.commentRequired;
		err++;
	}

	if (err == 0) {

		var pArgs = {
			pDiv : 'comments-' + idObj,
			pUrl : pUrl,
			pArgs : 'op=save&code=' + Code + '&id-t-val='
					+ idTabVal + '&comment=' + cmt
					+ '&name=' + name + '&email=' + email
					+ '&web=' + web,
			pUrlAfter : '',
			insertMode : 'top'
		};
		fk_ajax_exec(pArgs);
		// Limpiar
		$('#leave-comment-' + idObj).blur();
		$('#leave-comment-' + idObj).val("");
		$('#name-user-' + idObj).val("");
		$('#email-user-' + idObj).val("");
		$('#web-user-' + idObj).val("");
		$('.leave-comment').removeClass('on');

		$('#message-err-' + idObj).hide();
		$('#name-user-' + idObj).removeClass('alert');
		$('#email-user-' + idObj).removeClass('alert');
		$('#web-user-' + idObj).removeClass('alert');
		$('#leave-comment-' + idObj).removeClass('alert');
		$('#leave-comment-' + idObj)
				.val(Lang.leaveAComment);
		

	} else {
		$('#message-err-' + idObj).html(
				'<h3>Error:</h3>' + errMsg);
		$('#message-err-' + idObj).show('slow');
	}
	
}

function fkComments(idObj, pUrl, Code, idTabVal) {

	
	
	$('#leave-comment-'+idObj).bind('keydown',function(e){
		if(e.which==13){sendFkComment(idObj, pUrl, Code, idTabVal); }
		
	});
	

	$('#leave-comment-' + idObj).val(Lang.leaveAComment);

	$('#leave-comment-' + idObj).focus(function() {
		
		// Limpiar
		if ($(this).val() == Lang.leaveAComment) {

			$(this).val("");
		}

		$('.leave-comment').addClass('on');
		if ($('#name-user-' + idObj).val() == '') {
			$('#name-user-' + idObj).focus();
		} else {
			if ($('#email-user-' + idObj).val() == '') {
				$('#email-user-' + idObj).focus();
			}
		}

	});

	

	$('#leave-comment-btn-' + idObj).click(function() {
		sendFkComment(idObj, pUrl, Code, idTabVal);
	
	});
	
	

}