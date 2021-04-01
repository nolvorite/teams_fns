<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@welcome');
Route::get('/signin', 'AuthController@signin');
Route::get('/callback', 'AuthController@callback');
Route::get('/signout', 'AuthController@signout');
Route::get('/teams', 'TeamsController@index');

Route::get('/teams/{teamId}', 'TeamsController@index');

Route::post('/create_team', 'TeamsController@createTeam');

Route::post('/make_meeting_link', 'TeamsController@makeMeetingLink');

Route::get('/teams/create', 'TeamsController@index');


Route::get('/calendar', 'CalendarController@calendar');
