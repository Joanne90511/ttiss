/**
 * Created by mat on 17/01/16.
 */
/*
 * redcapChecked
 * =============
 * REDCap uses "1" and "0" to determine if a checkbox has been selected.
 * use: redcapChecked: $valueToBeChecked
 * Note: This is a first attempt and semi copied from the KO source
 */
ko.bindingHandlers.redcapChecked = {
    'init': function (element, valueAccessor) {
        function updateModel() {
            var isChecked = element.checked,
                elemValue = isChecked ? "1" : "0";

            // When we're first setting up this computed, don't change any model state.
            if (ko.computedContext.isInitial()) {
                return;
            }
            // Might need more logic here to check for value
            var modelValue = valueAccessor();
            if (ko.isObservable(modelValue) && elemValue !== modelValue()) {
                modelValue(elemValue);
            }

        }

        function updateView() {
            var modelValue = ko.utils.unwrapObservable(valueAccessor());

            //Might need to be ===
            element.checked = modelValue == "1";
        }

        var isCheckbox = element.type == "checkbox";

        // Only bind to check boxes
        if (!isCheckbox) {
            return;
        }

        // Set up two computed values to update the binding:

        // The first responds to changes in the checkedValue value and to element clicks
        ko.computed(updateModel, null, {disposeWhenNodeIsRemoved: element});
        ko.utils.registerEventHandler(element, "click", updateModel);

        // The second responds to changes in the model value (the one associated with the checked binding)
        ko.computed(updateView, null, {disposeWhenNodeIsRemoved: element});
    }
};


/*
 * redcapTime
 * ==========
 * Formats time when entered without formatting. 24Hr Time only
 * allows HH:MM H:MM HHMM HMM
 * use: redcapTime: $valueToFormat
 */
ko.bindingHandlers.redcapTime = {
    init: function (element, valueAccessor) {
        //Setup change event handler to update value in ViewModel
        $(element).prop('placeholder', 'HHMM, HH:MM, H:MM')
        var value = valueAccessor();
        $(element).change(function () {
            value($(this).val());
        });
    },
    update: function (element, valueAccessor) {
        var valueWrapped = valueAccessor();
        var value = ko.unwrap(valueWrapped);
        var parent = $(element).parent('.form-group');

        //Regex of a formated time
        var regexWorks = /^([0-1][0-9]|2[0-3]):[0-5][0-9]$/i;
        //Regex of an unformatted but formattable time
        var semiWorks = /^([0-9]|[0-1][0-9]|2[0-3])[0-5][0-9]$/i;
        //Assume error otherwise
        var error = true;

        if (regexWorks.test(value) || value.length == 0) {
            //everything is fine
            error = false;
        } else if (regexWorks.test('0' + value)) {
            //Check if leading zero was left off
            value = '0' + value;
            error = false;
        } else if (semiWorks.test(value)) {
            //try and format time
            var newVal = value.substr(0, value.length - 2) + ":" + value.substr(-2);

            //Check for leading zero
            if (value.length == 3) {
                newVal = '0' + newVal;
            }
            //Check if formatted time now works
            if (regexWorks.test(newVal)) {
                error = false;
                value = newVal;
            }
        }
        //Setup bootstrap class for form error
        if (error) {
            parent.addClass('has-error');
        } else {
            parent.removeClass('has-error');
            valueWrapped(value);
        }
        //Place the new value in the textbox
        $(element).val(value);
    }
};

/*
 * redcapDate
 * ==========
 * Formats the entered date. yyyy-mm-dd
 * Allows for date to be entered yyyymmdd for clients speed
 * use: redcapDate: $valueToBeFormatted
 */
ko.bindingHandlers.redcapDate = {
    init: function (element, valueAccessor) {
        //Setup change event handler to update value in ViewModel
        $(element).prop('placeholder', 'YYYYMMDD or YYYY-MM-DD');
        var value = valueAccessor();
        $(element).change(function () {
            value($(this).val());
        });
    },
    update: function (element, valueAccessor) {
        var valueWrapped = valueAccessor();
        var value = ko.unwrap(valueWrapped);
        var parent = $(element).parent('.form-group');

        //Formatted Regex
        var regexWorks = /^[0-2][0-9][0-9][0-9]-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])$/i;
        // Formattable regex
        var semiWorks = /^[0-2][0-9][0-9][0-9](0[1-9]|1[0-2])([0-2][0-9]|3[0-1])$/i;
        //Assume error by default
        var error = true;

        if (regexWorks.test(value) || value.length == 0) {
            //everything is fine
            error = false;
        } else if (semiWorks.test(value)) {
            //Try formatting date and running through regex again
            var newVal = value.substring(0, 4) + "-" + value.substring(4, 6) + '-' + value.substring(6);

            if (regexWorks.test(newVal)) {
                value = newVal;
                error = false;
            }
        }

        //Add or remove bootstrap class based on error
        if (error) {
            parent.addClass('has-error');
        } else {
            parent.removeClass('has-error');
            valueWrapped(value);
        }
        //Display new value if formatted otherwise client still sees old value
        $(element).val(value);
    }
};

