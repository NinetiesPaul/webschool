function verificarLogin(val, tipo, id = null) {
    $.ajax({
        type: "POST",
        url: "/verificarLogin",
        data: { login: val, tipo: tipo, id: id },
        success: function(data){
            data = jQuery.parseJSON(data)
            if (data.loginTaken === true){
                $("#btn").attr("disabled", true)
                $("#disponibilidade").slideDown("slow", function(){});
            } else {
                $("#btn").attr("disabled", false)
                $("#disponibilidade").slideUp("slow", function(){});
            }
        }
    });
}
