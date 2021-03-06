$(document).ready(function() {
    // when input of type text is focused, change the color of the label
    $("body").on("focus", "input", function () {
        if ($(this).attr("type") == "text") {
            var $labelParent = $(this).parents(".form-group").first();
            var $label = $labelParent.find("label").first();
            $label.addClass("focused");
        }
    });

    // when input is blurred change to normal color
    $("body").on("blur", "input", function () {
        $(this).parents(".form-group").first().find("label").removeClass("focused");
    });

    // when input of type textarea is focused, change the color of the label
    $("body").on("focus", "textarea", function () {
        var $labelParent = $(this).parents(".form-group").first();
        var $label = $labelParent.find("label").first();
        $label.addClass("focused")
    });

    // when input is blurred change to normal color
    $("body").on("blur", "textarea", function () {
        $(this).parents(".form-group").first().find("label").removeClass("focused");
    });
});
