/**
 * Created by mat on 29/12/15.
 */

var Option = function (label, value) {
    this.label = label;
    this.value = value;
};

function AppViewModel() {

    var self = this;

    //Used to give status
    self.saveStatusMessage = ko.observable('Has Not Been Saved');
    self.saveStatus = ko.observable();

    self.save = function (callback) {
        self.saveStatusMessage('Saving...');
        self.clearHiddenFields();
        //Need to clear hidden elements
        $.ajax({
            type: "POST",
            url: "/form/record",
            data: ko.mapping.toJSON(self.data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
        }).done(function (data, status) {
            self.saveStatus(true);
            self.saveStatusMessage('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Save Successful ');
            currentdate = new Date();
            var datetime = "Last Save: " + currentdate.getDate() + "/"
                + (currentdate.getMonth() + 1) + "/"
                + currentdate.getFullYear() + " "
                + currentdate.getHours() + ":"
                + currentdate.getMinutes() + ":"
                + currentdate.getSeconds();
            setTimeout(function () {
                self.saveStatus('');
                self.saveStatusMessage(datetime);
            }, 2000);
            self.data.case_id(data.id);
            var getType = {};
            if (getType.toString.call(callback) === '[object Function]') {
                callback();
            }
        }).fail(function (data, status) {
            self.saveStatus(false);
            // TODO display failure to user with possible resolutions
            console.log(data);
            console.log(status);
        });
    };

    self.clearHiddenFields = function() {
        for(var field in self.data)
        {
            if(ko.isObservable(self.data[field]) && $('#' + field).is(':visible') == false){
                self.data[field](''); //Clears the fields that are not visible. I.E. entered by accident
            }
        }
    };

    self.output = function () {
        self.save(function () {
            var params = [];

            params.push(encodeURIComponent('first_name') + '=' + encodeURIComponent($('#first_name').val()));
            params.push(encodeURIComponent('last_name') + '=' + encodeURIComponent($('#last_name').val()));
            params.push(encodeURIComponent('health_card_number') + '=' + encodeURIComponent($('#healthnum').val()));
            params.push(encodeURIComponent('hospital_card_number') + '=' + encodeURIComponent($('#hospnum').val()));
            params.push(encodeURIComponent('city') + '=' + encodeURIComponent($('#city').val()));
            params.push(encodeURIComponent('province') + '=' + encodeURIComponent($('#province').val()));
            self.ignore = true;

            window.location.replace("/form/output/" + self.data.case_id() + '?' + params.join('&'));
        });

    };

    self.ignore = false;

    self.saveAndExit = function () {
        self.save(function () {
            self.ignore = true;
            window.location.replace('/form/');
        });

    };


}

var vm = new AppViewModel();
var case_id = $('#record').val();
$(".loader h6").text("Getting Record");
$.getJSON("/form/record/" + case_id, function (data) {
    $(".loader h6").text("Mapping Data");
    vm.data = ko.mapping.fromJS(data);
}).done(function () {
    $(".loader h6").text("Getting Display Information");
    $.getJSON("/form/fields", function (data) {
        var fields = {};
        $.each(data, function (i, val) {
            fields[i] = ko.observableArray();
            $.each(val, function (i2, val2) {
                //Added toString as some values are coming in as numbers and this creates an issue when
                //We check if the values match as KO uses === which checks type
                fields[i].push(new Option(val2, i2.toString()));
            });
        });
        vm.fields = fields;
        vm.fields['ctaerfcanadian_transfusion_reaction_adverse_event_complete'] = ko.observableArray();
        vm.fields['ctaerfcanadian_transfusion_reaction_adverse_event_complete'].push(new Option('Incomplete', 1));
        vm.fields['ctaerfcanadian_transfusion_reaction_adverse_event_complete'].push(new Option('Complete', 2))
        vm.fields.blood_group_rh.reverse()
    }).done(function () {
        $(".loader h6").text("Applying Data Bindings");
        ko.applyBindings(vm);
        $(".loader h6").text("Complete");
        $(".loader").fadeOut("slow");
        $('#facilityid_name_facility').tooltip();
        if(vm.data.facilityid_name_facility() == '')
        {
            $('#facilityid_name_facility').focus();
        }

    });
});

$(window).on('beforeunload', function () {
    if (vm.ignore == false) {
        return 'Please save any changes have been saved before leaving this page';
    }
});
