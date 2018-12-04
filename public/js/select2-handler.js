var filters = {};
var data = {};
var results = [];

function showFilter() {
    console.log(this.filters);
}

function triggerResetWithoutWidth (arrayOfData) {
    console.log('reset')
    var data = arrayOfData[0];
    var table = arrayOfData[1];
    var element = arrayOfData[2];
    var newElement = $('#'+arrayOfData[1]);
    // console.log(newElement)
    var url = arrayOfData[3];
    var tableColumns = arrayOfData[4];
    var columnDefs = arrayOfData[5];
    var order = arrayOfData[6];

    data.map((id) => {
        $(id).val('').trigger('change');
    });

    this.filters = {};
    if(typeof arrayOfData[8] !== 'undefined') {
        this.filters['date_range'] = moment().format('YYYY-MM-DD')+'|'+moment().format('YYYY-MM-DD');
    }

     // Datatable setup

    if($.fn.dataTable.isDataTable('#'+table)){
        newElement.DataTable().clear();
        newElement.DataTable().destroy();
    }

    // swal({
    //   title: "Please Wait!",
    //   text: "Data in Process, Relax!",
    //   icon: "success",
    //   showCancelButton: false,
    //   showConfirmButton: false
    // });
    
    newElement.dataTable({
        "fnCreatedRow": function (nRow, data) {
            $(nRow).attr('class', data.id);
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: url,
            type: 'POST',
            dataType: 'json',
            error: function (data) {
              swal("Error!", "Failed to load Data!", "error");
            },

            dataSrc: function(result){
                this.data = result.data;
                return result.data;
            },
        },
        // scrollY:        "300px",
        scrollX:        true,
        scrollCollapse: true,
        "bFilter": false,
        "rowId": "id",
        "columns": tableColumns,
        "columnDefs": columnDefs,
        "order": order,
        "autoWidth" : false,
    });

    swal("Reset Filter Done", "Please check the results", "success");
}

// Filtering data without search box
function filteringReportWithoutWidth(arrayOfData) {
    // console.log('filter');
    // console.log(arrayOfData);
    var table = arrayOfData[0];
    var element = arrayOfData[1];
    var newElement = $('#'+arrayOfData[0]);
    var url = arrayOfData[2];
    var tableColumns = arrayOfData[3];
    var columnDefs = arrayOfData[4];
    var order = arrayOfData[5];
    var filter = [];

    this.moreParams = [];
    this.moreParamsPost  = {};
    // console.log('filters:');
    // console.log(this.filters);
    for (filter in this.filters) {
        this.moreParams.push(filter + '=' + this.filters[filter]);
        this.moreParamsPost[filter] = this.filters[filter];
    }
    var self = this;
    // console.log('moreParamsPost:');
    // console.log(self.moreParamsPost);
    $(document).ready(function () {
        // console.log(self.moreParamsPost);
        // console.log(element);
        // console.log(newElement);
        if($.fn.dataTable.isDataTable('#'+table)){
            // console.log('isDataTable');
            newElement.DataTable().clear();
            newElement.DataTable().destroy();
        }

        // swal({
        //   title: "Please Wait!",
        //   text: "Data in Process, Relax!",
        //   icon: "success",
        //   showCancelButton: false,
        //   showConfirmButton: false
        // });

        newElement.dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url + "?" + getParam(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },

                dataSrc: function(result){
                    this.data = result.data;
                    return result.data;
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter": false,
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
            "autoWidth": false
        });
        

        swal("Set Filter Done", "Please check the results", "success");
    })
}

