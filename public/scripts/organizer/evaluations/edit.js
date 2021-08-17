$(function() {
    let questions_div = $('.questions-div');
    let form_builder_div = $('.form_builder-div');

    $('#add-evaluation_type').on('click', function() {
        formBuilder();
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
                            <div class="option_list"></div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" minlength="1" id="add_option_value">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" id="add_option_button"> <i class="fas fa-plus"></i> </button>
                                </div>
                            </div>
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

        let input_query = `<div class="form-group col-md-12"> <label>Query</label> <textarea name="form_evaluation_query" id="form_evaluation_query" class="form-control"></textarea> </div>`;
        form_builder_div.empty().append(input_query+attributes);

        hookFormBuilderEventListeners(form_builder_div);
    });

    function formBuilder() {
        let evaluation_type = $('#evaluation_type').val();
        let label = form_builder_div.find('#form_evaluation_query').val();

        if(!label) return;

        let data = formAttributeConstructor();

        console.log(data)
        let form_input = formInputConstructor({
            label: form_builder_div.find('#form_evaluation_query').val(),
            type: evaluation_type,
            attributes: data.attributes,
            options: data.options
        });

        questions_div.append(form_input);
    }

    function formAttributeConstructor() {
        let evaluation_type = $('#evaluation_type').val();

        let attributes = '';
        let options = '';

        switch (evaluation_type) {
            case 'checkbox':
                form_builder_div.find('input, select').map((i, input) => {
                    if($(input).hasClass('option')) {
                        options += `<div class="form-check"> <label> <input class="form-check-input" type="checkbox" name="option[]" value="${$(input).val()}"> ${$(input).val()} </label> </div>`;
                    } else {
                        attributes += ` ${$(input).attr('name')}="${$(input).val()}`
                    }
                });
                break;

            case 'date':
                form_builder_div.find('input, select').map((i, input) => attributes += ` ${$(input).attr('name')}="${$(input).val()}"`);
                break;

            case 'number':
                form_builder_div.find('input, select').map((i, input) => attributes += ` ${$(input).attr('name')}="${$(input).val()}"`);
                break;

            case 'select':
                form_builder_div.find('input, select').map((i, input) => {
                    if($(input).hasClass('option')) {
                        options += `<option value="${$(input).val()}">${$(input).val()}</option>`;
                    } else {
                        attributes += ` ${$(input).attr('name')}="${$(input).val()}`
                    }
                });
                break;

            case 'text':
                form_builder_div.find('input, select').map((i, input) => attributes += ` ${$(input).attr('name')}="${$(input).val()}"`);
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
            case 'date':
                var form = `<input type="date" class="form-control" ${data.attributes}>`;

                break;
            case 'number':
                var form = `<input type="number" class="form-control" ${data.attributes}>`;

                break;
            case 'select':
                var form = `<select class="form-control"  ${data.attributes}> ${data.options}</select>`;

                break;
            case 'text':
                var form = `<textarea class="form-control" ${data.attributes}></textarea>`;

                break;
        }

        return `<div class="form-group col-md-12"><label>${data.label}</label>${form}</div>`;
    }

    function hookFormBuilderEventListeners(form_builder_div) {
        form_builder_div.find('#add_option_button').on('click', _ => {
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
        });
    }
});