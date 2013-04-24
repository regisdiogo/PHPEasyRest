var per = {};

per.navClick = function() {
    $('.nav-link').click(function(event) {
        history.pushState({ path: $(this).attr('href') }, '', $(this).attr('href'));
        $('body').addClass('historypushed');
        return per.slideToContent($(this).attr('href'));
    });
};

per.slideToContent = function(url) {
    $('.nav-link').removeClass('selected');
    $('.nav-link').each(function() {
        if ($(this).attr('href') == url) {
            $(this).addClass('selected');
        }
    });
    $.ajax({
        type: 'GET',
        url: 'ajax/' + url,
        cache: false,
        success: function(data) {
            var width = parseInt($('#main-content').css('width'));
            var transfer = $('<div class="transfer"></div>').css({ 'width': (2 * width) + 'px' });
            var current = $('<div class="current"></div>').css({ 'width': width + 'px', 'left': '0', 'float': 'left' }).html($('#main-content').html());
            var next = $('<div class="next"></div>').css({ 'width': width + 'px', 'left': width + 'px', 'float': 'left' }).html(data);
            transfer.append(current).append(next);
            $('#main-content').html('').append(transfer);
            transfer.animate({ 'margin-left': '-' + width + 'px' }, 300, function () {
                $('#main-content').html(data).find('.btn-test-1').each(function() {
                    per.startupTestWindow();
                });
            });
        }
    });
    return false;
}

per.bindTestClick = function(element) {
    element.on('click', function() {
        $.ajax({
            type : element.parent('form').attr('method'),
            url : element.parent('form').attr("action"),
            data : element.parent('form').serialize(),
            success : function(data) {
                var result = 'Json response: <br />';
                jQuery.each(data, function(key, value) {
                    result += key + ': ' + value + '<br />';
                });
                element.parent('form').children('.result').html(result);
            }
        });
    });
}

per.startupTestWindow = function() {
    per.bindTestClick($('.btn-test-1'));
    $('.show-source-code').on('click', function() {
       $(this).parent().siblings('.source-code').slideDown();
       $(this).siblings('.hide-source-code').show();
       $(this).hide();
    });
    $('.hide-source-code').on('click', function() {
        $(this).parent().siblings('.source-code').slideUp();
        $(this).siblings('.show-source-code').show();
        $(this).hide();
     });
}

if (typeof String.endsWith !== 'function') {
    String.prototype.endsWith = function (suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}

$(document).ready(function() {
    per.navClick();
    per.startupTestWindow();
});

$(window).bind('popstate', function(e) {
    if ($('body').hasClass('historypushed')) {
        $('.nav-link').each(function() {
            if (location.pathname.endsWith($(this).attr('href'))) {
                per.slideToContent($(this).attr('href'));
            }
        });
    }
});