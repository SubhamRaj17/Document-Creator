//Section Number
var section = 8 ;


//Data Fetch
//var url = apiUrl+'/section8/fetch.php?prs=' + prs + '&token=' + token ;

//Fill div area using spreadsheet data
$(document).ready(function(){
  $('#load_excel_form').on('submit', function(event){
    event.preventDefault();
    $.ajax({
      url:"../upload.php",
      method:"POST",
      data:new FormData(this),
      contentType:false,
      cache:false,
      processData:false,
      success:function(data)
      {
        $('#excel_area').html(data);
        $('mainContainer').css('width','100%');
        
      }
    })
  });
});