/*
 * redcapReset
 * ===========
 * Removes the choice made for radio buttons
 * use: redcapReset: $valueToReset
 */
ko.bindingHandlers.redcapReset = {
    init: function (element, valueAccessor) {
        //simply set the value to blank on click
        var value = valueAccessor();
        $(element).click(function () {
            value("");
        });
    }
};
/*
 * redcapAuto
 * ==========
 * Uses the jQuery autocomplete to create an autocomplete checkbox
 * use: redcapAuto: {value: $valueToUpdateOnComplete, label: $LabelsToShow}
 */
ko.bindingHandlers.redcapAuto = {
    init: function (element, valueAccessor) {
        var object = valueAccessor();

        //Supplied options for the autocomplete
        var fields = ko.unwrap(object.label);
        //supplied value to place choice into
        var datum = ko.unwrap(object.value);
        var elem = $(element);

        // run through each item until we find the current value
        if (datum != '') {
            $.each(fields, function (index, item) {
                if (item.value == datum) {
                    elem.val(item.label);
                }
            });
        }

        //Select all the text if the textbox is clicked
        elem.click(function () {
            this.select();
        });

        elem.autocomplete({
            autoFocus: true,
            source: fields,
            select: function (event, ui) {
                //need this to show label
                event.preventDefault();
                //set in View model
                object.value(ui.item.value);
                //Show label from choice
                $(this).val(ui.item.label);

            }
        });
    }
};
/*
 * redcapRequired
 * ==============
 * Marks an elemnt with a style if it does not contain a value and is required
 * use: redcapRequired: $valueThatIsRequired
 */
ko.bindingHandlers.redcapRequired = {
    update: function (element, valueAccessor) {
        var value = ko.unwrap(valueAccessor());
        var needsError = false;
        //Determine if we are adding or removing the style
        if (value === '' || value === undefined) {
            needsError = true;
        }
        //Elements and the label for styling
        var target, label;
        //Get the type of element we are dealing with
        var type = $(element).prop('nodeName');
        // Determine the target of the CSS class based on node type
        switch (type) {
            case 'DIV':
                //For this we assume radios because I can :)
                target = $(element);
                label = $(element).siblings('label');
                break;
            case 'SELECT':
            case 'INPUT':
                target = $(element).parent('.form-group');
                label = $(element).siblings('label');
                break;
        }
    }
};

ko.bindingHandlers.redcapForeach = {
    init: function (element, valueAccessor) {
        //We know this is a table so we will
        var valueWrapped = valueAccessor();
        var fields = ko.unwrap(valueWrapped.field);
        var add = '';
        $.each(fields, function (i, item) {
            add += "<td><input type='radio' data-bind='value:" + item.value + "'></td>"
        });
        $(element).append(add);
        console.log(add);
    }
};

ko.bindingHandlers.redcapMessenger = {
    update: function (element, valueAccessor) {
        var value = ko.unwrap(valueAccessor());
        var elem = $(element);

        if (value === true || value === false) {
            var child = $(elem.children()[0]);
            if (value) {
                child.addClass('alert-success');
            } else {
                child.addClass('alert-danger');
            }

            elem.slideDown();
            setTimeout(function () {
                elem.fadeOut(400, function(){
                    child.removeClass('alert-success');
                    child.removeClass('alert-danger');
                });
            }, 3000);

        }


    }
};

ko.bindingHandlers.redcapTableCheckbox = {
    init: function (element, valueAccessor, allBindings) {
        $(element).on('click', function () {
            //Needs to be in the callback or the value will never change
            var object = valueAccessor();
            var value = ko.unwrap(valueAccessor());
            var check = allBindings.get('val');
            console.log(check);
            console.log(value);
            if(value !== '' && value == check)
            {
                object('');
            }else{
                object(check);
            }

        })
    },

    update: function (element, valueAccessor, allBindings) {
        var value = ko.unwrap(valueAccessor());
        var object = valueAccessor();
        var check = allBindings.get('val');

        if(value !== '' && check == value)
        {
            //This should be checked
            $(element).html('<i class="fa fa-check fa-3x" aria-hidden="true"></i>')
        }else{
            $(element).html('');
        }
        //Check if item is selected
        //console.log(value);
        //console.log(allBindings.get('val'));
    }
};

/*
* DateTimePicker Functionality
*/
ko.bindingHandlers.RedcapDatePicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        $(element).datetimepicker({format: 'YYYY-MM-DD', showTodayButton: true});

        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "dp.change", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                if (event.date != null && !(event.date instanceof Date)) {
                    // value(event.date.toDate());
                    value(event.date.format('YYYY-MM-DD'));
                } else {
                    value(event.date);
                }
            }
        });
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
        var picker = $(element).data("DateTimePicker");
        //when the view model is updated, update the widget
        if (picker) {
            var koDate = ko.utils.unwrapObservable(valueAccessor());

            picker.date(moment(koDate));
        }
    }
};

