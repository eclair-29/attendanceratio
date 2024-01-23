const cols = [
    { data: "staff_code" },
    { data: "division" },
    { data: "dept" },
    { data: "section" },
    { data: "entity" },
    { data: "attendance_ratio" },
    { data: "absent_ratio" },
    { data: "total_sl" },
    { data: "total_vl" },
    { data: "total_lwop" },
    { data: "total_late" },
    { data: "total_early_exit" },
    { data: "sl_percentage" },
    { data: "vl_percentage" },
    { data: "lwop_percentage" },
    { data: "late_percentage" },
    { data: "early_exit_percentage" },
];

let result = new DataTable("#result", {
    data: [],
    pageLength: 25,
    columns: cols,
});

const entries = $("#result_length");
const tableTopControls =
    "<div class='table-top-controls d-flex align-items-center justify-content-between'></div>";
const search = $("#result_filter");
const tableTopRightControls =
    "<div class='table-top-right-controls d-flex align-items-center'></div>";
const seriesSelect = $("#series");
const exportBtn = $("#export_series_btn");
const initNotifBtn = $("#init_notif_btn");

$("#result").wrap("<div class='table-responsive'></div>");
entries.wrap(tableTopControls);
search.detach().appendTo(".table-top-controls");
search.wrap(tableTopRightControls);

function getRatioBySeries(id) {
    $.ajax({
        url: `${baseUrl}/ratiobyseries?series_id=${id}`,
        type: "GET",
        success: function (response) {
            result.clear().draw();
            result.rows.add(response.data).draw();
            exportBtn.attr("class", "btn btn-outline-dark");
            exportBtn.attr(
                "href",
                `${baseUrl}/export?series_id=${seriesSelect.val()}`
            );
            initNotifBtn.attr("disabled", false);
        },
        error: function (error) {
            alert("Error fetching data");
        },
    });
}

seriesSelect.on("change", function () {
    getRatioBySeries($(this).val());
});
