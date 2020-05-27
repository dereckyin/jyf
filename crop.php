<!-- Cover Modal -->
<div class="modal fade" id="coverModal" role="dialog" aria-labelledby="modalLabel" tabindex="-1" style="top: 0;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">照片</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
      </div>
      <div class="modal-body">
        <div class="img-container">
          <img id="coverImage" src="" alt="cover Image">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn pull-left" data-dismiss="modal" style="width: 80px;">取消</button>
        <button id="coverSave" type="button" class="btn btn-default" data-dismiss="modal" style="width: 80px;">儲存</button>
      </div>
    </div>
  </div>
</div>
<!-- Cover Modal -->

<link rel="stylesheet" href="assets/crop/cropper.css">
<script src="assets/crop/cropper.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', function () {
    /* crop */
        var coverImage = document.getElementById("coverImage");
        var cropper;

        $("#inputCover").on("change", function(){
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $("#coverImage").attr('src', e.target.result);
                    $("#coverImage").width("100%");
                }

                reader.readAsDataURL(input.files[0]);

                // $("#coverModal").modal({'backdrop':'static'});
                $("#coverModal").modal("show");
            }
            $(this).val("");
        });

        $('#coverModal').on('shown.bs.modal', function () {
            cropper = new Cropper(coverImage, {
                aspectRatio: 4 / 3,
                movable: false,
                rotatable: false,
                scalable: false,
                zoomable: false,
                zoomOnTouch: false,
                zoomOnWheel: false,
            });

                $(".modal").css({
                    'z-index': '1049'
                });
                // $(".modal-backdrop").on('click', function(event) {
                //     return false;
                // });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
        });

        $("#coverSave").on('click', function(event) {
            var result = cropper.getCroppedCanvas();
            $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);

            $.ajax({
                url: "vendor/img_upload",
                data: {
                    imageData: result.toDataURL("image/jpeg")
                },
                type:"POST",
                dataType:'text',
                success: function(msg){
                    $("#coverArea").append("<div class='col-lg-3 col-md-4 col-sm-6 col-xs-6' style='border: 1px solid #DDD; background-color:#FFF; padding: 0;'><img src='"+msg+"' style='width:94%; margin:3%;'><button type='button' onclick='delCover(this, "+'"'+msg+'"'+"); return false;' class='btn btn-sm del-btn'><span class='fa fa-fw fa-trash'></span></button></div>");
                    $("#coverurls").val($("#coverurls").val()+msg+",");
                },
                error:function(xhr, ajaxOptions, thrownError){ 
                    alert("照片上傳發生錯誤"); 
                }
            });

            cropper.destroy();
        });

        /* crop end */
    });

    function delCover(obj, pic){
        if (!confirm("確定刪除此照片嗎?刪除後請按下方儲存鈕，才會真正刪除。")) return;
        $(obj).parent("div").fadeOut();
        $("#coverDeleted").val($("#coverDeleted").val()+pic+",");
    }
</script>