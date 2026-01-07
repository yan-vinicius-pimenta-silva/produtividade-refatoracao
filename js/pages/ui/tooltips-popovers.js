$(function () {
    //Tooltip
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        html: true
    });

    //Popover
    $('[data-toggle="popover"]').popover({
    	html: true
    });
})