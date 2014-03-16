// JavaScript Document
function fk_ajax_exec(xParams){
	/*
	 * xParams={pDiv:'', pUrl:'', pArgs:'', pUrlAfter:'', insertMode:''};
	 */
	var Loading  = '';
	   if(xParams.showLoading !=undefined){
		   if(xParams.showLoading == true){
			    Loading = '<div class="loading-img"><img src="'+HTTP+'_HTML/img/ajax-loader.gif" /></div>';	   
		   }
	   }
	   if(xParams.insertMode==''){$('#'+xParams.pDiv).hide();}
	   
	   if ($("#loading-message").length==0){ $('body').prepend('<div id="loading-message">Cargando...</div>');}
	   
	   $("#loading-message").show();
	   

 	  
	   //$('#'+xParams.pDiv).css({ opacity: 0.3 });
		if(xParams.insertMode==''){ if(xParams.showLoading == true){$('#'+xParams.pDiv).prepend(Loading);}	}
		if(xParams.insertMode=='top'){	$('#'+xParams.pDiv).prepend(Loading);}
		if(xParams.insertMode=='bottom'){	$('#'+xParams.pDiv).append(Loading);}
				
        var response = $.ajax({ type: "POST",   
                          url : HTTP_FILE+xParams.pUrl,   
                        async : false,
						 data : xParams.pArgs+'&ajax=1',
                      success : function(resp) {
                    	  
                    	  setTimeout('$("#loading-message").hide();',500);
                    	  
         		            if(xParams.insertMode==''){
         		            	  
                            	  $('#'+xParams.pDiv).fadeIn();
        		
				            	var htmlStr  = $('#'+xParams.pDiv).html();
				            	if(htmlStr!=resp){
				            		$('#'+xParams.pDiv).html(resp);	
				            	}
							}else{
							   $('.loading-img').html(''); 
							}
				            if(xParams.insertMode=='top'){
								$('#'+xParams.pDiv).prepend(resp);								
							}							
							if(xParams.insertMode=='bottom'){
								$('#'+xParams.pDiv).append(resp); 
							}							
							
							if( xParams.pUrlAfter != '' ){ 
							   window.open(HTTP+xParams.pUrlAfter,'_self'); 
							}
		   
                     } // Success
        });	  // $.ajax
} // fk_ajax_exec

/* function fk_ajax_submit(p_div,p_url,p_form,p_url_after){ */
function fk_ajax_submit(xVars){
     
	 var formArgs = $("#"+xVars.pForm).serialize();
	
	 pVarsData = {pDiv:xVars.pDiv,
				  pUrl:xVars.pUrl,
				  pArgs:formArgs+'&'+xVars.pArgs,
				  pUrlAfter:xVars.pUrlAfter,
				  insertMode:xVars.insertMode,
				  showLoading:true}; 
	
     fk_ajax_exec(pVarsData);
	 
}

function fk_ajax_json(xParams,submit){
	var formArgs = '';
	if(submit){ formArgs = $("#"+xParams.pForm).serialize(); }
	
	$.ajax({
		  url: HTTP_FILE+xParams.pUrl,
		  type: "POST",
		  dataType: 'json',
		  data: formArgs+'&'+xParams.pArgs+'&ajax=1',
		  success: function(d){
			  if(d.js){eval(d.js);}
		  }
	});
}
function fk_toggle(Objt){
	 $(Objt).slideToggle();
}

function oculta(IdObj){
	 $('#data_'+IdObj).slideToggle();
}

