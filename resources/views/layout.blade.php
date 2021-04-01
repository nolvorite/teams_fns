<!-- Copyright (c) Microsoft Corporation.
     Licensed under the MIT License. -->

<!-- <LayoutSnippet> -->
<!DOCTYPE html>
<html>
  <head>
    <title>{{ env('app_name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
      integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
      crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
    <link rel="stylesheet" href="{{ asset2('/css/app.css') }}">
    <style type="text/css">
      #teams_pane{padding:2rem;}
      label{font-weight:bold;}
      textarea.form-control{min-height:200px;}
      ul#team_list {
          max-height: 300px;
          overflow: auto;
          display: block;
      }

      #team_list .nav-item {
          display: block;
          width: 100%;
      }
      td a {
          max-width: 200px;
          overflow: hidden;
          text-overflow: ellipsis;
          display: inline-block;
      }

      td a.nav-link {
          max-width: unset;
      }

      table td a:visited {
          color: #333;
          opacity: .5!important;
      }
    </style>
  </head>

  <body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <div class="container">
        <a href="{{ link2('') }}" class="navbar-brand">{{ env('app_name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a href="{{ link2('') }}" class="nav-link {{$_SERVER['REQUEST_URI'] == '' ? ' active' : ''}}">Home</a>
            </li>
            <li class="nav-item">
              <a href="{{ link2('teams') }}" class="nav-link {{$_SERVER['REQUEST_URI'] == 'teams' ? ' active' : ''}}">Teams</a>
            </li>
          </ul>
          <ul class="navbar-nav justify-content-end">
            @if(isset($userName))
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                  aria-haspopup="true" aria-expanded="false">
                  @if(isset($user_avatar))
                    <img src="{{ $user_avatar }}" class="rounded-circle align-self-center mr-2" style="width: 32px;">
                  @else
                    <i class="far fa-user-circle fa-lg rounded-circle align-self-center mr-2" style="width: 32px;"></i>
                  @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                  <h5 class="dropdown-item-text mb-0">{{ $userName }}</h5>
                  <p class="dropdown-item-text text-muted mb-0">{{ $userEmail }}</p>
                  <div class="dropdown-divider"></div>
                  <a href="{{ link2('signout') }}" class="dropdown-item">Sign Out</a>
                </div>
              </li>
            @else
              <li class="nav-item">
                <a href="{{ link2('signin') }}" class="nav-link">Sign In</a>
              </li>
            @endif
          </ul>
        </div>
      </div>
    </nav>
    <main role="main" class="container">
      @if(session('error'))
        <div class="alert alert-danger" role="alert">
          <p class="mb-3">{{ session('error') }}</p>
          @if(session('errorDetail'))
            <pre class="alert-pre border bg-light p-2"><code>{{ session('errorDetail') }}</code></pre>
          @endif
        </div>
      @endif

      @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
      integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
      crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
      integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
      crossorigin="anonymous"></script>

      <script type="text/javascript">
        siteUrl = "{{ link2('') }}";
        inProcess = false;
      </script>

      <script type="text/javascript">
        $(document).ready(function(){
            window._token = '{{ csrf_token() }}';

            $("#make_meeting_link").on("click",function(event){
              event.preventDefault();

              if(!inProcess){

                  teamId = $(this).attr("team_id");

                  $.post(siteUrl+"make_meeting_link",{_token: window._token, team_id: teamId},function(results){

                    if(results.status){

                      alert("Successfully created new meeting link.");
                      window.location.reload();

                    }else{
                      alert("Failed to create new meeting link.");
                    }

                    inProcess = false;

                  },"json").fail(function(){
                    inProcess = false;
                  });

                  inProcess = true;

              }

              


            });

        });
      </script>
  </body>
</html>
<!-- </LayoutSnippet> -->
