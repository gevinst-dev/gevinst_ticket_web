@extends('layouts.auth')

@section('page-title')
    {{ __('Ticket') }} - {{$ticket->ticket_id}}
@endsection

@push('css-page')
<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush

@section('style')
    <style>
        @media (max-width: 767px) {
            .auth-layout-wrap .auth-content {
                min-width: 100%;
            }
        }
        @media (min-width: 768px) {
            .auth-layout-wrap .auth-content {
                min-width: 90%;
            }
        }
        @media (min-width: 1024px) {
            .auth-layout-wrap .auth-content {
                min-width: 50%;
            }
        }
    </style>
@endsection

@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
<script>
    if ($(".summernote-simple").length > 0) {
        $('.summernote-simple').summernote({
            dialogsInBody: !0,
            minHeight: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 250,
        });
    }
</script>
<script src="{{ asset('public/custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script>
   function show_toastr(title, message, type) {
       var o, i;
       var icon = '';
       var cls = '';
       if (type == 'success') {
           icon = 'fas fa-check-circle';
           // cls = 'success';
           cls = 'primary';
       } else {
           icon = 'fas fa-times-circle';
           cls = 'danger';
       }

       console.log(type, cls);
       $.notify({
           icon: icon,
           title: " " + title,
           message: message,
           url: ""
       }, {
           element: "body",
           type: cls,
           allow_dismiss: !0,
           placement: {
               from: 'top',
               align: 'right'
           },
           offset: {
               x: 15,
               y: 15
           },
           spacing: 10,
           z_index: 1080,
           delay: 2500,
           timer: 2000,
           url_target: "_blank",
           mouse_over: !1,
           animate: {
               enter: o,
               exit: i
           },
           // danger
           template: '<div class="toast text-white bg-' + cls +
               ' fade show" role="alert" aria-live="assertive" aria-atomic="true">' +
               '<div class="d-flex">' +
               '<div class="toast-body"> ' + message + ' </div>' +
               '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
               '</div>' +
               '</div>'
           // template: '<div class="alert alert-{0} alert-icon alert-group alert-notify" data-notify="container" role="alert"><div class="alert-group-prepend alert-content"><span class="alert-group-icon"><i data-notify="icon"></i></span></div><div class="alert-content"><strong data-notify="title">{1}</strong><div data-notify="message">{2}</div></div><button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
       });
   }
</script>


@endpush

@section('content')

<div class="auth-wrapper auth-v1">
    <div class="bg-auth-side bg-primary"></div>
    <div class="auth-content">

        <div class="row align-items-center justify-content-center text-start">
            <div class="col-xl-12 text-start">
                <div class="mx-3 mx-md-5">
                    <div class="card-header">
                        <h5 class="text-white">{{ __('Ticket') }} - {{$ticket->ticket_id}}</h5>
                    </div>
                </div>

                <div class="card p-4">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-header"><h6>{{$ticket->name}} <small>({{$ticket->created_at->diffForHumans()}})</small></h6></div>
                        <div class="card-body w-100">
                            <div>
                                <p>{!! $ticket->description !!}</p>
                            </div>
                            @php
                                $attachments=json_decode($ticket->attachments);
                            @endphp
                            @if(!is_null($attachments) && count($attachments)>0)
                                <div class="m-1 ml-3">
                                    <b>{{ __('Attachments') }} :</b>
                                    <ul class="list-group list-group-flush">
                                        @foreach($attachments as $index => $attachment)
                                            <li class="list-group-item px-0">
                                                {{$attachment}}<a download="" href="{{ asset(Storage::url('tickets/'.$ticket->ticket_id."/".$attachment)) }}" class="edit-icon py-1 ml-2" title="{{ __('Download') }}"><i class="fa fa-download ms-2"></i></a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    @foreach($ticket->conversions as $conversion)
                        <div class="card mb-3">
                            <div class="card-header"><h6>{{$conversion->replyBy()->name}} <small>({{$conversion->created_at->diffForHumans()}})</small></h6></div>
                            <div class="card-body w-100">
                                <div>{!! $conversion->description !!}</div>
                                @php
                                    $attachments=json_decode($conversion->attachments);
                                @endphp
                                @if(count($attachments))
                                    <div class="m-1">
                                        <b>{{ __('Attachments') }} :</b>
                                        <ul class="list-group list-group-flush">

                                            @foreach($attachments as $index => $attachment)
                                                <li class="list-group-item px-0">
                                                    {{$attachment}}<a download="" href="{{ asset(Storage::url('reply_tickets/'.$ticket->id."/".$attachment))  }}" class="edit-icon py-1 ml-2" title="{{ __('Download') }}"><i class="fa fa-download ms-2"></i></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($ticket->status != 'Closed')
                        <div class="card mb-3">
                            <div class="card-body w-100">
                                <form method="post" action="{{route('home.reply',$ticket->ticket_id)}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="require form-label">{{ __('Description') }}</label>
                                            <textarea name="reply_description" class="form-control summernote-simple {{ $errors->has('reply_description') ? ' is-invalid' : '' }}">{{old('reply_description')}}</textarea>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('reply_description') }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 file-group">
                                            <label class="require form-label">{{ __('Attachments') }}</label>
                                            <label class="form-label"><small>({{__('You can select multiple files')}})</small></label>
                                            <div class="choose-file form-group">
                                                <label for="file" class="form-label">
                                                    <div>{{ __('Choose File Here') }}</div>
                                                    <input type="file" class="form-control {{ $errors->has('reply_attachments') ? 'is-invalid' : '' }}" multiple="" name="reply_attachments[]" id="file" data-filename="multiple_reply_file_selection">
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('reply_attachments') }}
                                                    </div>
                                                </label>
                                                <p class="multiple_reply_file_selection"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <div class="text-center">
                                            <input type="hidden" name="status" value="New Ticket"/>
                                            <button class="btn btn-submit btn-primary btn-block mt-2">{{ __('Submit') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body">
                                <p class="text-blue font-weight-bold text-center mb-0">{{ __('Ticket is closed you cannot replay.') }}</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
    <div id="liveToast" class="toast text-white fade" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
        </div>
    </div>
</div>

@endsection


@push('scripts')
    <script>
        // for Choose file
        $(document).on('change', 'input[type=file]', function () {
            var names = '';
            var files = $('input[type=file]')[0].files;

            for (var i = 0; i < files.length; i++) {
                names += files[i].name + '<br>';
            }
            $('.' + $(this).attr('data-filename')).html(names);
        });
    </script>
@endpush
