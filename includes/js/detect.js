var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

$(document).ready(function(){
    $(".fotoBotao").removeClass('btn-block');
    $(".contatoBotao").removeClass('btn-block');
    $(".enderecoBotao").removeClass('btn-block');
    
    if (isMobile.any() != null) {
        $(".mobile").css('display', 'block');
        $(".fotoBotao").addClass('btn-block');
        $(".contatoBotao").addClass('btn-block');
        $(".enderecoBotao").addClass('btn-block');
    };
});