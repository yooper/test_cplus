@extends('layouts.app')
 
@section('content')
        <div class="container">             
            <div class="panel panel-primary">
              <div class="panel-heading">Civic Plus Calendar</div>
              <div class="panel-body" >
            {!! $calendar->calendar() !!}
            {!! $calendar->script() !!}
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
    

    
    function eventDialog(event)
    {
    var eventTemplate = `<dl>
  <dt>Title</dt>
  <dd>${event.title}</dd>
  <dt>Start Date</dt>
  <dd>${event.start}</dd>
  <dt>End Date</dt>
  <dd>${event.end}</dd>
  <dt>Description</dt>
  <dd>${event.description}</dd>        
</dl>`;
        
        bootbox.alert(eventTemplate);
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
                                       });
                                       dialog.modal('hide');
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
