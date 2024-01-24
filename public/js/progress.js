function clearProgress(id) {
    id.children(".progress-bar").attr("style", `width: 100%`);
    id.children(".progress-bar").text("100%");
}

function setProgress(response, id) {
    let finishedJobs =
        response.total_batch_count - response.current_batch_count;
    let percentage = parseInt(
        (finishedJobs / response.total_batch_count) * 100
    ).toFixed(0);

    id.attr("hidden", false);
    id.children(".progress-bar").attr("style", `width: ${percentage}%`);
    id.children(".progress-bar").text(`${percentage}%`);

    if (percentage >= 100) alert("File successfully uploaded");
}

function setFinished(id) {
    id.children(".progress-bar").attr("style", `width: 100%`);
    id.children(".progress-bar").text("100%");
}

function setCleared(id, interval) {
    clearInterval(interval);
    id.attr("hidden", true);
}

const currentPath = window.location.pathname;

function callAjax(type, interval, id) {
    if (!currentPath.includes("login")) {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/progress?type=${type}`,
            success: function (response) {
                if (!response) {
                    setCleared(id, interval);
                    return;
                }

                if (response.current_batch_count <= 0) setFinished(id);
                else setProgress(response, id);
            },
            error: function (error) {
                alert("Error uploading file");
            },
        });
        return;
    }
}

const attendanceInterval = setInterval(trackAttendance, 1000);
const baseInterval = setInterval(trackBase, 1000);

function trackAttendance() {
    callAjax("attendance", attendanceInterval, $("#upload_progress"));
}

function trackBase() {
    callAjax("base", baseInterval, $("#base_upload_progress"));
}
