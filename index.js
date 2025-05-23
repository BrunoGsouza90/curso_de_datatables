document.addEventListener("DOMContentLoaded", () => {

    const table = new DataTable('#listar_usuarios', {

        ajax: 'listar_usuarios.php',
        processing: true,
        serverSide: true,
        language: {
    
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
    
        }
    
    })

    $('#listar_usuarios tbody').on('click', 'tr', function () {

        let tr = $(this);
        let row = table.row(tr);

        if (row.child.isShown()) {

            row.child.hide();
            tr.removeClass('shown');

        } else {

            let trs_verify = $("tr")

            for(let tr_verify of Object.entries(trs_verify)) {
    
                for(let td_verify of tr_verify) {
    
                    if(typeof(td_verify) == "object") {
    
                        if(td_verify.children[0] != undefined) {
    
                            if(td_verify.children[0].tagName != "TH") {
    
                                if(td_verify.classList.contains("shown")) {

                                    let row_verify = table.row(td_verify);

                                    row_verify.child.hide();
                                    td_verify.classList.remove("shown")

                                }
        
                            }
    
                        }
    
                    }
    
                }
    
            }

            row.child(format(row.data())).show();
            tr.addClass('shown');

        }

    })

    function format(data) {

        return `

            <div style="padding: 10px; background: #f9f9f9;">

                <strong>Detalhes do usuário:</strong><br>
                ID: ${data[0]}<br>
                Nome: ${data[1]}<br>
                Email: ${data[2]}<br>
                Função: ${data[3]}

            </div>
        `;

    }

})