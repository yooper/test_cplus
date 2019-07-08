@extends('layouts.app')
 
@section('content')
<ul class="nav nav-pills mb-3 nav-fill" id="pills-tab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="pills-listview-tab" data-toggle="pill" href="#pills-listview" role="tab" aria-controls="pills-listview" aria-selected="false">List View</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
  </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

        <div class="container" id="calendarContainer">             
            <div class="panel panel-primary">
              <div class="panel-heading">Civic Plus Calendar</div>
              <div class="panel-body" >
            {!! $calendar->calendar() !!}
            {!! $calendar->script() !!}
              </div>
            </div> 
        </div>                        
    </div>
  <div class="tab-pane fade" id="pills-listview" role="tabpanel" aria-labelledby="pills-listview-tab">...
      
  
  </div>
  <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
<div class="card" style="width: 30rem;">
  <div class="card-body">
    <h5 class="card-title">Dan Cardin</h5>
    <h6 class="card-subtitle mb-2 text-muted">Software Engineer</h6>
    <p class="card-text">email : dcardin2007@gmail.com<br/>phone : 906.251.0964</p>
    <a href="https://github.com/yooper/" class="card-link" target="_blank">Github</a>
    <a href="https://www.linkedin.com/in/dan-cardin-275a6914/" class="card-link" target="_blank">LinkedIn</a>
  </div>
</div>
  </div>
</div>


<script type="text/javascript">
        
    var formHtml = `<div id="hiddenForm"> 
    
                    <div class="alert alert-warning alert-dismissible fade show" role="alert" id="alerts" style="display:none">
                        <ul id='errorList'></ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                   {!! Form::open(array('route' => 'events.add','method'=>'POST', 'id' => 'eventForm', 'files'=>'false', 'role' => 'form')) !!}
 
                        <div class="row form-group">                              
                            <div class='col-md-6'>                              
                            {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'enter title', 'id'=> 'title']) !!}
                            </div>
                        </div>
                              
                        <div class="row form-group">                              
                            <div class='col-md-6'>                              
                                <div class="input-group date" id="startPicker" data-target-input="nearest">
                                    <input id="startDate" type="text" placeholder='click the calendar' class="form-control datetimepicker-input" data-target="#startPicker"/>
                                    <div class="input-group-append" data-target="#startPicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>                                                    
                            </div>
                        </div>
                                        
                        <div class="row form-group">                              
                            <div class='col-md-6'>                              
                                <div class="input-group date" id="endPicker" data-target-input="nearest">
                                    <input id="endDate" type="text" placeholder='click the calendar' class="form-control datetimepicker-input" data-target="#endPicker"/>
                                    <div class="input-group-append" data-target="#endPicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>                                                    
                            </div>
                        </div>
                              
                        <div class="row form-group">                              
                            <div class='col-md-6'>                              
                                {!! Form::textarea('description',null,['id' => 'description', 'class'=>'form-control', 'placeholder' => 'description', 'rows' => 4, 'cols' => 100]) !!} 
                            </div>
                        </div>                              
                              
                              
                              
                              
                      </div>

                   {!! Form::close() !!}
</div>`;    
    

    // build list view at start up
    $(function() {
        buildListView()
    });

    function buildListView()
    {
        $('#pills-listview').empty();
        let events = $('#calendar-civicPlusCalendar').fullCalendar( 'clientEvents');
        
        events.sort(function(a, b){
             return new Date(b.start) - new Date(a.start);
        });
        
        let html = '<ul class="list-group">';
        html += events.map(function(event){ return '<li class="list-group-item">'+eventTemplate(event)+'</li>' }).join(' ');
        html += '</ul>'    
        $('#pills-listview').html(html);
    }

    function eventTemplate(event)
    {
        var template = `
        <dl>
            <dt>Title</dt>
            <dd>${event.title}</dd>
            <dt>Start Date</dt>
            <dd>${event.start}</dd>
            <dt>End Date</dt>
            <dd>${event.end}</dd>
            <dt>Description</dt>
            <dd>${event.description}</dd>        
        </dl>`;
        return template
    }
    
    function eventDialog(event)
    {                
        bootbox.alert(eventTemplate(event));
    }
    
    function openDialog(date)
    {        
        var dialog = bootbox.dialog({
            title: '',
            message: formHtml,
            size: 'large',
            buttons: {
                cancel: {
                    callback: function(){
                        
                    }
                },                
                add: {
                    label: "Add Event",
                    className: 'btn-info',
                    callback: function(){
                        var form = $('#eventForm');
                        var url = form.attr('action');

                        $.ajax({
                               type: "POST",
                               url: url,
                               data: {
                                    _token: "{!! csrf_token() !!}",
                                    title : $("#title").val(),
                                    startDate : $("#startDate").val(),
                                    endDate : $("#endDate").val(),
                                    description : $("#description").val()
                               }, // serializes the form's elements.
                               success: function(data)
                               {
                                   if(data.success === true)
                                   {
                                       // add the event
                                       $('#calendar-civicPlusCalendar').fullCalendar( 'renderEvent',
                                       { 
                                            title : $("#title").val(), 
                                            start: $("#startDate").val(),
                                            end: $("#endDate").val(),
                                            description : $("#description").val()
                                       }, true);
                                       
                                       dialog.modal('hide');
                                       // re-render the list view
                                       buildListView();
                                   } 
                                   else 
                                   {

                                   }
                               },
                               error : function(data)
                               {
                                   $('#errorList').empty();
                                   $('#alerts').css('display','block');
                                    let obj = data.responseJSON.errors;
                                    Object.keys(obj).forEach(key => {
                                        let value = obj[key][0];
                                        $("#errorList").append($("<li>").text(`Error ${key} - ${value}`));
                                    });
                                    

                               }
                         });
                         return false;
                    }
                }
            }
        });
        
        $(document).on("shown.bs.modal", function (event) {
            $('#startPicker').datetimepicker();
            $('#endPicker').datetimepicker({
                useCurrent: false
            });
            $("#startPicker").on("change.datetimepicker", function (e) {
                $('#endPicker').datetimepicker('minDate', e.date);
            });
            $("#endPicker").on("change.datetimepicker", function (e) {
                $('#startPicker').datetimepicker('maxDate', e.date);
            });            
        });

    }

</script>
    

@endsection
