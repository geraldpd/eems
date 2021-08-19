$(function() {
    let questions_div = $('.questions-div');
    let form_builder_div = $('.form_builder-div');

    $('#save-evaluation_form').on('click', function() {
        const html_form = questions_div.html();

        if(!questions_div.find('.evaluation_item').length) {
            window.Swal.fire({
                title: 'Evalaution Sheet can\'t be empty!',
                text: 'Please provide at least 1 evalaution item',
                icon: 'info',
                confirmButtonText: 'Okay',
                confirmButtonColor: '#007bff',
            });
            return;
        }

        $('#name').val($('#preview-name').val());
        $('#description').val($('#preview-description').val());
        $('#questions').val(JSON.stringify($.map(questions_div.find('.question_item'), label => $(label).text())));
        $('#html_form').val(html_form);
        localStorage.setItem('html_form', html_form);

        $('#evaluation-form').trigger('submit');
    });

    $('#add-evaluation_type').on('click', function() {
        let evaluation_type = $('#evaluation_type').val();
        let label = form_builder_div.find('#form_evaluation_query').val();

        if(['select', 'checkbox', 'radio'].includes(evaluation_type)) { //when the evaluation is type and there is o option provided, do nothin
            if(!form_builder_div.find('.option').length) return;
        }

        if(!label) return; //when no Query is provided, do not add to the evauation item

        let data = formAttributeConstructor();

        let form_input = formInputConstructor({
            label: form_builder_div.find('#form_evaluation_query').val(),
            type: evaluation_type,
            attributes: data.attributes,
            options: data.options
        });

        form_builder_div.slideUp().empty();
        questions_div.find('.empty-form_text').remove().append(form_input);
        questions_div.append(form_input);
    });

    $('#clear-evaluation_type').on('click', function() {

        if(!questions_div.find('.evaluation_item').length) {
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
                questions_div.html('<h2 class="empty-form_text">No Evaluation Entries </h2>');
            }
        })
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

    function formAttributeConstructor() {
        let evaluation_type = $('#evaluation_type').val();

        let attributes = '';
        let options = '';

        let questionToName = text => text.toLowerCase().replace(/[^\w ]+/g,'').replace(/ +/g,'_');

        switch (evaluation_type) {
            case 'checkbox':
                form_builder_div.find('input, select').map((i, input) => {
                    let attribute = $(input);
                    if(attribute.attr('name') == 'required' && attribute.val()) {
                        attributes += ` ${$(input).attr('name')}="${$(input).val()}"`;
                    }

                    if($(input).hasClass('option')) {
                        options += `<div class="form-check">
                                        <label>
                                            <input class="form-check-input" type="checkbox" name="${questionToName(form_builder_div.find('#form_evaluation_query').val())}[]" value="${$(input).val()}">
                                            ${$(input).val()}
                                        </label>
                                    </div>`;
                    }
                });
                break;
            case 'radio':
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
                                            <input class="form-check-input" type="radio" name="${questionToName(form_builder_div.find('#form_evaluation_query').val())}[]" value="${$(input).val()}">
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
            options: options
        };
    }

    function formInputConstructor(data) {
        switch (data.type) {
            case 'checkbox':
                var form = data.options;
                break;

            case 'radio':
                var form = `<br> <div class="d-flex justify-content-between align-content-stretch flex-wrap">
                                <div class="align-middle"><span>Not Very</span></div>
                                ${data.options}
                                <div class="align-middle"><span>Very Much</span></div>
                            </div>`;
                break;

            case 'date':
                var form = `<input type="date" class="form-control" ${data.attributes}>`;

                break;
            case 'number':
                var form = `<input type="number" class="form-control" ${data.attributes}>`;

                break;
            case 'select':
                var form = `<select class="form-control" ${data.attributes}> ${data.options} </select>`;

                break;
            case 'text':
                var form = `<textarea class="form-control" placeholder="Your answer" ${data.attributes}></textarea>`;

                break;
        }

        let has_required = data.attributes.includes('required') ? '<strong class="text-danger" title="required">*</strong>' : '';

        return `<li draggable data-type="${data.type}" class="form-group evaluation_item alert alert-light">
                    <label class="question_item">${data.label} ${has_required}</label>
                    <span class="edit-evaluation_type btn btn-link float-right">edit</span>
                    ${form}
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
                <div class="input-group mb-3 option_item_div">
                    <input type="text" class="option form-control" value="${option_value.val()}">
                    <div class="input-group-append">
                        <button class="btn btn-light remove_option_button" type="button"> <i class="fas fa-trash"></i> </button>
                    </div>
                </div>
            `);

            form_builder_div.find('.remove_option_button').on('click', function() {
                $(this).closest('.option_item_div').remove();
            });

            option_value.val('').focus();
        }

    }

    $(document).on('click', '.edit-evaluation_type', function() {
        let form_item =  $(this).closest('li');
        let form_element = form_item.find('input, select, textarea, checkbox, radio');

        let type = form_item.data('type');
        let label = form_item.find('label.question_item').text().replace(' *', '');
        let attributes = form_element.get(0).attributes;

        $('#evaluation_type').val(type).trigger('change');

        form_builder_div.find(`select[name="required"]`).val(attributes.hasOwnProperty('required') ? 'required' : ''); //so far, for required attribute only

        $.each(attributes, (i, attr) => {
            form_builder_div.find(`input[name="${attr.name}"]`).val(attr.value);
        });

        //loop form_element
        if(type == 'checkbox') {
            form_builder_div.find('#add_option_value').val(attr.value)
            form_builder_div.find('#add_option_button').trigger('click')
        }

        console.log(form_element)

        form_builder_div.find('#form_evaluation_query').val(label);


    })
});