/**
 * HTML TEMPLATE
 * <div class="tagbox ">
 *     <input class="invisible-input" type="text" name="tagbox" value="" />
 * </div>
 * 
 */

var tagbox = {};
tagbox.resize_input;
tagbox.delete_event = function (e) {
    var $t = $(e.target);
    txt = $.trim($t.text());
    input = $t.siblings("input").eq(0);

    $t.remove();
    delete $(input).data("tmap")[txt];
    if (Object.keys($(input).data("tmap")).length === 0) {
        $(input).width("100%");
        $(input).attr("placeholder", "Tags (e.g., albert einstein, flying pig, mashup)");
    } else {

        tagbox.resize_input($input);
    }
}
tagbox.split_event = function (e) {
    var $t = $(e.target),
            tmap = $t.data("tmap") || {},
            n = e.keyCode || e.which,
            val = $.trim($t.val());
    $t.data("tmap", tmap);
    if ($.inArray(n, [9, 13]) !== -1) {//tab, enter
        if (val !== "" && tmap[val] === undefined) {
            tmap[val] = 1;

            tagbox.create_tag_chip(this, val);

        }
        e.preventDefault();
    } else if (n === 8 && val === "" && tmap !== {}) {
        $t.prev(".tag").click();
    }
};
tagbox.create_tag_chip = function (container, tag) {
    var $input = $(container).children("input");
    var tag_element = $("<span>").addClass("tag").html(tag);
    oldHeight = $(container).height();

    $input.before(tag_element);
    $input.val("");
    $input.attr("placeholder", "");


    tagbox.resize_input($input);

};
tagbox.tagbox = function (selector) {
    tagbox.resize_input = function ($input) {
        
        $chips = $input.siblings(".tag");
        chips_width = 0;
        parent_width = $input.parent().width();
        $chips.each(function () {
            chips_width += ($(this).outerWidth(true));
        });
        
        new_width = parent_width - chips_width-20;
        if (new_width <= (30/100*parent_width))
            new_width = "100%";
        $input.width(new_width);
    }
    $(window).resize(function () {
        $(selector).each(function(){
            tagbox.resize_input($(this));
        });
        
    });
    $(selector).click(function () {
        $(this).children("input").focus()
    });
    $(selector).keydown(tagbox.split_event);
    $(selector).on("blur", "input", function () {
        var $t = $(this),
                tmap = $t.data("tmap") || {},
                n = this.keyCode || this.which,
                val = $.trim($t.val());
        $t.data("tmap", tmap);
        if (val !== "" && tmap[val] === undefined) {
            tmap[val] = 1;

            tagbox.create_tag_chip($(this).parent(), val);

        }
    });
    $(selector).on("click", ".tag", tagbox.delete_event);
    $(selector).each(function () {
        $input = $(this).children("input");
        $input.attr("placeholder", "Tags (e.g., albert einstein, flying pig, mashup)");
        $input.width('100%');
        $input.init.prototype.tagvalue = function (newtags) {
            $this = $(this);
            if (!newtags) {//get
                var data = $(this).data("tmap");
                return !data ? null : Object.keys(data).join(",");
            }
            //set
            $this.siblings(".tag").click();
            words = String(newtags).split(",");
            if (words.length == 1 && words[0] == "")
                return undefined;
            $.each(words, function (key, value) {
                tmap = $this.data("tmap") || {},
                        $this.data("tmap", tmap);
                if (tmap[value] === undefined) {
                    tmap[value] = 1;
                    tagbox.create_tag_chip($this.parent(), value);
                }
            });
        };
        $input.tagvalue($input.val());
    });
};