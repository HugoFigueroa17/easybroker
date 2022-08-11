$(document).ready(function (e) {


    const tblDatos = $("#tblDatos").DataTable({
        destroy: true,
        lengthMenu: [[10, 25, 50,100,500, -1], [10, 25, 50,100,500, "Todos"]],
        fixedHeader: true,
        scrollY: "400px",
        scrollCollapse: true,
        scrollX:        true,
        paging:         false,
        responsive:true,
        stateSave:false,
        //dom: 'Blfrtip',
        dom:'<"row"<"col-md-4"l><"col-md-4 text-center"f><"col-md-4 cls-export-buttons"B>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Datos',
                text: '<i class="fa fa-file-excel-o"></i>&nbsp;Excel',
                titleAttr: "Exportar a excel",
                className: "btn btn-outline-success",
                autoFilter: true,
                exportOptions: {
                    columns: [ 0, ':visible' ],
                },
            },
            {
                extend: 'pdfHtml5',
                title: 'Datos',
                text: '<i class="fa fa-file-pdf-o"></i>&nbsp;PDF',
                titleAttr: "Exportar a PDF",
                className: "btn btn-outline-danger",
                orientation: 'landscape',
                pageSize: 'LETTER',
                exportOptions: {
                    columns: [ 0, ':visible' ]
                }
            },
            {
                extend: 'colvis',
                text: 'Columnas',
            }
        ],
        language: {
            paginate: {
                previous: "<i style='color: #000000;font-size: 26px' class='iconsminds-arrow-left-in-circle'></i>",
                next: "<i style='color: #000000;font-size: 26px' class='iconsminds-arrow-right-in-circle'></i>"
            },
            search: "_INPUT_",
            searchPlaceholder: "Buscar...",
            lengthMenu: "Registros por página _MENU_",
            info:"Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty:"Mostrando 0 a 0 de 0 registros",
            zeroRecords: "No hay datos para mostrar",
            loadingRecords: "Cargando...",
            infoFiltered:"(filtrado de _MAX_ registros)",
            "processing": "Procesando...",

        },
        "order": [[ 0, "desc" ]],
        "processing":false
    });

    
});
