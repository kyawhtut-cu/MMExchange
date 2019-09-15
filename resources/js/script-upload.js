function initImageUpload(route, eleValue, category) {
  var $elm = $(eleValue);

  var imageUrl = $elm.val();
  if (imageUrl != '') {
    $('#image-add-continer').hide();
    $("img#image-preview").attr('src', imageUrl.replace(".", route));
  } else {
    $('#image-preview-container').hide();
  }

  $('#upload-btn').on('click', function (){
      $('#upload-input').click();
      $('.progress-bar').text('0%');
      $('.progress-bar').width('0%');
  });

  $('#upload-input').uploader({
    url: route + '/api/upload',
    type: 'POST',
    progressbar: '.progress-bar',
    complete: function(data) {
      if (typeof data === 'object') {
        if (data.message == 'success' && Array.isArray(data.files) && data.files.length > 0) {
          $('#image-add-continer').hide();
          $("img#image-preview").attr('src', data.files[0].replace(".", route));
          $elm.val(data.files[0]);
          // console.log(data);
          $('#image-preview-container').show();
        }
      } else {
        // console.log("Not work");
      }
    }
  });

  $('.image-delete').on('click', function(ev) {
    if (confirm('Are you sure to remove this image?')) {
      $.ajax({
        url: route + '/api/delete',
        type: 'POST',
        data: {'file': $elm.val()},
        success: function(data){
          if (data.message == 'success') {
            $elm.val('');
            $("img#image-preview").attr('src', '');
            $('#image-add-continer').show();
            $('#image-preview-container').hide();
          } else {
            alert(data.error);
          }
        },
        error: function(xhr, status, error) {
          console.log(error);
        }
      });
    }
  });
}