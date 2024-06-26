$(function () {
    var send_invitation_button = $(".send-invitation");

    var invitees = $("#invitees");

    var tagTemplate = function (tagData) {
        return `
            <tag title="${tagData.title || tagData.name}"
                    contenteditable='false'
                    spellcheck='false'
                    tabIndex="-1"
                    class="${this.settings.classNames.tag} ${
            tagData.class ? tagData.class : ""
        }"
                    ${this.getAttributes(tagData)}>
                <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                <div>

                    <span class='tagify__tag-text'>${tagData.email}</span>
                </div>
            </tag>
        `;
    };

    var suggestionItemTemplate = function (tagData) {
        return `
            <div ${this.getAttributes(tagData)}
                class='tagify__dropdown__item ${
                    tagData.class ? tagData.class : ""
                }'
                tabindex="0"
                role="option">
                <h4 style="margin:0px;">${tagData.name}</h4> <span>${
            tagData.email
        }</span>
            </div>
        `;
    };

    if (invitees.length) {
        var tagify = new window.tagify(invitees.get(0), {
            pattern:
                /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
            blacklist: config.event.blacklist,
            enforceWhitelist: false,
            tagTextProp: "email", // very important since a custom template is used with this property as text
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate,
            },
            dropdown: {
                closeOnSelect: false,
                enabled: 0,
                classname: "users-list",
                searchKeys: ["name", "email"], // very important to set by which keys to search for suggesttions when typing
            },
            editTags: {
                clicks: 1, // single click to edit a tag
                keepInvalid: false, // if after editing, tag is invalid, auto-revert
            },
            callbacks: {
                add: console.log, // callback when adding a tag
                remove: console.log, // callback when removing a tag
            },
        });
    }

    function onInputTag(e) {
        tagify.whitelist = null; // reset current whitelist
        tagify.loading(true); // show the loader animation

        axios
            .post(config.routes.suggest_attendees, {
                keyyword: e.detail.value,
                event_id: config.event.id,
            })
            .then(function (response) {
                tagify.whitelist = response.data;
                tagify.loading(false);
            })
            .catch(function (error) {
                console.warn(error);
            });
    }

    function onAddTag(e) {
        //do something
    }

    function onRemoveTag(e) {
        tagify.value.length
            ? send_invitation_button
                  .removeAttr("disabled")
                  .addClass("btn-primary")
                  .removeClass("btn-secondary")
            : send_invitation_button
                  .attr("disabled", true)
                  .addClass("btn-secondary")
                  .removeClass("btn-primary");
    }

    function onEditTag(e) {
        tagify.value.length
            ? send_invitation_button
                  .removeAttr("disabled")
                  .addClass("btn-primary")
                  .removeClass("btn-secondary")
            : send_invitation_button
                  .attr("disabled", true)
                  .addClass("btn-secondary")
                  .removeClass("btn-primary");
    }

    function onTagifyFocusBlur(e) {
        tagify.value.length
            ? send_invitation_button
                  .removeAttr("disabled")
                  .addClass("btn-primary")
                  .removeClass("btn-secondary")
            : send_invitation_button
                  .attr("disabled", true)
                  .addClass("btn-secondary")
                  .removeClass("btn-primary");
    }

    if (!config.event_is_past) {
        if (invitees.length) {
            tagify
                .on("input", onInputTag)
                .on("add", onAddTag)
                .on("add", onEditTag)
                .on("remove", onRemoveTag)
                .on("blur", onTagifyFocusBlur);
        }
    }

    $(".print-button").on("click", (_) => {
        window.print();
    });

    $("#table").DataTable({
        order: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0],
                visible: false,
            },
        ],
        language: {
            search: "",
            searchPlaceholder: "Search Invited Guests",
        },
        bLengthChange: false,
        bFilter: true,
        bAutoWidth: false,
        pagingType: "full",
        responsive: true,
    });

    $(".approve-booking").on("click", function () {
        let attendee_id = $(this).data("attendee_id");

        Swal.fire({
            icon: "question",
            title: "Are you sure you want to approve this Booking?",
            showCancelButton: false,
            confirmButtonText: "Approve Booking",
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false,
            preConfirm: () => {
                Swal.showLoading();
                return axios.post(config.routes.approve, {
                    attendee_id: attendee_id,
                    action: 'approve'
                }).then(response => {
                    if (response.data.result == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: "Booking has been Approved!",
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: "Ooops",
                            text: response.data.message
                        });
                    }
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        text: "Something went wrong!"
                    });
                    console.log(error);
                });
            }
        });
    });

    $(".disapprove-booking").on("click", function () {
        let attendee_id = $(this).data("attendee_id");

        Swal.fire({
            icon: "warning",
            input: "text",
            title: "Are you sure you want to disapprove this Booking?",
            customClass: {
                confirmButton: 'btn btn-danger',
            },
            inputPlaceholder: 'Please provide reason for disapproval',
            buttonsStyling: false,
            showCancelButton: false, // Allow cancellation for better UX
            confirmButtonText: "Disapprove Booking",
            showLoaderOnConfirm: true,
            preConfirm: (reason) => {

                return axios.post(config.routes.disapprove, {
                    attendee_id: attendee_id,
                    action: 'disapprove',
                    reason: reason
                }).then(response => {
                    if (response.data.result == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: "Booking has been Disapproved!",
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: "Ooops",
                            text: response.data.message
                        });
                    }
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        text: "Something went wrong!"
                    });
                    console.log(error);
                });
            }
        });
    });

});
