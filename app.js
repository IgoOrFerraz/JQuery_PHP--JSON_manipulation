$(document).ready(function(){

    const textJson = {"pessoas" : []}
    
    $('#textJson').html(JSON.stringify(textJson, null, 4))

    let Refresh = () => {
        $('#textJson').html(JSON.stringify(textJson, null, 4))
        new Factory()

        $('.btn_addFilho').click(function(){
            let addFilhoId = parseInt($(this).attr('id').replace('addFilho', ''))
            let filhoNome = prompt("Digite o Nome do Filho")
            
            textJson.pessoas[addFilhoId].filhos.push(filhoNome)
            Refresh()
        })
        
        $('.btn_removerPai').click(function(){
            let idregister = parseInt($(this).attr('id').replace('remover', ''))
            textJson.pessoas.splice(idregister, 1)
            Refresh()
        })

        $('.btn_removerFilho').click(function(){
            let id = $(this).attr('id').replace('removerFilho', '').split('apud')
            
            let idregister = id[0]
            let idfilho = id[1]

            textJson.pessoas[idregister].filhos.splice(idfilho, 1)
            Refresh()
        })
    }

    $('#btn_incluirPai').click(function(){
        let register = new Register($('#text_incluir').val())
    
        textJson.pessoas.push(register)
        $('#text_incluir').val('')
        Refresh()
    })

    $('#btn_gravar').click(function(){
        let text = $('#textJson').val()
        
        $.ajax({
            url: "./main.php?acao=gravar",
            method: 'POST',
            data: {textJson: text},
            datatype: 'json',
            success: function(result){
                alert("gravado")
            },
            error: function(error){
                alert("Erros, "+error)
            }
        })
    })

    $('#btn_ler').click(function(){
        $.ajax({
            url: "./main.php?acao=ler",
            success: function(result){
                //alert(result)
                result != null ? textJson.pessoas = JSON.parse(result) : alert("Favor Insira um Registro")
                Refresh()
            },
            error: function(error){
                alert(error)
            }
        })
    })
})


class Register{
    constructor(nome, filhos=[]){
        this.nome = nome
        this.filhos = filhos
    }
}

class Factory{
    constructor(){
        $('#registers').html('')
        this.inserirRegistroViewPai()
    }

    inserirRegistroViewPai(){
        
        let registers = JSON.parse($('#textJson').val())
        
        registers.pessoas.map((value, index) => {
            $('#registers').append(`<div class='registro'>
                                        <div class='divPai'>
                                            ${registers.pessoas[index].nome}
                                        </div>
                                        
                                        <div class='divRemoverPai'>
                                            <input type='button' value='Remover' id='remover${index}' class='btn_removerPai'>
                                        </div>
                                    </div>

                                    <div id='registroFilhos${index}' class='registroFilhosGeral'></div>

                                    <div class='divAddFilho'>
                                        <input type='button' value='Adicionar Filho' id='addFilho${index}' class='btn_addFilho'>
                                    </div>`)
           
            if(registers.pessoas[index].filhos != '') this.inserirRegistroViewFilhos(registers, index);
        })
    }

    inserirRegistroViewFilhos(registers, idRegister){

        registers.pessoas[idRegister].filhos.map((value, index) => {
            $(`#registroFilhos${idRegister}`).append(`<div id='registroFilho${index}' class='registroFilho'>
                                                        <div class='registroFilhoNome'>
                                                            - ${value}
                                                        </div>
                                                        <div class='divRemoverFilho'>
                                                            <input type='button' value='Remover' id='removerFilho${idRegister}apud${index}' class='btn_removerFilho'>
                                                        </div>
                                                    </div>`)
        })
    }
}