let result = new DataTable("#result");
const entries = $("#result_length");
const tableTopControls =
    "<div class='table-top-controls d-flex align-items-center justify-content-between'></div>";
const initNotifBtn =
    '<button class="btn btn-outline-danger" id="init_notif_btn">Send Initial Notif</button>';
const search = $("#result_filter");
const tableTopRightControls =
    "<div class='table-top-right-controls d-flex align-items-center'></div>";

$("#result").wrap("<div class='table-responsive'></div>");
entries.wrap(tableTopControls);
search.detach().appendTo(".table-top-controls");
search.wrap(tableTopRightControls);

search.attr("style", "padding-right: 15px;");

$(".table-top-right-controls").append(initNotifBtn);
