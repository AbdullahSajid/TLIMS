var handleResultContent = function () {
     $('#test_results').froalaEditor({
      toolbarButtons: ['bold', 'italic', 'underline','strikeThrough', 'subscript', 'superscript','fontFamily', 'fontSize','|','paragraphStyle','paragraphFormat', 'align', 'formatOL', 'formatUL', 'indent', 'outdent', 'insertTable','|','insertHR','undo', 'redo','clearFormatting', 'selectAll', 'html','help','fullscreen'],
      heightMin: 100,
      heightMax: 250,
      placeholderText: 'Click the \'set default content\' checkbox to write test results in table',
//   initOnClick: true,
      tableResizerOffset: 10,
//   increased it from normal 50 to 700 to avoid resizing the table
      tableResizingLimit: 700,
      quickInsertButtons: ['image', 'table', 'ol', 'ul', 'hr'],
//   choose font family options in toolbar, we are chhosing only 1 family
      fontFamily: {
        'Arial,Helvetica,sans-serif': 'Arial'
      },
    /*we can choose many different options in dropdown, but we are restricting to only one font-size which is 12.*/
      fontSize: ['12'],
    /*shows a dropdown of the actual font family name for the current text selection.*/   
//    fontFamilySelection: true,
//    by default the font-size is 12, we can change it       
//    fontSizeDefaultSelection: '12',
      htmlRemoveTags: ['script','base']
    });
};
var handleTestConditions = function () {
     $('#test_conditions').froalaEditor({
      toolbarButtons: ['bold', 'italic', 'underline','strikeThrough', 'subscript', 'superscript','fontFamily', 'fontSize','|','paragraphStyle','paragraphFormat', 'align', 'formatOL', 'formatUL', 'indent', 'outdent','insertLink', 'insertTable','|','insertHR','undo', 'redo','clearFormatting', 'selectAll', 'html','fullscreen'],
      placeholderText: 'Click the \'set default content\' checkbox to write any notes or test conditions in list',
      heightMin: 100,
      heightMax: 250,
      quickInsertButtons: ['image', 'table', 'ol', 'ul', 'hr'],
      fontFamily: {
        'Arial,Helvetica,sans-serif': 'Arial'
      },
      fontSize: ['12'],
      htmlRemoveTags: ['script','base']
    });
};
var setDefaultContent = function () {
    $('#set_default_content').on('change',function(){
     if($(this).is(':checked')){
    
         var markupContent = '<table style="width: 100%;font-size: 12px;font-family: Arial,Helvetica,sans-serif;"><tbody style="text-align:center;"><tr><td style="width: 33.3333%;font-weight:bold;">Test Attribute</td><td style="width: 33.3333%;font-weight:bold;">Value</td><td style="width: 33.3333%;font-weight:bold;">C.V. (%)</td></tr><tr><td><br></td><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td><td><br></td></tr></tbody></table>';
         $('#test_results').froalaEditor('html.set', markupContent);
     }
     else{
          $('#test_results').froalaEditor('html.set','');
     }

});
    $('#set_default_content_2').on('change',function(){
     if($(this).is(':checked')){
         var markupContent = '<ul style="font-size: 12px;font-family: Arial,Helvetica,sans-serif;"><li>Condition 1</li><li>Condition 2</li><li>Condition 3</li></ul>';
         $('#test_conditions').froalaEditor('html.set', markupContent);
     }
     else{
          $('#test_conditions').froalaEditor('html.set',''); 
     }

});
$('#send_test_file_data').on('change',function(){
     if($(this).is(':checked')){
        $("#test_file").attr("required", true); 
        $('#test_results').froalaEditor('html.set',''); 
        // we can also destroy froala here but we are only disabling it
        $('#test_results').froalaEditor('edit.off');
         // also uncheck set default content checkbox if checked
        $('#set_default_content').prop('checked', false);
         // then disable set default content checkbox, bcz we don't need it
        $("#set_default_content").prop("disabled", true);
     }
     else{
         $("#test_file").attr("required", false); 
         $('#test_results').froalaEditor('edit.on');
         // enable set default content checkbox, if disabled earlier
         $("#set_default_content").prop("disabled", false);
     }
});
};
var postForm = function() {
    // test results authentication
     if($('#send_test_file_data').is(":not(:checked)")){
         var test_results_html = $('#test_results').froalaEditor('html.get');
         var results_char_count = $('#test_results').froalaEditor('charCounter.count');
         if (test_results_html == '' || test_results_html == "undefined" || test_results_html == null) {       
             swal({
                title             : "Test Result box must not be empty",
                text              : "",
                type              : "warning",
                showCancelButton  : !0,
                confirmButtonClass: "btn-warning",
                confirmButtonText : "Okay!"
             });
             return false;  
         }
         else if(results_char_count == null || results_char_count < 6){
             swal({
                title             : "Test result box has limited characters",
                text              : "",
                type              : "warning",
                showCancelButton  : !0,
                confirmButtonClass: "btn-warning",
                confirmButtonText : "Okay!"
            });
            return false;
         }
     }
    // test condtions authentication
    var conditions_char_count = $('#test_conditions').froalaEditor('charCounter.count');
    if(conditions_char_count != null && conditions_char_count != 0 && conditions_char_count <= 2){
             swal({
                title             : "Test Conditions box has limited characters",
                text              : "Make sure to remove any white spaces if there are no test conditions",
                type              : "warning",
                showCancelButton  : !0,
                confirmButtonClass: "btn-warning",
                confirmButtonText : "Okay!"
            });
            return false;
    }
};
var SubmitTestResults = function () {
    "use strict";
    return {
        init: function () {
            handleResultContent();
            handleTestConditions();
            setDefaultContent();
        }
    }
}();