// JavaScript Document
/*
 * Function: fkComments
 * Purpose:  Comments Object
 * Returns:  -
 * Inputs:   object:oInit - initalisation options 
 */
var Lang = {
		leaveAComment : 'Deja un comentario...',
		nameRequired : '<div>-El nombre es requerido</div>',
		emailRequired : '<div>-El correo no es v&aacute;lido</div>',
		commentRequired : '<div>-El comentario es requerido</div>',
	    deleteComment : 'Desea eliminar comentario?'
	};
function fkComments(idObj, pUrl, Code, idTabVal) {

	

	$('#leave-comment-' + idObj).val(Lang.leaveAComment);

	$('#leave-comment-' + idObj).focus(function() {
		// Limpiar
		if ($(this).val() == Lang.leaveAComment) {
			$(this).val("");
		}

		$('#li-lv-' + idObj).addClass('on');
		if ($('#name-user-' + idObj).val() == '' ) {
			$('#name-user-' + idObj).focus();
		} else {
			if ($('#email-user-' + idObj).val() == '') {
				$('#email-user-' + idObj).focus();
			}else{
				$('#leave-comment-' + idObj).focus();
			}
		}

	});
	
	$('#leave-comment-' + idObj).blur(function(){
		val_cmt = $('#leave-comment-' + idObj).val();
		if(val_cmt==''){$('#li-lv-' + idObj).removeClass('on');}
		
	});

	$('#leave-comment-btn-' + idObj)
			.click(
					function() {
						// <?=$this->id_obj?>

						var cmt = $('#leave-comment-' + idObj).val();
						var name = $('#name-user-' + idObj).val();
						var email = $('#email-user-' + idObj).val();
						var web = $('#web-user-' + idObj).val();
						var err = 0;
						
						var errMsg = '';

						if (name != undefined) {
							if (name == '') {
								$('#name-user-' + idObj).addClass('alert');
								errMsg += Lang.nameRequired;
								err++;
							}
						} else {
							name = '';
						}

						if (email != undefined) {
							if (isEmail('#email-user-' + idObj) == false) {
								$('#email-user-' + idObj).addClass('alert');
								errMsg += Lang.emailRequired;
								err++;
							}
						} else {
							email = '';
						}

						if (web == undefined) {
							web = '';
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
								insertMode : 'bottom'
							};
							fk_ajax_exec(pArgs);
							// Limpiar
							$('#leave-comment-' + idObj).val("");
							if (name != '') {
								$('#name-user-' + idObj).val("");
							}
							if (email != '') {
								$('#email-user-' + idObj).val("");
							}
							if (web != '') {
								$('#web-user-' + idObj).val("");
							}
							$('.leave-comment').removeClass('on');

							$('#message-err-' + idObj).hide();
							if(name!=""){$('#name-user-' + idObj).removeClass('alert');}
							if(email!=""){$('#email-user-' + idObj).removeClass('alert');}
							if(web!=""){$('#web-user-' + idObj).removeClass('alert');}
							$('#leave-comment-' + idObj).removeClass('alert');
							$('#leave-comment-' + idObj).val(Lang.leaveAComment);

						} else {
							//$('#message-err-' + idObj).html('<h3>Error:</h3>' + errMsg);
							//$('#message-err-' + idObj).show('slow');
						}

					});

}
function del_coment(args){
	
	if(confirm(Lang.deleteComment)){
		$('#com-'+args.i +'-'+ args.o).hide();
		var pArgs = {
				pDiv : 'oper-comments-' + args.o,
				pUrl : args.u,
				pArgs : 'op=del&i=' + args.i+'&id-t-val='+args.it ,
				pUrlAfter : '',
				insertMode : ''
			};
			fk_ajax_exec(pArgs);		
	}
}