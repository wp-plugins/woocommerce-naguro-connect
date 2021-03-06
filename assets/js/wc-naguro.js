(function ($) {
    $(window).load(function () {
        $("#naguro-add-new-design-area").click(function () {
            var copy = $(".naguro-design-areas-container-ghost .naguro-design-area").clone();
            randomizeId(copy);
            copy.appendTo($(".naguro-design-areas-container"));

            bind_image_chosen($(".naguro-design-area input[type=file][name*='[image]']"), copy);
            bind_overlay_chosen($(".naguro-design-area input[type=file][name*='[overlay]']"), copy);
            bind_remove_row($(".remove_row", copy));

            bind_edit_area($(".naguro-define-image-area", copy));
            bind_close_area($(".naguro-printable-area-save-button", copy));

            activate_uploads($(".naguro-upload", copy));
        });

        bind_image_chosen($(".naguro-design-area input[type=file][name*='[image]']"));
        bind_overlay_chosen($(".naguro-design-area input[type=file][name*='[overlay]']"));
        bind_remove_row($(".naguro-design-area .remove_row"));

        bind_edit_area($(".naguro-design-area .naguro-define-image-area"));
        bind_close_area($(".naguro-printable-area-save-button"));

        bind_float_check($(".naguro-float-val"));

        activate_uploads($(".naguro-upload"));
    });

    function activate_uploads(objs) {
        objs.each(function () {
            var obj = $(this);
            var root = obj.parent();
            var hiddenInput = root.find("input[name='"+obj.data("hidden-name") + "']");
            var upload = obj.next();
            var uploadContainer = obj.find(".upload-file");

            upload.append(obj);
            uploadContainer.append(upload.find(".description, input"));
            obj.find("a").css("cursor", "pointer").on("click", function () {
                obj.removeClass("opened");
                obj.addClass("closed");
                hiddenInput.val("");
            });
        });
    }

    function bind_float_check(element) {
        element.on("keyup", function (e) {
            if (this.value.match(/[^\d.]/g)) {
                this.value = this.value.replace(/[^\d.]/g, '');
            }
        }).on("blur", function () {
            if (this.value <= 0) {
                this.value = 1;
            }
        });
    }

    function bind_image_chosen(element) {
        element.on("change", singleImage);
    }

    function bind_overlay_chosen(element) {
        element.on("change", singleOverlay);
    }

    function bind_remove_row(element) {
        element.click(function () {
            var root = $(this).parent();
            $('.naguro-printable-product .background-image', root).imgAreaSelect({
                remove: true
            });
            root.remove();
        });
    }

    function randomizeId(element) {
        var rand = Math.floor((Math.random() * 89999) + 1);

        element.find(".naguro_designarea_upload_key").val(rand);
        element.find("input[type=file][name*='[image]']").attr({
            name: "naguro_designarea[image][" + rand + "]",
            id: "naguro_designarea[image][" + rand + "]"
        });
		element.find("input[type=file][name*='[overlay]']").attr({
			name: "naguro_designarea[overlay][" + rand + "]",
			id: "naguro_designarea[overlay][" + rand + "]"
		});
        element.find(".naguro-define-image-area").attr("data-id", rand);
        element.find(".naguro-printable-product").attr("id", rand);
    }

    function init_imgselectarea(x, y) {
        $('.naguro-printable-product').each(function () {
            var obj = $(this);
            var img = obj.find(".background-image");
            var imgWidth = img.width();
            var imgHeight = img.height();
            var printWidth = parseFloat(obj.find(".naguro_designarea_print_width").val());
            var printHeight = parseFloat(obj.find(".naguro_designarea_print_height").val());
            var left = parseFloat(obj.find(".naguro_designarea_left").val());
            var top = parseFloat(obj.find(".naguro_designarea_top").val());

            var pos = {
                x1: imgWidth * (left / 100), y1: imgHeight * (top / 100)
            };
            pos.x2 = pos.x1 + (imgWidth * (printWidth / 100));
            pos.y2 = pos.y1 + (imgHeight * (printHeight / 100));

            if (isNaN(pos.x1) || isNaN(pos.y1) || isNaN(pos.x2) || isNaN(pos.y2)) {
                pos.x1 = 0;
                pos.x2 = imgWidth;
                pos.y1 = 0;
                pos.y2 = imgHeight;
            }

            img.imgAreaSelect({
                handles: true,
                x1: pos.x1,
                y1: pos.y1,
                x2: pos.x2,
                y2: pos.y2,
                aspectRatio: x + ":" + y,
                onSelectEnd: handleSelection
            });
        });
    }

    function handleSelection(img, selection) {
        var obj = $(img).parent();
        var printWidth = (selection.width / $(img).width()) * 100;
        var printHeight = (selection.height / $(img).height()) * 100;
        var left = (selection.x1 / $(img).width()) * 100;
        var top = (selection.y1 / $(img).height()) * 100;

        obj.find(".naguro_designarea_print_width").val(printWidth);
        obj.find(".naguro_designarea_print_height").val(printHeight);
        obj.find(".naguro_designarea_left").val(left);
        obj.find(".naguro_designarea_top").val(top);
    }

    function singleImage(evt) {
        readSingleFile(evt, "image");
    }

    function singleOverlay(evt) {
        readSingleFile(evt, "overlay");
    }

    function readSingleFile(evt, type) {
        //Retrieve the first (and only!) File from the FileList object
        var f = evt.target.files[0];

        if (f) {
            var r = new FileReader();
            r.onload = function(e) {
                var contents = e.target.result;

                if (f.type.substr(0, 5) === "image") {
                    placeImage(contents, evt.target.parentNode.parentNode.parentNode.parentNode, type);
                } else {
                    alert("File type is not supported, choose an image.");
                }
            };

            r.readAsDataURL(f);
        } else {
            console.log("Failed to load file");
        }
    }

    function placeImage(contents, designArea, type) {
        $(".naguro-printable-product ." + (type === "image" ? "background-image" : "overlay-image"), designArea).attr("src", contents).css({
            height: "auto",
            width: "auto"
        });

        if (type === "image") {
            setTimeout(function () {
                open_design_area($(".naguro-define-image-area", designArea)[0]);
            }, 50);
        }
    }

    function bind_edit_area(element) {
        element.on("click", function () {
            open_design_area(this);
        });
    }

    function open_design_area(element) {
        tb_show("Define the printable area", "#TB_inline&modal=true");

        var id = element.getAttribute("data-id");
        var contentBox = $("#TB_ajaxContent");
        var obj = $("#" + id);

        var x = obj.parent().find("input[name='naguro_designarea[output_width][]']").val();
        var y = obj.parent().find("input[name='naguro_designarea[output_height][]']").val();

        contentBox.css({
            width: "100%",
            height: "100%",
            padding: "0",
            overflow: "visible"
        }).append(obj);

        contentBox.css({
            height: (contentBox.height() - 90) + "px"
        });

        var imgs = obj.find("img");

        for (var i = 0; i < imgs.length; i++) {
            var img = $(imgs[i]);

            if (img.height() >= img.width()) {
                img.css({
                    height: "100%",
                    width: "auto"
                });
            } else {
                img.css({
                    height: "auto",
                    width: "100%"
                });
            }
        }


        //remove close...
        $("#TB_overlay").off("click");
        $("#TB_closeAjaxWindow").remove();

        init_imgselectarea(x, y);
    }

    function bind_close_area(element) {
        element.on("click", function (e) {
            e.preventDefault();

            $(this).parent().find('.background-image').imgAreaSelect({
                remove: true
            });

            var parent = $(this).parent();
            var target = $("input[value=" + parent.attr("id") + "]");

            $(this).parent().appendTo(target.parent());

            tb_remove();
        });
    }
})(jQuery);