$(function () {
    $(document).on("click", "#upload-image", function (e) {
        e.preventDefault();
        
        swal("Do you want to create new album ?", {
            buttons: {
                yes: "New album.",
                exist: "Exist album."
            }
        })
        .then((value) => {
            switch (value) {

                case "yes":
                    window.location.href = $(this).attr('href')+'/new';
                    break;

                case "exist":
                    window.location.href = $(this).attr('href')+'/exist';
                    break;

            }
        });
    });
    $(document).on("click", "#error-messages, #success-message", function(){
        $(this).fadeOut('slow');
    });
    window.errorPopUp = function(data, clickToRemove = false){
        let $element = $("#error-messages");
        $element.prependTo(".content");
        $element.text(data);
        if(!clickToRemove)$element.stop().fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
        else $element.fadeIn().animate({opacity: 1.0}, 4000);
        
    };
    window.successPopUp = function(data, clickToRemove = false){
        let $element = $("#success-messages");
        $element.prependTo(".content");
        $element.text(data);
        if(!clickToRemove)$element.stop().fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
        else $element.fadeIn().animate({opacity: 1.0}, 4000);
    };
});

