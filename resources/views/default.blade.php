<!-- Copyright (c) Microsoft Corporation.
     Licensed under the MIT License. -->

<!-- <WelcomeSnippet> -->
@extends('layout')

@section('content')
<h3>Teams Manager
@if($mode === "create") 
      <a href="{{ link2('teams/') }}" class="btn btn-warning btn-sm" style="vertical-align:top">Go Back</a>
      @endif</h3>

<div class="container jumbotron" id="teams_pane">
  <div class="row">
    
    <div class="col-lg-4 col-md-6">
      @if(gettype($teams) === "array")
      <h5>Your Team Memberships</h5>
      @if(count($teams) === 0)
      <p><em>Unfortunately, you are not a part of any teams! You can create one here for yourself and others, however.</em><br><br>
      <a href="{{ link2('teams/create') }}" class="btn btn-primary">Create New Team!</a>
      </p>
      @else
      <ul id="team_list" class="nav flex-column nav-pills">
        

        @for($i = count($teams) - 1; $i >= 0; $i--)
        @php 
        $team = $teams[$i]
        @endphp

          <li class="nav-item">
            <a class="nav-link{{ ($request->segment(2) === $team->getProperties()['id'].'') ? ' active' : '' }}" href="{{ link2('teams/'.$team->getProperties()['id']) }}">{{ $team->getProperties()['displayName'] }}</a>
          </li>
        @endfor

      </ul>
      @endif
      <a href="{{ link2('teams/create') }}" class="btn btn-primary">Create New Team!</a>
      @endif
      
    </div>

    <div class="col-lg-8 col-md-6">
      @if($mode === "create")
      <form method="POST" action="{{link2('create_team')}}">
        @csrf
      <h5>Create New Team</h5>
      <div class="form-group">
        <label>Team Name</label>
        <input type="text" class="form-control" name="team_name" placeholder="Team Name">
      </div>
      <div class="form-group">
        <label>Team Description</label>
        <textarea class="form-control" name="team_description" placeholder="Team Description"></textarea>
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-success" value="Create New Team!"></div>
      </div>
      </form>
      @endif

      @if($mode === "dashboard")
      <h5>Information</h5>
      <p>
        Hello, user! Feel free to look here to find any information regarding teams that you are signed up on.
      </p>

      <p>Here are some other links.</p>

      <ul>
        <li><a href="{{ link2('teams/create') }}">Create New Team</a></li>
      </ul>

      @endif

      @if($mode === "submitted")

      <h5>Notice</h5>
      <p>Please Wait. Creating Team...</p>

      <script type="text/javascript">
          setTimeout(function(){
            window.location.assign("{{ link2('teams/') }}");
          },5000);
      </script>

      @endif

      @if($mode === "view team")

      <h3><b>Viewing Team:</b> {{ $teamInfo['displayName'] }}</h3>

      <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <table class="table table-sm table-striped" id="team_info_table">
                <thead>
                  <tr>
                    <th>Team Property</th>
                    <th>Detail</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th>Team Name</th>
                    <td>
                      {{ $teamInfo['displayName'] }}
                    </td>
                  </tr>
                  <tr>
                    <th>Team Privacy</th>
                    <td>
                      {{ $teamInfo['visibility'] }}
                    </td>
                  </tr>
                  <tr>
                    <th>Team Status</th>
                    <td>
                      {!! !$teamInfo['isArchived'] ? '<b>Active</b>' : '<em>Inactive</em>' !!}
                    </td>
                  </tr>
                  <tr>
                    <th>Meeting Links</th>
                    <td id="meeting_links">
                      @if(count($meetingLinks) > 0)
                        <ul class="nav flex-column nav-pills" id="meetings">
                        @foreach($meetingLinks as $link)
                          <li class="nav-item">
                            <a class="nav-link" href="{{ $link->meeting_url }}" target="_blank"> {{ $link->name }}</a>
                          </li>
                        @endforeach
                        </ul>
                      @else
                        <p><em>None Currently Exist.</em></p>

                        
                      @endif
                      <p><button id="make_meeting_link" class="btn btn-dark btn-sm" team_id="{{ $teamInfo['id'] }}">Create New Meeting!</button></p>

                    </td>
                  </tr>
                  <tr>
                    <th>Team URL (Microsoft)</th>
                    <td><a href="{{ $teamInfo['webUrl'] }}" target="_blank">{{ $teamInfo['webUrl'] }}</a></td>
                  </tr>
                  <tr>
                    <th>Team URL (Home)</th>
                    <td>
                      <a href="{{ link2('teams/'.$teamInfo['id']) }}">{{ link2('teams/'.$teamInfo['id']) }}</a>
                    </td>
                  </tr>
                  <tr>
                    <th>Date Created</th>
                    <td>
                      {{ $teamInfo['createdDateTime'] }}
                    </td>
                  </tr>
                  
                </tbody>
              </table> 
            </div>
            <?php /* <div class="col-lg-5">
              <h5>Members</h5>
              <div id="member_list">
                @foreach($memberList as $member)

                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">{{ $member['surname'] }}, {{ $member['givenName'] }}</h5>
                      <p class="card-text"><b>Email:</b> {{ $member['mail'] }}</p>
                      <a href="{{ $member['id'] }}" class="btn btn-primary btn-sm">Edit Member's Details</a>
                    </div>
                  </div>

                @endforeach
              </div>
            </div> */ ?>
          </div>
        
      </div>

      @endif
    </div>

  </div>
</div>

@endsection
<!-- </WelcomeSnippet> -->
