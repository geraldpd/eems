$(function () {
    $(".schedule-row").each((i, schedule_row) => {
        let schedule_picker = $(schedule_row).find(".schedule-picker");
        let index = schedule_picker.data("day");
        let schedule_start = $("#schedule-" + index + "-start");
        let schedule_end = $("#schedule-" + index + "-end");

        schedule_start.off().on("change", function () {
            schedule_end.prop("min", $(this).val());
            let time = $(this).val().split(":");

            let max_schedule_end = moment()
                .set({
                    hour: time[0],
                    minute: time[1],
                })
                .add(30, "minute")
                .format("HH:mm");

            schedule_end.val(max_schedule_end);
        });

        schedule_end.off().on("change", function () {
            //schedule_start.prop('max', $(this).val());
        });
    });

    $("#location").on("change", function () {
        let location = $(this).val();
        let additional_field = $(".location-additional-field");
        additional_field
            .find(".form-group")
            .each((i, div) => $(div).addClass("d-none"));
        additional_field.find(`.location-${location}`).removeClass("d-none");
    });

    window.ClassicEditor.create($("#description").get(0), {
        removePlugins: [
            "CKFinderUploadAdapter",
            "CKFinder",
            "EasyImage",
            "Image",
            "ImageCaption",
            "ImageStyle",
            "ImageToolbar",
            "ImageUpload",
            "MediaEmbed",
        ],
    })
        .then((editor) => {
            //console.log( editor );
        })
        .catch((error) => {
            //console.error( error );
        });

    $("#location").trigger("change");

    $("#banner").on("change", function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#banner_preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    $("#banner").on("change", function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#banner_preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // if(!  _.keys(config.event.uploaded_documents).length) {
    //     $('.uploaded-documents').addClass('d-none')
    // }
});
