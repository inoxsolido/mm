/* 
 * This javascript file contain javascript of 
 * search-main 
 * search-box 
 * search content
 */
$(window).load(function () {

    /************* MAIN **************/
    function setCookie(cname, cvalue, exdays) {
        let d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        let cookies = document.cookie;
        let pattern = /(\w+)=(\w+)/;
        let m = '';
        while ((m = pattern.exec(cookies)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === pattern.lastIndex) {
                pattern.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            if (m[1] === cname) {
                return m[2];
            }
        }
        return '';
    }
    function getQuery(variable)
    {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable) {
                return decodeURI(pair[1]);
            }
        }
        return '';
    }
    var main;
    /*********** END MAIN ************/
    /************* SEARCH CONTENTS **********/
    function changeView(event, view_name) {
        //clear state from all btn & add active state
        $(event).parent().children().removeClass("active");
        $(event).addClass("active");
        //switch current view to target view
        if (view_name === "t") {
            $(".content-list").hide();
            $(".content-table").show();
            setCookie('searchView', 't', 7);
        } else {
            $(".content-table").hide();
            $(".content-list").show();
            setCookie('searchView', 'l', 7);
        }
    }

    /*********** END SEARCH CONTENTS ********/
    /************* SEARCH BOX ***************/
    var queryValue = $("#query").val();
    var cache = {};
    $("#query").autocomplete({
        source: function (request, response) {
            let term = request.term;

            if (term in cache) {
                response(cache[ term ]);
                return;
            }
            $.ajax({
                url: window.url_suggest_word,
                type: "post",
                dataType: "json",
                data: {
                    query: $("#query").val()
                },
                success: function (data) {
                    queryValue = $("#query").val();
                    response(data);
                }
            });

        },
        minLength: 1,
        select: function (event, ui) {

        }
    });
    $("#btn-search-option").click(function () {
        $("#search-option").slideToggle();
    });
    
    $(".check-type").change(function(){
        if($(".check-type:checked").length === 0) $(".check-type").prop({checked:true});
    });
    $(".check-search").change(function(){
        if($(".check-search:checked").length === 0) $(".check-search").prop({checked:true});
    });
    
    function createQueryParameter(page, isPageRequest = true) {
        /* OBJECTS IN ARRAY
         * query => '',
         * mediaType[] => 0-5,
         * date => '2018-05-24 12:00 AM - 2018-05-24 11:59 PM',
         * onlyAlbum => 'only', */
        let old_query = getQuery('q');
        let form_var = $("#search").serializeArray();
        console.log(page);
        if (!page && page !== 0)
            page = 1;
        form_var.push({name: 'p', value: page});
        if(!isPageRequest){
            form_var.push({name: 'oq', value: old_query});
        }
        return form_var;
    }
    function updatePrevQuery(){
        let oq = getQuery('q');
        
    }
    $("#search").submit(function (event, page) {
        event.preventDefault();
        //catch submitting event 
        /*
         * 1. change url to abc.xyz/search/....
         * 2. send query information to server 
         * 3. display feedback on search content (the return infomation from server 
         * will be html content by view search content(render partial))
         */
        let isPageRequest = true;
        if (page === undefined){
            page = 1;
            isPageRequest = false;
        }
        let data = createQueryParameter(page - 1, isPageRequest);
        let form_var = createQueryParameter(page, isPageRequest);;
        let new_url = "?";
        if (form_var != '') {
            new_url = "?q=" + form_var[0].value;
            form_var.shift();
        }

        //ทำเป็น url เพื่อใช้ ในการquery แบบ get ได้
        $.each(form_var, function () {
            new_url += "&" + this.name + "=" + this.value;
        });
        window.history.pushState(null, null, "./" + new_url);

        //get new search_content and update it with new page

        $.ajax({
            url: this.action,
            method: 'POST', // "POST", "GET", "PUT" 
            async: true,
            data: data,
            beforeSend: function () {
                $("#loading").show();
            },
            success: function (data, textStatus, jqXHR) {
                $(".section-content").html(data);
                main();
                
            },
            error: function (jqXHR, textStatus, errorThrown) {},
            complete: function (jqXHR, textStatus) {
                $("#loading").hide();
            }
        });
        return false;
    });
    var queryCache = {};
    $("#query").bind("keydown", function() {
        console.log($(this).val());
        var regex = /^[a-zA-Z0-9ก-๙ ]+$/;
        if (regex.test($(this).val()) || $(this).val() == '') {
            queryCache[$(this).attr("id")] = $(this).val();
        }
        
    });
    $("#query").bind("keyup", function() {
        console.log($(this).val());
        var regex = /^[a-zA-Z0-9ก-๙ ]+$/;
        console.log((!regex.test($(this).val())));
        if (!(regex.test($(this).val()) || $(this).val() == '')) {
            $(this).val(queryCache[$(this).attr("id")]);
        }
        
    });
    /*********** END SEARCH BOX *************/



    /************** PAGINATION *************/
    function pagination(e) {
        e.preventDefault();

        let page = $(this).data('page') + 1;

        $("#search").trigger('submit', [page]);
        return false;
    }

    /************ END PAGINATION ***********/

    /***************** RUN *****************/
    main = function (takeUrl = false) {
        //copy url params to search field
        if (takeUrl) {
            $("#query").val(getQuery('q'));
            $('[name=v]').prop({checked:getQuery('v')==='1'?true:false});
            $('[name=i]').prop({checked:getQuery('i')==='1'?true:false});
            $('[name=a]').prop({checked:getQuery('a')==='1'?true:false});
            $('[name=d]').prop({checked:getQuery('d')==='1'?true:false});
            $('[name=e]').prop({checked:getQuery('e')==='1'?true:false});
            $('.check-type:first').change();
            $('[name=dr]').val(getQuery('dr')).change();
            $("#w0-container").find(".range-value").html($('[name=dr]').val());
            $('[name=omedianame]').prop({checked:getQuery('omedianame')==='1'?true:false});
            $('[name=omediatag]').prop({checked:getQuery('omediatag')==='1'?true:false});
            $('[name=oalbumname]').prop({checked:getQuery('oalbumname')==='1'?true:false});
            $('[name=oalbumtag]').prop({checked:getQuery('oalbumtag')==='1'?true:false});
            $('.check-search:first').change();
        }
        //clear old event & binding new
        $(".section-content").off();
        $(".section-content").on("click", ".btn-page", pagination);
        $(".section-content").on("click", "#btntable", function () {
            changeView(this, "t");
        });
        $(".section-content").on("click", "#btnlist", function () {
            changeView(this, "l");
        });
        let cSearchView = getCookie('searchView');
        if (cSearchView === 't') {
            $("#btntable").click();
        } else if (cSearchView === 'l') {
            $("#btnlist").click();
        } else {
            $("#btntable").click();
    }
    };
    main(true);
    /*************** END RUN **************/
});    