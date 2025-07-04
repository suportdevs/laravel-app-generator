const csrfToken = document.head.querySelector(
    'meta[name="csrf-token"]'
).content;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
    });

    $(document).on("change", "#check_all", function () {
        if ($(this).is(":checked")) {
            $("#check input[type='checkbox']").prop("checked", true);
            $("#check input[type='checkbox']")
                .closest("tr")
                .not("#r_checkAll")
                .addClass("table-primary");
            $("#Del_btn").removeAttr("disabled");
            $("#deleteMultiple").removeAttr("disabled");
            $("#updateMultiple").removeAttr("disabled");
            $("#updateAll").removeAttr("disabled");
            $("#btnConfirm").removeAttr("disabled");
            $("#btnReset").removeAttr("disabled");
            $("#btnDone").removeAttr("disabled");
        } else {
            $("#check input[type='checkbox']").prop("checked", false);
            $("#check tr").removeClass("table-primary");
            $("#Del_btn").attr("disabled", "disabled");
            $("#deleteMultiple").removeAttr("disabled");
            $("#updateMultiple").removeAttr("disabled");
            $("#updateAll").attr("disabled", "disabled");
            $("#btnConfirm").attr("disabled", "disabled");
            $("#btnReset").attr("disabled", "disabled");
            $("#btnDone").attr("disabled", "disabled");
        }
        // checkbox_count();
    });

    $(document).on("change", "#check input[type='checkbox']", function () {
        if (this.checked) {
            $(this).closest("tr").not("#r_checkAll").addClass("table-primary");
        } else {
            $(this).closest("tr").removeClass("table-primary");
        }

        if (
            $("#check input[type='checkbox']:checked").not("#check_all")
                .length > 0
        ) {
            $("#check_all").prop("checked", true);
            $("#Del_btn").prop("disabled", false);
            $("#deleteMultiple").prop("disabled", false);
            $("#updateMultiple").prop("disabled", false);
            $("#updateAll").prop("disabled", false);
            $("#btnConfirm").prop("disabled", false);
            $("#btnReset").prop("disabled", false);
            $("#btnDone").prop("disabled", false);
        } else {
            $("#check_all").prop("checked", false);
            $("#Del_btn").prop("disabled", true);
            $("#deleteMultiple").prop("disabled", true);
            $("#updateMultiple").prop("disabled", true);
            $("#updateAll").prop("disabled", true);
            $("#btnConfirm").prop("disabled", true);
            $("#btnReset").prop("disabled", true);
            $("#btnDone").prop("disabled", true);
        }
        // checkbox_count();
    });

    $(document).on("click", "#ajaxPaginate ul li a", function (event) {
        event.preventDefault();
        search($(this).attr("href"));
    });

    // delete records
    $(document).on("click", "#deleteMultiple, #Del_btn", function () {
        let _url = $("#frmList").attr("action");
        let _form = $("#frmList");
        // let _rc = confirm("Are you sure about this action? This cannot be undone!");
        // if (_rc === true) {
        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .post(_url, _form.serialize())
                    .then((response) => {
                        let result = response.data;
                        let res_type = typeof result;
                        if (response.data.success == true) {
                            toastr.success(response.data.message);
                        }
                        if (response.data.success == false) {
                            toastr.error(response.data.message);
                        }
                        if (res_type == "string") {
                            toastr.success(result);
                        } else if (res_type == "object") {
                            toastr.success(result.message, result.status);
                        } else if (res_type == "array") {
                            result.forEach(function (item) {
                                toastr.success(item);
                            });
                        }
                        console.log(response);
                        if (result.status === "success") {
                            Swal.fire("Deleted!", result.message, "success");
                            // Optionally refresh the page or remove the deleted row
                            $("#frmSearch").trigger("submit");
                        } else {
                            Swal.fire("Error!", result.message, "error");
                        }
                        $("#frmSearch").trigger("submit");
                    })
                    .catch((error) => {
                        console.log(
                            "🚀 ~ file: custom.js:1095 ~ axios.post ~ error",
                            error
                        );
                        toastr.error(error.data);
                        Swal.fire(
                            "Error!",
                            "An error occurred while deleting the tag.",
                            "error"
                        );
                    });
            }
        });
        // }
    });
    // ajax list search
    $(document).on("submit", "#frmSearch", function (event) {
        event.preventDefault();
        search(this.action);
    });
    $(".select2search").each(function () {
        $(this).select2({
            placeholder: $(this).data("placeholder"), // Dynamically fetch the placeholder
            allowClear: true, // Enable clear button
            theme: "bootstrap-5", // Use the Bootstrap 5 theme
            width: "100%", // Ensure responsive width
        });
    });
    $(document).on("select2:open", () => {
        document
            .querySelector(".select2-container--open .select2-search__field")
            .focus();
    });

    // Initialize the clock
    updateClock();
    setInterval(updateClock, 1000);
});

