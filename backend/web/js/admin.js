/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
adminFileInputDeleteIds = [];
adminFileInputDeleteField = '';
function adminFileInput()
{
        attachDelete("");
        $('.file-preview .fileinput-remove').remove();     
}

function attachDelete(id)
{
    $(id + " .widget-file-remove").click(function(a){ 
       
      var l = $(this);
      var h = l.attr("href");
      var d = l.attr('data-trigger');
      var n = l.parent().attr('data-fileindex').split('_')[1];
      var a = l.parents('.file-input').find('input[type=file]').attr('data-krajee-fileinput');
    

      $.get(h,function(d){}); 
     
      window[a].initialPreview.splice(n,1) ;
      $('#objectsapartment-images').fileinput('refresh');
      attachDelete('.field-'+d);

      return false; 

    });
      
}






