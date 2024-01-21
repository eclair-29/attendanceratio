let result = new DataTable("#result");

$("#result").wrap("<div class='table-responsive'></div>");
$("#result_length").wrap(
    "<div class='table-top-controls d-flex align-items-center justify-content-between'></div>"
);
$("#result_filter").detach().appendTo(".table-top-controls");
