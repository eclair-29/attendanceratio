function clearProgress(id) {
    id.children(".progress-bar").attr("style", `width: 100%`);
    id.children(".progress-bar").text("100%");
}

function setProgress(id, percentage) {
    id.attr("hidden", false);
    id.children(".progress-bar").attr("style", `width: ${percentage}%`);
    id.children(".progress-bar").text(`${percentage}%`);
}

function setFinished(id, percentage = 100) {
    id.children(".progress-bar").attr("style", `width: ${percentage}%`);
    id.children(".progress-bar").text(`${percentage}%`);
}

function setCleared(interval) {
    clearInterval(interval);
}

function clearBatchTables(type) {
    $.ajax({
        url: `${baseUrl}/clear?type=${type}`,
        type: "GET",
        success: function (response) {
            console.log(response);
        },
        error: function (error) {
            console.log(error);
        },
    });
}

function setProgressError(id, error, type) {
    id.children(".progress-bar").attr("class", `progress-bar bg-danger`);

    setTimeout(() => {
        if (!alert("Missing File Fields: " + error)) {
            clearBatchTables(type);
            location.reload();
        }
    }, 500);
}

const currentPath = window.location.pathname;

function callAjax(type, interval, id) {
    const isAuthRoutes =
        currentPath.includes("login") ||
        currentPath.includes("register") ||
        currentPath.includes("approval") ||
        currentPath.includes("feedback") ||
        currentPath.includes("export/division");
    if (!isAuthRoutes) {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/progress?type=${type}`,
            success: function (response) {
                if (response) {
                    let totalJobs = parseInt(response.total_batch_count);
                    let pendingJobs = parseInt(response.current_batch_count);
                    let finishedJobs = totalJobs - pendingJobs;
                    let percentage = 0;

                    if (response.error) {
                        setProgressError(id, response.error, type);
                    }

                    if (pendingJobs === 0) {
                        percentage = 100;
                        setFinished(id, percentage);
                    } else {
                        percentage = parseInt(
                            (finishedJobs / totalJobs) * 100
                        ).toFixed(0);
                        pendingJobs < 0
                            ? (percentage = 100)
                            : (percentage = percentage);
                        setProgress(id, percentage);
                    }

                    if (parseInt(percentage) >= 100) {
                        percentage = 0;
                        setTimeout(() => {
                            id.attr("hidden", true);
                            if (!alert("File successfully uploaded"))
                                location.reload();
                        }, 1000);

                        setCleared(interval);
                        clearBatchTables(type);
                    }
                } else {
                    setCleared(interval);
                }
            },
            error: function (error) {
                // alert("Error uploading file. Please contact ISD for support.");
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
