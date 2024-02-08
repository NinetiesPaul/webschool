function desativarUsuario(id, tipo) {
    $.ajax({
        url: tipo + '/desativar',
        data: { id: id },
        type: "PUT",
        success: function(data){
            data = jQuery.parseJSON(data)
            if (data.error === true) {
                $('.alert')
                    .css('display', 'block')
                    .text(data.msg)
                    .delay(5000).fadeOut(1000, function(){
                        $('.alert').css('display', 'none');
                    });
            } else {
                if ($('.label-status_' + id).hasClass('label-success') == true) {
                    $('.label-status_' + id)
                        .removeClass('label-success')
                        .addClass('label-danger')
                        .text('Inativo');
                } else {
                    $('.label-status_' + id)
                        .removeClass('label-danger')
                        .addClass('label-success')
                        .text('Ativo');
                }
            }
        }
    });
}
