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

function tableDrawCallback(settings) {
    this.api()
        .columns([1, 2, 3, 4])
        .every(function (d) {
            let col = this;

            let cell = $("#filters th").eq($(col.column(d).header()).index());

            // Create select element
            const theadLabel = $("#result th").eq([d]).text();

            let select = $(
                "<select class='form-select result-filter'></select>"
            );
            select.append(new Option(theadLabel));
            // $(col.footer()).empty().append(select);
            $(cell).html(select);

            // Apply listener for user change in value
            select.on("change", function () {
                const val = $.fn.dataTable.util.escapeRegex(select.val());
                col.search(val && "^" + val + "$", true, "").draw();
                // $(`#${selectId} option[value="${val}"]`).attr("selected", true);
            });

            // Add list of options
            col.data()
                .unique()
                .sort()
                .each(function (d, j) {
                    // select.append(new Option(d));
                    if (col.search() === "^" + d + "$") {
                        select.append(
                            '<option value="' +
                                d +
                                '" selected="selected">' +
                                d +
                                "</option>"
                        );
                    } else {
                        select.append(
                            '<option value="' + d + '">' + d + "</option>"
                        );
                    }
                });
        });
}

function roundOffCols() {
    return [
        {
            render: function (data, type, row) {
                return data !== 0 ? data.toFixed(2) : 0;
            },
            targets: [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
        },
    ];
}

let result = new DataTable("#result", {
    data: [],
    pageLength: 25,
    columns: cols,
    drawCallback: tableDrawCallback,
    orderCellsTop: true,
    columnDefs: roundOffCols(),
    fixedColumns: {
        left: 1,
    },
});

const entries = $("#result_length");
const tableTopControls =
    "<div class='table-top-controls d-flex align-items-center justify-content-between'></div>";
const search = $("#result_filter");
const tableTopRightControls =
    "<div class='table-top-right-controls d-flex align-items-center'></div>";
const seriesSelect = $("#series");
const clearFilterBtn = $("#clear_filter_btn");
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
            clearFilterBtn.attr("disabled", false);
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

clearFilterBtn.on("click", function () {
    result.search("");
    result.columns().search("").draw();
});