const showPreloader = () => {
    $(".loader-overlay").removeClass("d-none");
};
const hidePreloader = () => {
    $(".loader-overlay").addClass("d-none");
};

// ajax list search function
const search = (searchUrl) => {
    var formData = $("#frmSearch").serializeArray();
    formData.push({ name: "view_render", value: true });
    // Show the loader
    showPreloader();

    $.ajax({
        url: searchUrl,
        type: "GET",
        data: formData,
        success: function (data) {
            // Hide the loader
            hidePreloader();
            if (data && data.status === "error") {
                toastr.error(data.message || "An error occurred.");
            } else {
                console.log(data);
                $("#ajax_content").html(data);
            }
        },
        error: function (xhr, status) {
            // Hide the loader
            hidePreloader();
            toastr.error("There is some error. Try after some time.");
        },
    });
};

function singleDelete(deleteUrl) {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Perform the delete operation via AJAX
            $.ajax({
                url: deleteUrl,
                type: "DELETE",
                data: {
                    _token: csrfToken,
                },
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire("Deleted!", response.message, "success");
                        // Optionally refresh the page or remove the deleted row
                        $("#frmSearch").trigger("submit");
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                },
                error: function (xhr) {
                    Swal.fire(
                        "Error!",
                        "An error occurred while deleting the tag.",
                        "error"
                    );
                },
            });
        }
    });
}

function readURL(input, to) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#" + to).attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function showToast(toastData) {
    // Default toastData if none is provided
    if (!toastData) {
        toastData = {
            header: "Notification",
            icon: "bx-bell",
            message: "",
            type: "bg-primary",
            placement: "top-end",
            time: "Just now",
        };
    }

    // Update toast header text
    if (toastHeader) {
        toastHeader.textContent = toastData.header ?? "Notification";
    }

    // Update toast header icon class
    if (toastHeaderIcon) {
        toastHeaderIcon.className = `bx ${toastData.icon ?? "bx-bell"} me-2`;
    }

    // Update toast header time
    if (toastHeaderTime) {
        toastHeaderTime.textContent = toastData.time ?? "Just now";
    }

    // Update toast body content
    if (singleMessage) {
        singleMessage.textContent = toastData.message ?? "";
    }

    // Add toast classes for type and placement
    toastPlacementExample.classList.add(
        toastData.type ?? "bg-primary",
        ...toastData.placement.split(" ")
    );

    // Show the toast
    const toastPlacement = new bootstrap.Toast(toastPlacementExample);
    toastPlacement.show();
}

function updateClock() {
    const now = new Date();
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    };
    const date = now.toLocaleDateString(undefined, options); // Format date
    const time = now.toLocaleTimeString(); // Format time
    $("#liveClock").text(`${date} | ${time}`);
}

function select2Dropdown(
    selector,
    url,
    placeholder = "Search",
    dropdownParent = null,
    options = {}
) {
    let $initialSelection = $(selector).find("option:selected");

    if ($initialSelection.length > 0) {
        let detach = $initialSelection.val() === "";

        setTimeout(() => syncClassToContainer(selector, detach), 100);
    }

    let defaultValues = {
        allowClear: true,
        width: "100%",
        placeholder,
        dropdownParent,
    };

    var checkable = $(selector).is("[checkable]");

    if (checkable) {
        options.default = {
            theme: "default select2-container--checkable",
            closeOnSelect: false,
        };
    }

    $select2 = $(selector)
        .select2({
            ...defaultValues,
            ...options.default,
            ajax: {
                dataType: "json",
                url,
                delay: 500,

                data: function (params) {
                    return {
                        term: params.term,
                        ...options.ajaxParams,
                    };
                },

                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: params.page * 30 < data.total_count,
                        },
                    };
                },

                cache: true,
            },
        })
        .on("select2:select", function (e) {
            syncClassToContainer($(this), false);
        })
        .on("select2:unselect", function (e) {
            if ($(selector).is("[multiple]")) {
                let findChoices =
                    ".selection .select2-selection--multiple ul.select2-selection__rendered .select2-selection__choice";

                let choices = $(selector)
                    .siblings(".select2-container")
                    .find(findChoices);

                if (choices.length == 0) {
                    $(selector).val(null).trigger("change");
                    syncClassToContainer($(this));
                }
            } else {
                $(selector).val(null).trigger("change");
                syncClassToContainer($(this));
            }
        })
        .on("select2:open", function (e) {
            if ($(this).is("[multiple]")) {
                syncClassToContainer($(this), false);
            }
        })
        .on("select2:close", function (e) {
            let findChoices =
                ".selection .select2-selection--multiple ul.select2-selection__rendered .select2-selection__choice";

            let choices = $(selector)
                .siblings(".select2-container")
                .find(findChoices);

            if ($(this).is("[multiple]") && choices.length == 0) {
                syncClassToContainer($(this));
            }
        });

    return $select2;
}
