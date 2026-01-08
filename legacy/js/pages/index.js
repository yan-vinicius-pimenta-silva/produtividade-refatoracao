
$(document).ready(function(){

    //order by column 0
    $("#data-table-no-extension").DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order": [[0, "asc"]],
        "stateSave": true
    });

    //order by column 1
    $("#data-table-no-extension-1").DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order": [[1, "asc"]],
        "stateSave": true
    });
    
    //order by column 0
    $("#data-table-no-extension-2").DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order": [[0, "asc"]],
        "stateSave": true
    });


    $("#data-table").DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        dom: "Bfrtip",
        buttons: [
            {
                extend: "print",
                text: "Imprimir todos"
            },
            {
                extend: "print",
                text: "Imprimir selecionados",
                exportOptions: {
                    modifier: {
                        selected: true
                    }
                }
            }
        ],
        select: true,
        "order": [[1, "asc"]],
        "stateSave": true
    });


    $("#data-table2").DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        dom: "Bfrtip",
        buttons: [
            {
                extend: "print",
                text: "Imprimir todos"
            },
            {
                extend: "print",
                text: "Imprimir selecionados",
                exportOptions: {
                    modifier: {
                        selected: true
                    }
                }
            }
        ],
        select: true,
        "order": [[1, "asc"]],
        "stateSave": true
    });

});

$(function () {
    //Widgets count
    $('.count-to').countTo();

    //Sales count to
    $('.sales-count-to').countTo({
        formatter: function (value, options) {
            return '$' + value.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, ' ').replace('.', ',');
        }
    });

    initSparkline();
});


function initSparkline() {
    $(".sparkline").each(function () {
        var $this = $(this);
        $this.sparkline('html', $this.data());
    });
}


var data = [], totalPoints = 110;
function getRandomData() {
    if (data.length > 0) data = data.slice(1);

    while (data.length < totalPoints) {
        var prev = data.length > 0 ? data[data.length - 1] : 50, y = prev + Math.random() * 10 - 5;
        if (y < 0) { y = 0; } else if (y > 100) { y = 100; }

        data.push(y);
    }

    var res = [];
    for (var i = 0; i < data.length; ++i) {
        res.push([i, data[i]]);
    }

    return res;
}