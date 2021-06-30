$(function() {

    var table = $('.table');

    var send_invitation_button = $('.send-invitation');

    var invitees = $('#invitees');

    var tagTemplate = function(tagData){
        return `
            <tag title="${(tagData.title || tagData.name)}"
                    contenteditable='false'
                    spellcheck='false'
                    tabIndex="-1"
                    class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ""}"
                    ${this.getAttributes(tagData)}>
                <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                <div>

                    <span class='tagify__tag-text'>${tagData.email}</span>
                </div>
            </tag>
        `
    }

    var suggestionItemTemplate = function(tagData){
        return `
            <div ${this.getAttributes(tagData)}
                class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}'
                tabindex="0"
                role="option">
                <strong>${tagData.name}</strong> - <span>${tagData.email}</span>
            </div>
        `
    }

    var tagify = new Tagify(invitees.get(0), {
        pattern: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        blacklist : config.event.blacklist,
        enforceWhitelist : false,
        tagTextProp: 'email', // very important since a custom template is used with this property as text
        templates: {
            tag: tagTemplate,
            dropdownItem: suggestionItemTemplate
        },
        dropdown: {
            closeOnSelect: false,
            enabled: 0,
            classname: 'users-list',
            searchKeys: ['name', 'email']  // very important to set by which keys to search for suggesttions when typing
        },
        editTags: {
            clicks: 1,              // single click to edit a tag
            keepInvalid: false      // if after editing, tag is invalid, auto-revert
        },
        callbacks : {
            add    : console.log,  // callback when adding a tag
            remove : console.log   // callback when removing a tag
        }
    });

    function onInputTag(e) {
        tagify.whitelist = null; // reset current whitelist
        tagify.loading(true) // show the loader animation

        axios.post(config.routes.suggest_attendees, {
            keyyword: e.detail.value,
            event_id: config.event.id
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
        tagify.value.length ? send_invitation_button.removeAttr('disabled') : send_invitation_button.attr('disabled', true)
    }

    function onEditTag(e) {
        tagify.value.length ? send_invitation_button.removeAttr('disabled') : send_invitation_button.attr('disabled', true)
    }

    function onTagifyFocusBlur(e) {
        tagify.value.length ? send_invitation_button.removeAttr('disabled') : send_invitation_button.attr('disabled', true)
    }

    tagify.on('input', onInputTag)
          .on('add', onAddTag)
          .on('add', onEditTag)
          .on('remove', onRemoveTag)
          .on('blur', onTagifyFocusBlur)

    $.noConflict();

    $(table).DataTable({
        order: [[0, 'desc']],
        columnDefs: [
            {
                "targets": [ 0 ],
                "visible": false
            }
        ],
        language: {
            search: "",
            searchPlaceholder: "Seach Invited Guests",
        },
        bLengthChange: false,
        bFilter: true,
        bInfo: false,
        bAutoWidth: false
    });
})