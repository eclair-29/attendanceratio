const notifBtn = $("#init_notif_btn");
const notifAlert = $("#notif_alert");

function toQueryString(value) {
    return value.replace(/\s/g, "%20");
}

function setNotifBtnBehavior(text, disabled) {
    notifBtn.text(text);
    notifBtn.attr("disabled", disabled);
}

function getAlert(type, text) {
    notifAlert.html(`<div class='alert alert-${type}'>${text}</div>`);
}

notifBtn.on("click", function () {
    const subject = "For Initial Approval";
    const notifMsg = "Please check your attendance ratio. Thank you.";
    const series = $("#series").val();

    setNotifBtnBehavior("Sending...", true);

    $.ajax({
        url: `${baseUrl}/sendnotification?subject=${toQueryString(
            subject
        )}&notifMsg=${toQueryString(notifMsg)}&seriesid=${series}`,
        type: "GET",
        success: function (data) {
            setNotifBtnBehavior("Send Initial Mail", false);
            alert(data);
        },
        error: function (error) {
            setNotifBtnBehavior("Send Initial Mail", false);
            alert("Error sending notification");
        },
    });
});