// Reset all filter for search without search box
function triggerResetWithoutSearch (arrayOfData) {
    console.log('reset')
    var data = arrayOfData[0];
    var table = arrayOfData[1];
    var element = arrayOfData[2];
    var newElement = $('#'+arrayOfData[1]);
    // console.log(newElement)
    var url = arrayOfData[3];
    var tableColumns = arrayOfData[4];
    var columnDefs = arrayOfData[5];
    var order = arrayOfData[6];

    data.map((id) => {
        $(id).val('').trigger('change');
    });

    this.filters = {};
    if(typeof arrayOfData[8] !== 'undefined') {
        this.filters['date_range'] = moment().format('YYYY-MM-DD')+'|'+moment().format('YYYY-MM-DD');
    }

     // Datatable setup

    if($.fn.dataTable.isDataTable('#'+table)){
        newElement.DataTable().clear();
        newElement.DataTable().destroy();
    }

    // swal({
    //   title: "Please Wait!",
    //   text: "Data in Process, Relax!",
    //   icon: "success",
    //   showCancelButton: false,
    //   showConfirmButton: false
    // });
    
    newElement.dataTable({
        "fnCreatedRow": function (nRow, data) {
            $(nRow).attr('class', data.id);
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: url + "?" + $("#filterForm").serialize(),
            type: 'POST',
            dataType: 'json',
            error: function (data) {
              swal("Error!", "Failed to load Data!", "error");
            },

            dataSrc: function(result){
                this.data = result.data;
                return result.data;
            },
        },
        // scrollY:        "300px",
        scrollX:        true,
        scrollCollapse: true,
        "bFilter": false,
        "rowId": "id",
        "columns": tableColumns,
        "columnDefs": columnDefs,
        "order": order,
        "ordering": false
    });

    swal("Reset Filter Done", "Please check the results", "success");
}

// Filtering data without search box
function filteringReportWithoutSearch(arrayOfData) {
    // console.log('filter');
    // console.log(arrayOfData);
    var table = arrayOfData[0];
    var element = arrayOfData[1];
    var newElement = $('#'+arrayOfData[0]);
    var url = arrayOfData[2];
    var tableColumns = arrayOfData[3];
    var columnDefs = arrayOfData[4];
    var order = arrayOfData[5];
    var filter = [];

    this.moreParams = [];
    this.moreParamsPost  = {};
    // console.log('filters:');
    // console.log(this.filters);
    for (filter in this.filters) {
        this.moreParams.push(filter + '=' + this.filters[filter]);
        this.moreParamsPost[filter] = this.filters[filter];
    }
    var self = this;
    // console.log('moreParamsPost:');
    // console.log(self.moreParamsPost);
    $(document).ready(function () {
        // console.log(self.moreParamsPost);
        // console.log(element);
        // console.log(newElement);
        if($.fn.dataTable.isDataTable('#'+table)){
            // console.log('testing : isDataTable');
            newElement.DataTable().clear();
            newElement.DataTable().destroy();
        }

        // swal({
        //   title: "Please Wait!",
        //   text: "Data in Process, Relax!",
        //   icon: "success",
        //   showCancelButton: false,
        //   showConfirmButton: false
        // });

        // console.log(tableColumns);
        // return;

        newElement.dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url + "?" + $("#filterForm").serialize(),
                type: 'POST',
                dataType: 'json',
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },

                dataSrc: function(result){
                    this.data = result.data;
                    return result.data;
                },

                success:function(response) {
                  swal("Set Filter Done", "Please check the results", "success");
                  
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "bFilter": false,
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
            "ordering": false
        });
                
    })
}

// Reset all filter for search
function triggerReset (arrayOfData) {
    console.log('reset')
    var data = arrayOfData[0];
    var table = arrayOfData[1];
    var element = arrayOfData[2];
    var newElement = $('#'+arrayOfData[1]);
    // console.log(newElement)
    var url = arrayOfData[3];
    var tableColumns = arrayOfData[4];
    var columnDefs = arrayOfData[5];
    var order = arrayOfData[6];

    data.map((id) => {
        $(id).val('').trigger('change');
    });

    this.filters = {};

     // Datatable setup

    if($.fn.dataTable.isDataTable('#'+table)){
        newElement.DataTable().clear();
        newElement.DataTable().destroy();
    }

    // swal({
    //   title: "Please Wait!",
    //   text: "Data in Process, Relax!",
    //   icon: "success",
    //   showCancelButton: false,
    //   showConfirmButton: false
    // });
    
    newElement.dataTable({
        "fnCreatedRow": function (nRow, data) {
            $(nRow).attr('class', data.id);
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: url,
            type: 'POST',
            dataType: 'json',
            
            error: function (data) {
              swal("Error!", "Failed to load Data!", "error");
            },
        },
        // scrollY:        "300px",
        scrollX:        true,
        scrollCollapse: true,
        "rowId": "id",
        "columns": tableColumns,
        "columnDefs": columnDefs,
        "order": order,
    });

    swal("Reset Filter Done", "Please check the results", "success");
}