function hide(IdObj){
	var options = {};
	$("#"+IdObj).hide( "highlight", options, 500);
}
function fk_show(IdObj){
	var options = {};
	$("#"+IdObj).show( "highlight", options, 500);
}
// AppForm
function appForm_updfldTxt(data){
	//alert(data.id);
	$('#'+data.id).show();
	$('#'+data.id).focus();
	$('#val-'+data.id).hide();
	$('#'+data.id).blur(function(){
		cur_val = $('#cur-v-'+data.id).val();
		new_val = $('#'+data.id).val();
		$('#val-'+data.id).html(new_val);
		$('#'+data.id).hide();
		$('#val-'+data.id).show();
		// Resaltar como cambio 
		if(cur_val!=new_val){
			$('#val-'+data.id).addClass('nw');
		}else{
			$('#val-'+data.id).removeClass('nw');
		}
		
	});
}
function appForm_ClearPopupSrc(data){
	$('#'+data.id+'-2').val('');
	$('#'+data.id).val('');
}
function appForm_PopupSrc(data){
	var val = $('#'+data.id+'-2').val();
	if(val!='' || data.forceOpen==true){
		fk_ajax_exec({pDiv:'srcfld-rs-'+data.id, pUrl:'FkMaster/PopupSrc', pArgs:'idf='+data.id+'&v='+val+'&t='+data.tbl+'&forceOpn='+data.forceOpen, pUrlAfter:'', insertMode:''});
	}
}
function appForm_PopupSrcSel(i,id){
	v_id = $("#td_"+i+"-0").html();
	v_text = $("#td_"+i+"-1").html();
	
	$("#"+id).val(v_id);
	$("#"+id+"-2").val(v_text);
	$("#psrc-"+id).dialog("close");
	$("#psrc-"+id).remove();	
}
// DATAGRID control FkGrid()
var DEBUG = true;
function dg_funct(tName,row,fnct){
   if (eval("typeof " +tName+  "_"+fnct+"_click == 'function'")) {
	   eval(tName+  "_"+fnct+"_click('"+tName+"','"+row+"')");
   }else{
	   dg_debug_msg('function '+tName+'_'+fnct+'_click() does not exist');
   }
}
function dg_get_val(tName,row,col){ 
  if($('#dg_'+tName+'_'+row+'_'+col)){
    return $('#dg_'+tName+'_'+row+'_'+col).val();
  }else{ return '';
  }
  
}
// appform
//FkConfig
function FkConf_ChgPrivMode(){
	var v_index = $('#mode').val();
	
	if(v_index==1){
		$('#tr-controller').show();
		$('#tr-action').show();
		$('#id_controller').addClass('required');
		$('#id_action').addClass('required');
		
		$('#tr-table').hide();
		$('#tr-field').hide();
		$('#table_name').removeClass('required');
		$('#field_name').removeClass('required');
		
		$('#id_controller').focus();
		
	}else{
		if(v_index==2){
			
			$('#tr-controller').hide();
			$('#tr-action').hide();
			$('#id_controller').removeClass('required');
			$('#id_action').removeClass('required');
			
			$('#tr-table').show();
			$('#tr-field').show();
			$('#table_name').addClass('required');
			$('#field_name').addClass('required');
			
			$('#table_name').focus();

		}else{
			// Limpia todo
			$('#tr-controller').hide();
			$('#tr-action').hide();
			$('#tr-table').hide();
			$('#tr-field').hide();
		} // 
	} // 
 
}
function fkCheckAll(obj,selector){
	checked = $(obj).attr('checked');
	if(checked==true){ $(selector).attr('checked',true); }else{ $(selector).attr('checked',false);}
}
function fkSelect(obj,cancel){
	if(cancel){
		id = obj.attr('id').substring(0,obj.attr('id').length-7);
	}else{
		id = obj.attr('id');
	}
	$("#"+id+'-txt').removeClass("required");
	if($("#"+id+'-txt').is(':visible')){
		$("#"+id+'-txt').hide();
		$("#"+id+'-cancel').hide();
		$("#"+id).show();
		$('#'+id+' :nth-child(1)').attr('selected', 'selected'); 
	}else{
		
		if($("#"+id).val()=='new'){
			$("#"+id+'-txt').show();
			$("#"+id+'-txt').focus();
			$("#"+id+'-cancel').show();
			$("#"+id).hide();	
			// siempre requerida no puede ir vacia
			$("#"+id+'-txt').addClass("required");
		}
		
		
	}
	
}