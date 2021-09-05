$(function() {
    let questions_div = $('.questions-div');
    let form_builder_div = $('.form_builder-div');

    $('#save-evaluation_form').on('click', function() {
        const html_form = questions_div.html();

        //! stop when - no entry is given
        if(!questions_div.find('.evaluation_entry').length) {
            window.Swal.fire({
                title: 'evaluation Sheet can\'t be empty!',
                text: 'Please provide at least 1 evaluation entry',
                icon: 'info',
                confirmButtonText: 'Okay',
                confirmButtonColor: '#007bff',
            });
            return;
        }

        //! when there is only one event set and has not yet concluded, this happens when a newly created event, gets its own evaluation
        if(config.events_count == 1 && config.event != null) {
            if(moment(config.event.scheduled_start).isBefore()) {
                return updateEvaluationForm();
            }
        }

        //!when there is more than one event using this evaluation sheet, this happens when an evaluation sheet has multiple pending events assigned to it
        if(config.evaluation.pending_events.length > 1) {
            let pending_event_rows = config.evaluation.pending_events.map((event) =>  `<tr> <td>${event.name}</td> <td>${moment(event.schedule_start).format('MMM DD, YYYY HH:mm a')}</td> </tr>`);

            window.Swal.fire({
                title: `Modify Evaluation Sheet?`,
                html: `
                    <table class="table table-bordered">
                        <thead>
                            <th>Event</th>
                            <th>Schedule</th>
                        </thead>
                        <tbody>${pending_event_rows}</tbody>
                    </table>
                    <br>
                   There are ${config.evaluation.pending_events.length} booked events that will use this evalution sheet, Proceed Modification?
                `,
                icon: 'question',
                confirmButtonText: 'Yes',
                confirmButtonColor: '#007bff',
                showCancelButton: true
            })
            .then((result) => {
                if (result.isConfirmed) {
                    return updateEvaluationForm();
                }
                return;
            });
        } else {
            return updateEvaluationForm();
        }

        function updateEvaluationForm() {

            let questions = [];
            $.map(questions_div.find('.question_entry'), label => {
                let key = $(label).data('question_key');
                let value =  $(label).text().replace(' *', '');

                questions.push({[key]: value})
                //return questions[$(label).data('question_key')] = $(label).text().replace(' *', '')
            });

            $('#name').val($('#preview-name').val());
            $('#description').val($('#preview-description').val());
            $('#questions').val(JSON.stringify(questions));
            $('#html_form').val(html_form);
            localStorage.setItem('html_form', html_form);

            $('#evaluation-form').trigger('submit');
        }

    });

    $('#add-evaluation_type').on('click', function() {
        let evaluation_type = $('#evaluation_type').val();
        let label = form_builder_div.find('#form_evaluation_query').val();

        if(['select', 'checkbox', 'radio'].includes(evaluation_type)) { //when the evaluation is type and there is o option provided, do nothin
            if(!form_builder_div.find('.option').length) return;
        }

        if(!label) return; //when no Query is provided, do not add to the evaluation entry

        questions_div.find('.empty-form_text').remove();
        questions_div.append($(formBuilder()));
    });

    $('#clear-evaluation_type').on('click', function() {

        if(!questions_div.find('.evaluation_entry').length) {
            window.Swal.fire({
                title: 'Nothing to clear!',
                text: 'This evaluation sheet is empty',
                icon: 'info',
                confirmButtonText: 'Okay',
                confirmButtonColor: '#007bff',
            });
            return;
        }

        window.Swal.fire({
            title: 'Please Confirm',
            text: 'Are you sure you want to clear the evaluation entries?',
            icon: 'question',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#007bff',
            showCancelButton: true
        })
        .then((result) => {
            if (result.isConfirmed) {
                questions_div.html('<h2 class="empty-form_text text-muted">No Evaluation Entries </h2>');
            }
        });
    });

    $('#evaluation_type').on('change', function() {
        let evaluation_type = $(this);

        if(!evaluation_type.val()) {
            return form_builder_div.empty().append('');
        };

        let evaluation_type_attributes = evaluation_type.find(':selected').data('attributes');

        let attributes = evaluation_type_attributes.map(attribute => {
            switch (true) {
                case attribute == 'required': //? Required
                    input = `<select name="${attribute}" class="form-control">
                                <option value="required" selected> Yes </option>
                                <option value="" > No </option>
                            </select>`;
                break;

                case attribute == 'options': //? Option
                    input = `
                        <div class="add_option_div">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" minlength="1" id="add_option_value">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" id="add_option_button"> <i class="fas fa-plus"></i> </button>
                                </div>
                            </div>
                            <div class="option_list"></div>
                        </div>
                    `;
                break;

                case evaluation_type.val() == 'date' && ['min', 'max'].includes(attribute): //? Date
                    type = 'date'
                    input = `<input name="${attribute}" type="date" min="1950-01-01" class="form-control">`;
                break;

                case attribute.includes(['min', 'max', 'minlength', 'maxlength']) ? 'number' : 'text': //? Number
                    type = 'number'
                    input = `<input name="${attribute}" type="number" class="form-control">`;
                break;

                default: //? Text
                    type = '';
                    input = `<input name="${attribute}" type="text" value="${attribute.includes('min') ? '0' : '100'}" class="form-control">`;
            }

            return `<div class="form-group col-md-6"><label style="text-transform:capitalize">${attribute}</label>${input}</div>`;
        }).join('');

        let input_query = `<div class="form-group col-md-12"> <label>Query</label> <textarea name="form_evaluation_query" id="form_evaluation_query" class="form-control" placeholder="Ask a question"></textarea> </div>`;
        form_builder_div.empty().append(input_query+attributes).slideDown();

        hookFormBuilderEventListeners();
    });

    questions_div.on('click', '.edit-evaluation_type', function() {
        formModifier(this);
    });

    questions_div.on('click', '.remove-evaluation_type', function() {
        window.Swal.fire({
            title: 'Remove this Entry?',
            text: 'Are you sure you want to remove this evaluation entry?',
            icon: 'question',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#007bff',
            showCancelButton: true
        })
        .then((result) => {
            $('#evaluation_type').val('').trigger('change');
            $('.form-creation-buttons').removeClass('d-none'); //return the add and clear buttons
            $('.form-modification-buttons').addClass('d-none'); //hide the update button
            if (result.isConfirmed) {
                $(this).closest('li').remove();
                if(!$('.questions-div').find('li').length) {
                    questions_div.html('<h2 class="empty-form_text">No Evaluation Entries </h2>');
                }
            }
        })
    });

    function formBuilder() {
        let evaluation_type = $('#evaluation_type').val();
        let label = form_builder_div.find('#form_evaluation_query').val();

        if(['select', 'checkbox', 'radio'].includes(evaluation_type)) { //when the evaluation is type and there is o option provided, do nothin
            if(!form_builder_div.find('.option').length) return;
        }

        if(!label) return; //when no Query is provided, do not add to the evaluation entry

        let data = formAttributeConstructor();

        let form_input = formInputConstructor({
            label: form_builder_div.find('#form_evaluation_query').val(),
            type: evaluation_type,
            attributes: data.attributes,
            options: data.options,
            name: data.name
        });

        form_builder_div.slideUp().empty();

        $('#evaluation_type').val('').trigger('change');

        return form_input;

    }

    function formModifier(edit_button) {
        questions_div.find('li').each((i, li) => $(li).removeClass('alert-info').addClass('alert-light')); //remove highlight of all list

        let form_entry =  $(edit_button).closest('li');
        let form_element = form_entry.find('input, select, textarea, checkbox, radio');

        let type = form_entry.data('type');
        let label = form_entry.find('label.question_entry').text().replace(' *', '');
        let required = form_entry.find('label.question_entry').data('is_required'); // 1 or 0
        let attributes = form_element.get(0).attributes;

        form_entry.removeClass('alert-light').addClass('alert-info'); //add highlight to the entry

        $('#evaluation_type').val(type).trigger('change');

        //required attr
        form_builder_div.find(`select[name="required"]`).val(required ? 'required' : '');

        //other inputs
        $.each(attributes, (i, attr) => {
            form_builder_div.find(`input[name="${attr.name}"]`).val(attr.value);
        });

        //checkboxes and radio buttons
        if(['checkbox', 'radio'].includes(type)) {
            form_element.each((i, input) => {
                form_builder_div.find('#add_option_value').val($(input).val())
                form_builder_div.find('#add_option_button').trigger('click')
            });
        }

        //select
        if(['select'].includes(type)) {
            form_element.find('option').each((i, input) => {
                form_builder_div.find('#add_option_value').val($(input).attr('value'))
                form_builder_div.find('#add_option_button').trigger('click')
            });
        }

        //the query
        form_builder_div.find('#form_evaluation_query').val(label);

        $('.form-creation-buttons').addClass('d-none'); //hide the add and clear buttons

        $('.form-modification-buttons').removeClass('d-none'); //show the update button

        //hook update event
        $('#update-evaluation_type').off().on('click', _ => {
            let evaluation_type = $('#evaluation_type').val();
            let label = form_builder_div.find('#form_evaluation_query').val();

            if(['select', 'checkbox', 'radio'].includes(evaluation_type)) { //when the evaluation is type and there is o option provided, do nothin
                if(!form_builder_div.find('.option').length) return;
            }

            if(!label) return; //when no Query is provided, do not add to the evaluation entry

            form_entry.replaceWith($(formBuilder())); //replace the list entry with the new form_entry
            stopModification();
        });

        //hook cancel event
        $('#cancel-evaluation_type').off().on('click', _ => stopModification());

        function stopModification() {
            $('#evaluation_type').val('').trigger('change'); //reset eht evalution type selector
            $('.form-creation-buttons').removeClass('d-none'); //return the add and clear buttons
            $('.form-modification-buttons').addClass('d-none'); //hide the update button
            form_entry.removeClass('alert-info').addClass('alert-light');
        }
    }

    function formAttributeConstructor() {
        let evaluation_type = $('#evaluation_type').val();

        let attributes = '';
        let options = '';

        let questionToName = text => text.toLowerCase().replace(/[^\w ]+/g,'').replace(/ +/g,'_');
        let name = (Math.random() + 1).toString(36).substring(7);

        switch (evaluation_type) {
            case 'checkbox':
                var is_required = form_builder_div.find("[name='required']").val() ? 'required' : '';
                form_builder_div.find('input, select').map((i, input) => {
                    let attribute = $(input);
                    if(attribute.attr('name') == 'required' && attribute.val()) {
                        attributes += ` ${$(input).attr('name')}="${$(input).val()}"`;
                    }

                    if($(input).hasClass('option')) {
                        options += `<div class="form-check">
                                        <label>
                                            <input class="form-check-input" type="checkbox" name="${name}[]" value="${$(input).val()}">
                                            ${$(input).val()}
                                        </label>
                                    </div>`;
                    }
                });
                break;
            case 'radio':
                var is_required = form_builder_div.find("[name='required']").val() ? 'required' : '';
                form_builder_div.find('input, select').map((i, input) => {
                    let attribute = $(input);
                    if(attribute.attr('name') == 'required' && attribute.val()) {
                        attributes += ` ${$(input).attr('name')}="${$(input).val()}"`;
                    }

                    if($(input).hasClass('option')) {
                        options += `<div class="form-check form-check-inline">
                                        <label>
                                            ${$(input).val()}
                                            <br>
                                            <input ${is_required} class="form-check-input" type="radio" name="${name}" value="${$(input).val()}">
                                        </label>
                                    </div>`;
                    }
                });
                break;

            case 'date':
                form_builder_div.find('input, select').map((i, input) => {input
                    let attribute = $(input);
                    if(attribute.attr('name') == 'required') {
                        if(!attribute.val()) return;
                    }
                    return attributes += ` ${attribute.attr('name')}="${attribute.val()}"`
                });
                break;

            case 'number':
                form_builder_div.find('input, select').map((i, input) => {input
                    let attribute = $(input);
                    if(attribute.attr('name') == 'required') {
                        if(!attribute.val()) return;
                    }
                    return attributes += ` ${attribute.attr('name')}="${attribute.val()}"`
                });
                break;

            case 'select':
                form_builder_div.find('input, select').map((i, input) => {
                    let attribute = $(input);

                    if(attribute.attr('id') == 'add_option_value') return;

                    if(attribute.attr('name') == 'required' && attribute.val()) {
                        attributes += ` ${$(input).attr('name')}="${$(input).val()}"`;
                    }

                    if($(input).hasClass('option')) {
                        options += `<option value="${$(input).val()}">${$(input).val()}</option>`;
                    }
                });
                break;

            case 'text':
                form_builder_div.find('input, select').map((i, input) => {input
                    let attribute = $(input);
                    if(attribute.attr('name') == 'required') {
                        if(!attribute.val()) return;
                    }
                    return attributes += ` ${attribute.attr('name')}="${attribute.val()}"`
                });
                break;
        }

        return {
            attributes: attributes,
            options: options,
            name: name
        };
    }

    function formInputConstructor(data) {

        switch (data.type) {
            case 'checkbox':
                var form = data.options;
                break;

            case 'radio':
                var form = `<br> <div class="d-flex justify-content-between align-content-stretch flex-wrap">
                                <div class="align-middle" contenteditable><span>Not Very</span></div>
                                ${data.options}
                                <div class="align-middle" contenteditable><span>Very Much</span></div>
                            </div>`;
                break;

            case 'date':
                var form = `<input name="${data.name}" type="date" class="form-control" ${data.attributes}>`;

                break;
            case 'number':
                var form = `<input name="${data.name}" type="number" class="form-control" ${data.attributes}>`;

                break;
            case 'select':
                var form = `<select name="${data.name}" class="form-control" ${data.attributes}> ${data.options} </select>`;

                break;
            case 'text':
                var form = `<textarea name="${data.name}" class="form-control" placeholder="Your answer" ${data.attributes}></textarea>`;

                break;
        }

        let has_required = data.attributes.includes('required') ? '<strong class="text-danger" title="required">*</strong>' : '';

        return `<li draggable data-type="${data.type}" class="form-group evaluation_entry alert alert-light">
                    <div class="row">
                        <div class="col-md-10 col-xs-12">
                            <label class="question_entry" data-question_key="${data.name.trim()}" data-is_required="${has_required ? 1 : 0}">${data.label.trim()} ${has_required}</label>
                        </div>
                        <div class="col-md-2 col-xs-12 d-flex justify-content-center">
                            <span class="edit-evaluation_type btn btn-link float-right">edit</span>
                            <span class="remove-evaluation_type btn btn-link text-secondary float-right">remove</span>
                        </div>
                        <div class="col-md-12">${form}</div>
                    </div>
                </li>`;
    }

    function hookFormBuilderEventListeners() {
        form_builder_div.find('#add_option_button').on('click', e => optionEvents()); //on click the add icon for options
        form_builder_div.find('#add_option_value').on('keypress', e => {
            if(e.keyCode == '13') {
                optionEvents()
            }
        }); //on press ente

        function optionEvents() {
            let option_value = form_builder_div.find('.add_option_div').find('#add_option_value');

            if(!option_value.val()) {
                return;
            }

            form_builder_div.find('.add_option_div').find('.option_list').append(`
                <div class="input-group mb-3 option_entry_div">
                    <input type="text" class="option form-control" value="${option_value.val()}">
                    <div class="input-group-append">
                        <button class="btn btn-light remove_option_button" type="button"> <i class="fas fa-trash"></i> </button>
                    </div>
                </div>
            `);

            form_builder_div.find('.remove_option_button').on('click', function() {
                $(this).closest('.option_entry_div').remove();
            });

            option_value.val('').focus();
        }
    }
});