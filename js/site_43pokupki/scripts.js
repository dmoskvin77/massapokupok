var menuMode = 0;
var showMobileMenu = false;

$().ready(function(){

    $('[data-toggle="tooltip"]').tooltip();

    $('.loginbutton').click(function(e)
    {
        e.preventDefault();
        $('#loginModal').modal({
            show: true,
            backdrop: 'static',
            keyboard: true
        });

    });

    $('.registerbutton').click(function(e)
    {
        e.preventDefault();
        $('#registerModal').modal({
            show: true,
            backdrop: 'static',
            keyboard: true
        });

    });

    // удаление заказанного товара из корзины
    $('.mpbtnremove').click(function(e)
    {
        var gotOrderId = $(this).attr("id");
        // чистый id
        var pureOrderId = gotOrderId.split("ordlstitem-").join("");

        window.location.href = "/index.php?do=userremoveorder&oid="+pureOrderId;
    });

    // оплата заказа из корзины
    $('.basketbtnopl').click(function(e)
    {
        var gotOrderId = $(this).attr("id");
        // чистый id
        var pureOrderId = gotOrderId.split("paybasketbtn-").join("");

        if ($(this).html() == 'Обновите страницу!')
            window.location.href = "/index.php?show=baskethead";
        else
            window.location.href = "/index.php?show=userpay&oheadid="+pureOrderId;

    });

    // орг подтверждает оплату
    $('.orgpayconfirmbtn').click(function(e)
    {
        var gotPayId = $(this).attr("id");
        // чистый id
        var purePayId = gotPayId.split("orgpayconfirmbtn-").join("");

        window.location.href = "/index.php?do=orgpayconfirm&payid="+purePayId;
    });

    // орг отклоняет оплату
    $('.orgpaydeclinebtn').click(function(e)
    {
        var gotPayId = $(this).attr("id");
        // чистый id
        var purePayId = gotPayId.split("orgpaydeclinebtn-").join("");

        window.location.href = "/index.php?do=orgpaydecline&payid="+purePayId;
    });

    // редактирование заказа
    $('.ordedititem').click(function(e)
    {
        var gotPayId = $(this).attr("id");
        // чистый id
        var purePayId = gotPayId.split("ordedititem-").join("");

        window.location.href = "/index.php?show=useraddorder&orderid="+purePayId;
    });

    $('.back-top').click(function() {
        $('body,html').animate({
            scrollTop: 0
        }, 200);
        return false;
    });

    menuResize();

});

$(window).resize(function() {
    menuResize();
});

function toggleMainMenu()
{
    if (showMobileMenu) {
        $('.mp-topline-menu').hide();
        $('#mp-top-menu').css("z-index", "0");
        showMobileMenu = false;
    }
    else {
        $('.mp-topline-menu').show();
        $('#mp-top-menu').css("z-index", "1");
        showMobileMenu = true;
    }
}

function menuResize()
{
    // какова ширина экрана?
    var winWidth = $(window).width();

    console.log('winWidth: '+winWidth);

    if (winWidth >= 990 && menuMode != 1)
    {
        menuMode = 1;

        if ($('.reserveddiv').html() != '')
            $('.navbar-header').html($('.reserveddiv').html());

        $('#leftCol').css("width", "22%");
        $('.onedlgli').css("float", "none");
        $('.onedlgli').css("margin-right", "0");

        $('.navbar-brand img').show();
        $('.navbar-form').show();
        $('#categorymenu').show();
        $('.vkcontainer').show();
        $('.live-counter').show();

        $('.mp-topline-menu').show();
        $('.basket-zakupka-pic').show();
    }

    if (winWidth < 990 && winWidth >= 760 && menuMode != 2)
    {
        menuMode = 2;

        if ($('.reserveddiv').html() != '')
            $('.navbar-header').html($('.reserveddiv').html());

        // лого лучше заменить на уменьшенный вариант
        $('.navbar-brand img').show();
        $('.navbar-form').hide();
        $('#categorymenu').hide();
        $('.vkcontainer').hide();
        $('.live-counter').show();

        $('.mp-topline-menu').show();
        $('.basket-zakupka-pic').show();

        // список собеседников
        $('#leftCol').css("width", "100%");
        $('.onedlgli').css("float", "left");
        $('.onedlgli').css("margin-right", "12px");

    }

    if (winWidth < 760 && menuMode != 3)
    {
        menuMode = 3;

        $('.navbar-brand img').show();
        $('.navbar-form').hide();
        $('#categorymenu').hide();
        $('.vkcontainer').hide();
        $('.live-counter').hide();

        // надо свернуть менюшки за общую кнопку-разворачивалку
        if ($('.reserveddiv').html() == '')
        {
            $('.reserveddiv').html($('.navbar-header').html());
        }

        $('.mp-topline-menu').hide();
        $('.basket-zakupka-pic').hide();

        // список собеседников
        $('#leftCol').css("width", "100%");
        $('.onedlgli').css("float", "left");
        $('.onedlgli').css("margin-right", "12px");

        // ставим кнопку-сокращалку
        $('.navbar-header').html('<button type="button" class="btn btn-default btn-openmenu" onclick="toggleMainMenu();"><span class="glyphicon glyphicon-align-justify"></span></button>');
    }

}