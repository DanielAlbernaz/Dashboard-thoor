function importarArquivo(){
    //event.preventDefault();



    var file_data = $('#arquivo').prop('files')[0];

    var form_data = new FormData();

    form_data.append('arquivo', file_data);

    Swal.fire({
        title: 'Aguarde que estamos importando seu arquivo...',
        willOpen: () => {
            Swal.showLoading()
          },
      })


    jQuery.ajax({
        url: "xml",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function( data )
        {
            if(data.situacao == 'success'){
                console.log(data.result);
                $('#frmImportar input').val("");
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: data.msg,
                        showConfirmButton: false,
                        timer: 1500
                    })
            }
            if(data.situacao == 'errorCnpj'){
                $('#errorCnpj').show();
                $('#errorCnpj').fadeOut(6000);
            }
            if(data.situacao == 'errorUsuario'){
                $('#errorUsuario').show();
                $('#errorUsuario').fadeOut(6000);
            }
        }
    });
}




function filtrar(){
    //event.preventDefault();

    //console.log($('#frmFiltrar').serialize());

    jQuery.ajax({
        url: "filtrar_graficos",
        type: "POST",
        data: $('#frmFiltrar').serialize(),
        success: function( data )
        {
            if(data.situacao == 'success'){
                console.log(data.motorista);
                location.reload();
                // $('#frmImportar input').val("");
                //     Swal.fire({
                //         position: 'top-end',
                //         icon: 'success',
                //         title: data.msg,
                //         showConfirmButton: false,
                //         timer: 1500
                //     })
            }
            if(data.situacao == 'warning'){
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...  Nenhum resultado foi encontrado nesse período de tempo!',
                  })
            }
            if(data.situacao == 'erro'){
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...  Selecione Motorista ou Placa!',
                  })
            }
            if(data.situacao == 'errorUsuario'){
                $('#errorUsuario').show();
                $('#errorUsuario').fadeOut(6000);
            }
        }
    });
}

// function limparFiltro(){
//     //document.getElementById('#blocoPlaca > span > span.selection > span > ul > li:nth-child(1)').empty
//    // removeOptions(document.getElementById('select2-selection__rendered').empty);
//    console.log();
//    console.log($('#blocoPlaca > span > span.selection > span > ul > li:nth-child(1)').remove());
//    console.log($('#blocoMotorista > span > span.selection > span > ul > li:nth-child(1)').remove());
// }

// function removeOptions(selectElement) {
//     console.log(selectElement.empty());
//     var i, L = selectElement.options.length - 1;
//     for(i = L; i >= 0; i--) {
//        selectElement.remove(i);
//     }
//  }

function limparFiltro(){
   
    var elementos = $('#motorista').html("");
    
    elementos.empty();
    console.log(elementos);

    
}

// function removeOptions(selectElement) {
//     console.log(selectElement.empty());
//     var i, L = selectElement.options.length - 1;
//     for(i = L; i >= 0; i--) {
//        selectElement.remove(i);
//     }


function cadastrarUsuario(){
    //event.preventDefault();

    console.log($('#frmCadUsuario').serialize());

    jQuery.ajax({
        url: "cadastrar_usuario",
        type: "POST",
        data: $('#frmCadUsuario').serialize(),
        success: function( data )
        {
            if(data.situacao == 'success'){

                $('#frmCadUsuario input').val("");
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: data.msg,
                        showConfirmButton: false,
                        timer: 1500
                    })
                    location.reload();
            }
            if(data.situacao == 'warning'){
                Swal.fire({
                    icon: 'warning',
                    title: data.msg,
                  })
            }
        }
    });
}



function deletarUser(id){

    Swal.fire({
        title: 'Tem certeza que deseja excluir?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim',
        cancelButtonText: 'não'
      }).then((result) => {
        if (result.isConfirmed) {

          jQuery.ajax({
            url: "deletar_usuario",
            type: "POST",
            data: {'id':id},
            success: function( data )
            {
                if(data.situacao == 'success'){

                    $('#frmCadUsuario input').val("");
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: data.msg,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        location.reload();
                }
                if(data.situacao == 'warning'){
                    Swal.fire({
                        icon: 'warning',
                        title: data.msg,
                      })
                }
            }
        });
        }
      })

}

function alterarUser(id){

    jQuery.ajax({
    url: "show_editar_usuario",
    type: "POST",
    data: {'id':id},
    success: function( data )
        {
            if(data.situacao == 'success'){

                // $('#frmCadUsuario input').val("");
                //     Swal.fire({
                //         position: 'top-end',
                //         icon: 'success',
                //         title: data.msg,
                //         showConfirmButton: false,
                //         timer: 1500
                //     })
                location.reload();
            }
            if(data.situacao == 'warning'){
                Swal.fire({
                    icon: 'warning',
                    title: data.msg,
                    })
            }
        }
    });
}


function editarUsuario(){
    //event.preventDefault();

    console.log($('#frmCadUsuario').serialize());

    jQuery.ajax({
        url: "editar_usuario",
        type: "POST",
        data: $('#frmCadUsuario').serialize(),
        success: function( data )
        {
            if(data.situacao == 'success'){

                $('#frmCadUsuario input').val("");
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: data.msg,
                        showConfirmButton: false,
                        timer: 1500
                    })
                    location.reload();
            }
            if(data.situacao == 'warning'){
                Swal.fire({
                    icon: 'warning',
                    title: data.msg,
                  })
            }
        }
    });
}

function logar(){
//    event.preventDefault();

    jQuery.ajax({
        url: "logar",
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: $('#frmLogarUsuario').serialize(),
        success: function( data )
        {
            if(data.situacao == 'success'){

                $('#frmLogarUsuario input').val("");
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: data.msg,
                        showConfirmButton: false,
                        timer: 1500
                    })
                    window.location = 'home';
            }
            if(data.situacao == 'warning'){
                Swal.fire({
                    icon: 'warning',
                    title: data.msg,
                  })
            }
        }
    });
}
