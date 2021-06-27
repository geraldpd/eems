$(function() {

    var input = document.querySelector('#email');
    var tagify = new Tagify(input, {
        pattern: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        enforceWhitelist : false,
        //delimiters : null,
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

    tagify.on('input', tagifyOnInput);
    //tagify.on('dropdown:show dropdown:updated', onDropdownShow)

    function tagifyOnInput(e) {
        tagify.whitelist = null; // reset current whitelist
        tagify.loading(true) // show the loader animation

        axios.post(config.routes.suggest_attendees, {keyyword: e.detail.value})
        .then(function (response) {
            tagify.whitelist = response.data;
            tagify.loading(false);
        })
        .catch(function (error) {
          console.warn(error);
        });

    }

    function tagTemplate(tagData){
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

    function suggestionItemTemplate(tagData){
        return `
            <div ${this.getAttributes(tagData)}
                class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}'
                tabindex="0"
                role="option">
                <strong>${tagData.name}</strong> - <span>${tagData.email}</span>
            </div>
        `
    }

    console.log(tagify)

})