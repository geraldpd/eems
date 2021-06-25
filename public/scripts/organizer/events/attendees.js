$(function() {

    let email = $('#email');
    $('.add-attendee').on('click', _ => {
        if(!email.val()) {
            return email.focus();
        }

        $('#attendees-list').append(`<li>${email.val()}</li>`);

        email.val('').focus();
    });

})