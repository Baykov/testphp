$(document).ready(function() {

    loadDataToTable();

    function loadDataToTable() {

        var table = $('#cases').DataTable( {
            "order": [[ 1, "desc" ]],
            "info": true,
            "retrieve": true,
            "stateSave": true,
            "searching": true,
            "processing": true,
            "ajax": {
                "url": "/api/index.php",
                "type": "POST",
                "data": {"task":"users"}
            },
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": "territory" },
                { "data": "id",
                  "orderable": false,
                  "render": function ( data, type, full, meta ) {
                                return  '<a href="edit.html?id=' +
                                            data +
                                        '" class="btn btn-default btn-xs">&nbsp;e&nbsp;</a> ' +
                                        '<a item_id="' + data + '" class="btn btn-danger btn-xs delete">&nbsp;x&nbsp;</a>'
                            }
                },
            ]
        });

    }

    $('body').on('click', 'a.delete', function() {
        if (confirm('Удалить пользователя?')) {
            var data = { id: $(this).attr('item_id') };
            $.ajax({
                type: 'post',
                url: '/api/index.php',
                dataType: 'json',
                async: false,
                data: {'task':'deleteuser', 'params':data},
                statusCode: {
                    200: function(response) {
                        if (response.status == 200) location.reload();
                    }
                }
            });
        }
    });
});
