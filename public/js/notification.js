const notifBtn = $("#init_notif_btn");
const alert = $("#notif_alert");

function toQueryString(value) {
    return value.replace(/\s/g, "%20");
}

function setNotifBtnBehavior(text, disabled) {
    notifBtn.text(text);
    notifBtn.attr("disabled", disabled);
}

function getAlert(type, text) {
    alert.html(`<div class='alert alert-${type}'>${text}</div>`);
}

notifBtn.on("click", function () {
    const subject = "For Initial Approval";
    const notifMsg = "Please check your attendance ratio. Thank you.";

    setNotifBtnBehavior("Sending...", true);

    $.ajax({
        url: `${baseUrl}/sendinitial?subject=${toQueryString(
            subject
        )}&notifMsg=${toQueryString(notifMsg)}`,
        type: "GET",
        success: function (data) {
            setNotifBtnBehavior("Send Initial Notif", false);
            getAlert("success", "Initial Notification sent successfully");
        },
        error: function (error) {
            setNotifBtnBehavior("Send Initial Notif", false);
            getAlert("danger", "Error sending notification");
        },
    });
});
