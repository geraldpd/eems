$(function() {

    var ccTag = $('#cc-tag');
    var bccTag = $('#bcc-tag');

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

    function initTaggify(tag) {
        if(tag.length) {
            var tags = new window.tagify(tag.get(0), {
                pattern: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                //blacklist : config.event.blacklist,
                enforceWhitelist : false,
                tagTextProp: 'email', // very important since a custom template is used with this property as text
                templates: {
                    tag: tagTemplate,
                    //dropdownItem: suggestionItemTemplate
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
                    //add    : console.log,  // callback when adding a tag
                    //remove : console.log   // callback when removing a tag
                }
            });

            return tags;
        }
    }

    let ccTagInstance = initTaggify(ccTag);

    let bccTagInstance = initTaggify(bccTag);

    ccTagInstance.on('blur', function() {
        $('#cc').val(ccTagInstance.value.map(tag => tag.email).join(','));
    })

    bccTagInstance.on('blur', function() {
        $('#bcc').val(bccTagInstance.value.map(tag => tag.email).join(','));
    })

    window.ClassicEditor
    .create($('#message').get(0), {
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'],
    })
    .then( editor => {
        //console.log( editor );
    } )
    .catch( error => {
        //console.error( error );
    } );

})