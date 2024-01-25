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

function setCleared(id, interval, type) {
    Toastify({
        text: "File successfully uploaded",
        duration: 5000,
    }).showToast();

    setTimeout(() => {
        id.attr("hidden", true);
    }, 2000);

    clearInterval(interval);
    $.ajax({
        url: `${baseUrl}/clearbatchtables?type=${type}`,
        type: "GET",
        success: function (response) {
            console.log(response);
        },
        error: function (error) {
            console.log(error);
        },
    });
}

const currentPath = window.location.pathname;

function callAjax(type, interval, id) {
    if (!currentPath.includes("login")) {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/progress?type=${type}`,
            success: function (response) {
                // if (!response) {
                //     setCleared(id, interval);
                //     return;
                // }
                // if (response.current_batch_count <= 0) setFinished(id);
                // else setProgress(response, id);

                if (response) {
                    let totalJobs = parseInt(response.total_batch_count);
                    let pendingJobs = parseInt(response.current_batch_count);
                    let finishedJobs = totalJobs - pendingJobs;
                    let percentage = 0;

                    if (pendingJobs === 0) {
                        percentage = 100;

                        setFinished(id, percentage);
                    } else {
                        percentage = parseInt(
                            (finishedJobs / totalJobs) * 100
                        ).toFixed(0);
                        setProgress(id, percentage);
                    }

                    if (parseInt(percentage) >= 100) {
                        percentage = 0;
                        setCleared(id, interval, type);
                    }
                }
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
