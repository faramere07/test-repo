
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Admin</title>
  
<!-- Fonts -->
        {{-- Laravel Mix - CSS File --}}
        {{-- <link rel="stylesheet" href="{{ mix('css/admin.css') }}"> --}}
        
        <link href="{{ asset('css/font.googleapis.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/fontawesome-free-5.10.2-web/css/all.css') }}">

        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" >
        <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
        <link href="{{ asset('fullcalendar/packages/core/main.css') }}" rel='stylesheet' />
        <link href="{{ asset('fullcalendar/packages/daygrid/main.css') }}" rel='stylesheet' />
        <link href="{{ asset('fullcalendar/packages/timegrid/main.css') }}" rel='stylesheet' />
        <link href="{{ asset('fullcalendar/packages/list/main.css') }}" rel='stylesheet' />
        <link rel="icon" href="{{ asset('MDB/img/mdb-favicon.ico') }}" type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('MDB/css/mdb.min.css') }}">
        <link rel="stylesheet" href="{{ asset('MDB/css/style.css') }}">

        
        <script src="{{ asset('js/jquery-3.4.1.slim.min.js') }}" ></script>
        <script src="{{ asset('js/jquery-3.4.1.min.js') }}" ></script>
        <script src="{{ asset('js/popper.min.js') }}" ></script>
        <script src="{{ asset('js/bootstrap.min.js') }}" ></script>
        <script type="text/javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
        
        <script src="{{ asset('fullcalendar/packages/core/main.js') }}"></script>
        <script src="{{ asset('fullcalendar/packages/interaction/main.js') }}"></script>
        <script src="{{ asset('fullcalendar/packages/daygrid/main.js') }}"></script>
        <script src="{{ asset('fullcalendar/packages/timegrid/main.js') }}"></script>
        <script src="{{ asset('fullcalendar/packages/list/main.js') }}"></script>
        <script src="{{ asset('js/filereader.js-master/filereader.js') }}"></script>
        <script type="text/javascript" src="{{ asset('MDB/js/mdb.min.js') }}"></script>
        
       {{-- Laravel Mix - CSS File --}}
       {{-- <link rel="stylesheet" href="{{ mix('css/admin.css') }}"> --}}

       <style type="text/css">
           .alert {
              position: fixed;
              margin: auto;
              top: 0%;
              left: 0;
              right: 0;
              width: 50%;
              z-index: 9;
            }
            #calendar {
              float: left;
              width: 100%;
            }
            .destroy, .view{
              position: relative;
            }
            .buttonText{
              display: none;
              position: absolute;
              top:-70%;
              right: -30%;
              color:#000;
            }
            .buttonText2{
              display: none;
              position: absolute;
              top:0%;
              right: 30%;
              color:#000;
            }
            .destroy:hover .buttonText{
              display: block;
            }
            .view:hover .buttonText2{
              display: block;
            }
       </style>
</head>
        