// Set the selected value to key in filter
function selected (key, val) {
    this.filters[key] = val;
    console.log(this.filters);
}

// Filtering data
function filteringReport(arrayOfData) {
    // console.log('filter');
    // console.log(arrayOfData);
    var table = arrayOfData[0];
    var element = arrayOfData[1];
    var newElement = $('#'+arrayOfData[0]);
    var url = arrayOfData[2];
    var tableColumns = arrayOfData[3];
    var columnDefs = arrayOfData[4];
    var order = arrayOfData[5];
    var filter = [];

    this.moreParams = [];
    this.moreParamsPost  = {};
    // console.log('filters:');
    // console.log(this.filters);
    for (filter in this.filters) {
        this.moreParams.push(filter + '=' + this.filters[filter]);
        this.moreParamsPost[filter] = this.filters[filter];
    }
    var self = this;
    // console.log('moreParamsPost:');
    // console.log(self.moreParamsPost);
    $(document).ready(function () {
        // console.log(self.moreParamsPost);
        // console.log(element);
        // console.log(newElement);
        if($.fn.dataTable.isDataTable('#'+table)){
            // console.log('isDataTable');
            newElement.DataTable().clear();
            newElement.DataTable().destroy();
        }

        // swal({
        //   title: "Please Wait!",
        //   text: "Data in Process, Relax!",
        //   icon: "success",
        //   showCancelButton: false,
        //   showConfirmButton: false
        // });

        newElement.dataTable({
            "fnCreatedRow": function (nRow, data) {
                $(nRow).attr('class', data.id);
            },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url + "?" + $("#filterForm").serialize(),
                type: 'POST',
                dataType: 'json',
                
                error: function (data) {
                  swal("Error!", "Failed to load Data!", "error");
                },
            },
            scrollX:        true,
            scrollCollapse: true,
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
        });
        

        swal("Set Filter Done", "Please check the results", "success");
    })
}

// Filtering data
function filteringAttendanceReport(arrayOfData) {

    var table = arrayOfData[0];
    var element = arrayOfData[1];
    var url = arrayOfData[2];
    var tableColumns = arrayOfData[3];
    var columnDefs = arrayOfData[4];
    var order = arrayOfData[5];

    // console.log(tableColumns);

    this.moreParams = [];
    this.moreParamsPost  = {};

    for (filter in this.filters) {
        this.moreParams.push(filter + '=' + this.filters[filter]);
        this.moreParamsPost[filter] = this.filters[filter];
    }

    var self = this;
    $(document).ready(function () {

        if($.fn.dataTable.isDataTable('#'+table)){
            // element.DataTable().clear();
            element.DataTable().destroy();
        }

        element.dataTable({
            "fnCreatedRow": function( nRow, data ) {
                $(nRow).attr('class', data.id);
            },
            // "scrollY":        "300px", 
                "scrollX":        true, 
                "scrollCollapse": true, 
                "paging":         true, 
                "fixedColumns":   { 
                    "leftColumns": "4",
                    // {{--"rightColumns": 1 --}}
                },
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: url,
                data: self.moreParamsPost,
                type: 'POST',
                dataType: 'json',
                dataSrc: function(result){

                    if(typeof arrayOfData[6] !== 'undefined') {
                        // export option exist

                        var count = result.data.length;

                        if(count > 0){
                            $(arrayOfData[6]).removeAttr('disabled');
                        }else{
                            $(arrayOfData[6]).attr('disabled','disabled');
                        }
                    }

                    data = result.data;
                    return result.data;
                }
            },
            "rowId": "id",
            "columns": tableColumns,
            "columnDefs": columnDefs,
            "order": order,
        })
    })
}

