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

    self.save = function () {
        self.saveStatusMessage('Saving...')
        $.ajax({
            type: "POST",
            url: "/record",
            data: ko.mapping.toJSON(self.data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
        }).done(function (data, status) {
            self.saveStatus(true);
            self.saveStatusMessage('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Save Successful ');
            currentdate = new Date();
            var datetime = "Last Save: " + currentdate.getDate() + "/"
                + (currentdate.getMonth()+1)  + "/"
                + currentdate.getFullYear() + " "
                + currentdate.getHours() + ":"
                + currentdate.getMinutes() + ":"
                + currentdate.getSeconds();
            setTimeout(function(){
                self.saveStatus('');
                self.saveStatusMessage(datetime);
            }, 2000);
            self.data.case_id(data.id);
        }).fail(function (data, status) {
            self.saveStatus(false);
            // TODO display failure to user with possible resolutions
            console.log(data);
            console.log(status);
        });
    };

    self.output = function(){
        var params = [];

        params.push(encodeURIComponent('first_name') + '=' + encodeURIComponent($('#first_name').val()));
        params.push(encodeURIComponent('last_name') + '=' + encodeURIComponent($('#last_name').val()));
        params.push(encodeURIComponent('health_card_number') + '=' + encodeURIComponent($('#healthnum').val()));
        params.push(encodeURIComponent('hospital_card_number') + '=' + encodeURIComponent($('#hospnum').val()));
        console.log(params.join('&'));

        window.location.replace("/output/" + self.data.case_id() + '?' + params.join('&'));
    };

    self.ignore = false;

    self.saveAndExit = function () {
        self.save();
        self.ignore = true;
        window.location.replace('/');
    };


}

var vm = new AppViewModel();
var case_id = $('#record').val();
$(".loader h6").text("Getting Record");
$.getJSON("/record/" + case_id, function (data) {
    $(".loader h6").text("Mapping Data");
    vm.data = ko.mapping.fromJS(data);
}).done(function () {
    $(".loader h6").text("Getting Display Information");
    $.getJSON("/fields", function (data) {
        var fields = {};
        $.each(data, function (i, val) {
            fields[i] = ko.observableArray();
            $.each(val, function (i2, val2) {
                fields[i].push(new Option(val2, i2));
            });
        });
        vm.fields = fields;
        vm.fields['ctaerfcanadian_transfusion_reaction_adverse_event_complete'] = ko.observableArray();
        vm.fields['ctaerfcanadian_transfusion_reaction_adverse_event_complete'].push(new Option('Incomplete',1));
        vm.fields['ctaerfcanadian_transfusion_reaction_adverse_event_complete'].push(new Option('Complete',2))
    }).done(function () {
        $(".loader h6").text("Applying Data Bindings");
        ko.applyBindings(vm);
        $(".loader h6").text("Complete");
        $(".loader").fadeOut("slow");
    });
});

$(window).on('beforeunload', function () {
    if (vm.ignore == false) {
        return 'Please any changes have been saved before leaving this page';
    }
});
