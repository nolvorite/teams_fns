<!-- Copyright (c) Microsoft Corporation.
     Licensed under the MIT License. -->

<!-- <WelcomeSnippet> -->
@extends('layout')

@section('content')
<div class="jumbotron">
  @if(isset($userName))
  <h1>Microsoft Teams Test Panel</h1>
  <p class="lead">
  	This is the admin panel.
  </p>
  @else
  <h1>Microsoft Teams Test Panel (Guest)</h1>
  <p class="lead">
  Hello, Guest! Please Log In To Continue.
  </p>
  @endif
</div>
@endsection
<!-- </WelcomeSnippet> -->