// Set options
function setOptions (url, placeholder, data, processResults, parent = '') {
    return {
        ajax: {
            url: url,
            method: 'POST',
            dataType: 'json',
            delay: 250,
            data: data,
            processResults: processResults
        },   
        // minimumInputLength: 2,     
        width: '100%',
        placeholder: placeholder,
        // dropdownParent: parent,
    }
}

// Filter data method
function filterData (search, term) {

    // Check if term is ""
    (term == "" || term == null) ? term = "all" : term = term;

    var results = {};
    if ($.isEmptyObject(filters)) {

        // Check search term is array or string
        if(!$.isArray(search)){

            return {
                [search]: term
            }
        }

        search.forEach(function(item) {
            results[item] = term
        });

        // console.log('result-search');
        console.log(results);

        return results;
    }

    for (var filter in filters) {
        results[filter] = filters[filter];        
    }

    // Check search term is array or string
    if(!$.isArray(search)){
        results[search] = term
    }else{
        search.forEach(function(item) {
            results[item] = term
        });
    }

    // console.log('results');
    console.log(results);

    return results;
}

// Set select2 for PATCH METHOD
function setSelect2IfPatch(element, id, text){

    if($('input[name=_method]').val() == "PATCH"){

        element.select2("trigger", "select", {
            data: { id: id, text: text }
        });

        // Remove validation of success
        element.closest('.form-group').removeClass('has-success');

        var span = element.parent('.input-group').children('.input-group-addon');
        span.addClass('display-hide');

        // Remove focus from selection
        element.next().removeClass('select2-container--focus');
        $(".select2-container--focus").removeClass("select2-container--focus");

        // Disable select2 focus
        $('.select2-search input').prop('focus',false);
        window.scrollTo(0, 0);
        $('html, body').scrollTop();
    }

}

// Set select2 for PATCH METHOD with No Out Focus
function setSelect2IfPatch2(element, id, text){

    // if($('input[name=_method]').val() == "PATCH"){

        element.select2("trigger", "select", {
            data: { id: id, text: text }
        });

        // Remove validation of success
        element.closest('.form-group').removeClass('has-success');

        var span = element.parent('.input-group').children('.input-group-addon');
        span.addClass('display-hide');

    // }

}

// Set select2 for PATCH METHOD => FOR MODALS
function setSelect2IfPatchModal(element, id, text){

    element.select2("trigger", "select", {
        data: { id: id, text: text }
    });

    // Remove validation of success
    element.closest('.form-group').removeClass('has-success');

    var span = element.parent('.input-group').children('.input-group-addon');
    span.addClass('display-hide');

    // Remove focus from selection
    element.next().removeClass('select2-container--focus');

}

// Reset select2
function select2Reset(element){

    element.select2('val','All');

    // Remove validation of success
    element.closest('.form-group').removeClass('has-success');

    var span = element.parent('.input-group').children('.input-group-addon');
    span.addClass('display-hide');

    // Remove focus from selection
    element.next().removeClass('select2-container--focus');

}

/*
 * Select2 validation
 *
 */ 

window.select2Change = function(element, formParam){

    // harus cek di html nya (harus pake attr "required")
    if(element.prop('required')) {

        var form = formParam;
        var errorAlert = $('.alert-danger', form);
        var successAlert = $('.alert-success', form);

        // set success class to the control group
        element.closest('.form-group').removeClass('has-error').addClass('has-success');

        // For select2 option
        var span = element.parent('.input-group').children('.input-group-addon');
        span.removeClass('display-hide');

        var spanIcon = $(span).children('i');
        spanIcon.removeClass('fa-warning').addClass("fa-check");
        spanIcon.removeClass('font-red').addClass("font-green");
        spanIcon.attr("data-original-title", "");

        // Check if all requirement valid and show success text
        if(errorAlert.is(":visible") || successAlert.is(":visible")){
            var errors = 0;
            form.each(function(){
                if($(this).find('.form-group').hasClass('has-error')){
                    errors += 1;
                } 
            });            

            if(errors == 0){ 
                successAlert.show();
                errorAlert.hide();
            }else{
                successAlert.hide();
                errorAlert.show();
            }
        }

    }
}