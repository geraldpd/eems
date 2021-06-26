$(function() {

    var input = document.querySelector('#email');
    var tagify = new Tagify(input, {
        pattern: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        enforceWhitelist : false,
        delimiters : null,
        // transformTag : function ( tagData ){
        //     tagData.style = "--tag-bg:#007bff";
        // },
        editTags: {
            clicks: 1,              // single click to edit a tag
            keepInvalid: false      // if after editing, tag is invalid, auto-revert
        },
        callbacks : {
            add    : console.log,  // callback when adding a tag
            remove : console.log   // callback when removing a tag
        }
    });

    tagify.on('input', tagifyOnInput)

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
})