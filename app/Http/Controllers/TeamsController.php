<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;

use Illuminate\Support\Facades\DB as DB;

class TeamsController extends Controller
{


    public function index(Request $request){
        if(session('userName') === null){
            exit;
        }

          $viewData = $this->loadViewData($request);

          $graph = new Graph();

          $tokenCache = new TokenCache();
          $accessToken = $tokenCache->getAccessToken();

          $graph->setAccessToken($accessToken);

          switch($request->segment(2)){
            case "create":
              $viewData['mode'] = "create";
            break;
            default:
              if($request->segment(2) === null){
                $viewData['mode'] = "dashboard";
              }else{
                $viewData['mode'] = "view team";

                $teamId = $request->segment(2);

                $viewData['teamInfo'] = $graph->createRequest('GET', '/teams/'.$teamId)->setReturnType(null)
              ->execute()->getBody();

                $viewData['meetingLinks'] = DB::table('meeting_links')->where("team_id",$teamId)->get();


              


              }
            break;
          }

          $viewData['teams'] = $graph->createRequest('GET', '/users/'.session('id').'/joinedTeams')
            ->attachBody(['$orderBy' => 'dateCreated asc'])
            ->setReturnType(Model\Team::class)
            ->execute();

          return view('teams', $viewData);
        
        
    }

    public function makeMeetingLink(Request $request){
        $result = ['status' => false];
        if(session('userName') !== null){

            $tokenCache = new TokenCache();
            $accessToken = $tokenCache->getAccessToken();
            $graph = new Graph();

            $viewData = $this->loadViewData($request);

            $userId = session('id');

            $graph->setAccessToken($accessToken);

            $date = new \DateTimeImmutable('@'.time());
            $expires = new \DateTimeImmutable('@'.(time()+3600));

            $format = $date->format('Y-m-d')."T".$date->format('h:i:s');
            $expiresFormat = $expires->format('Y-m-d')."T".$expires->format('h:i:s');

            $date = new \DateTimeImmutable('@'.time());

            $forTitleFormat = $date->format('F j, Y, g:i A');

            // $data = [
            //     'startDateTime' => $format,
            //     'endDateTime' => $expiresFormat,
            //     'subject' => 'Meeting Link ('. $forTitleFormat .')'
            // ];

            // $data = json_encode($data);

            //as it turns out, attach body is the only one that works lol

            $dt = $graph->createRequest('POST', '/users/'. $userId .'/onlineMeetings')->attachBody('{}')->setReturnType(null)
            ->execute()->getBody();

            //meeting links set

            $meetingLinksCheck = DB::table('meeting_links')->where("team_id",$request->team_id)->get();

            if(count($meetingLinksCheck) > 3){
                $deleteMeetingLinks = DB::table('meeting_links')->where("team_id",$request->team_id)->limit(1)->delete();
            }

            DB::table("meeting_links")->insert([
                'meeting_url' => $dt['joinUrl'],
                'team_id' => $request->team_id,
                'name' => 'Meeting Link ('. $forTitleFormat .')'
            ]);

            $result['status'] = true;

        }

        return response()->json($result);

    }

    public function createTeam(Request $request){


        if($_SERVER['REQUEST_METHOD'] === "POST"){

          $tokenCache = new TokenCache();
          $accessToken = $tokenCache->getAccessToken();
          $graph = new Graph();

          $viewData = $this->loadViewData($request);

          $graph->setAccessToken($accessToken);

          $date = new \DateTimeImmutable('@'.time());

          $formatted = $date->format('F j, Y, g:i A');

          $dataToSend = [
            'displayName' => $request->team_name . " (".$formatted.")",
            'description' => $request->team_description,
            "template@odata.bind" => "https://graph.microsoft.com/v1.0/teamsTemplates('standard')"
          ];

          $viewData['teams'] = $graph->createRequest('POST', '/teams/')->attachBody($dataToSend)->setReturnType(null)
            ->execute();

          

          $viewData['mode'] = "submitted";

          return view('teams', $viewData);

        }else{
            return redirect('/');
        }

        
    }
}