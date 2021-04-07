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
        

        $isDefaultPage = $request->segment(1) === "default";

        if(!$isDefaultPage){
          if(session('userName') === null){
              exit;
          }
        }

        $currentMode = $isDefaultPage ? "default" : "teams";

          $viewData = $this->loadViewData($request);

          

          $tokenCache = new TokenCache();
          $accessToken = $tokenCache->getAccessToken();

         
          $graph = new Graph();

          if($isDefaultPage){
            $accessToken = $this->getAccessTokenForDefaultVisits();
          }

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

          $viewData['current_mode'] = $currentMode;

          if($isDefaultPage){
            //get user's id

            $id = 'e8d2f65b-8daa-4db8-9281-e23a035b81cb';

          }else{
            $id = session('id');
          }



          $viewData['teams'] = $graph->createRequest('GET', '/users/'.$id.'/joinedTeams')
            ->attachBody(['$orderBy' => 'dateCreated asc'])
            ->setReturnType(Model\Team::class)
            ->execute();

          return view('teams', $viewData);
    }

    public function getAccessTokenForDefaultVisits(){

      $viewData = $this->loadViewData();

      $clientId = 'dcf8d8d1-bcb7-48ee-a763-6d5d17994554';
      $clientSecret = 'YyBKN.O2OgcDb_Ef2y~~59OXV7I746vvkn';
      $tenantId = 'bf9a66e2-8278-4c86-823f-edaf5c8c3429';

       $guzzle = new \GuzzleHttp\Client();
        $url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token?api-version=1.0';
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'client_credentials',
            ],
        ])->getBody()->getContents());

        $accessToken = $token->access_token;

      return $accessToken;

    }

    public function makeMeetingLink(Request $request){
        $result = ['status' => false];
        $isDefaultMode = $request->all()['mode'] === 'default';
        if(session('userName') !== null || $isDefaultMode){

            $tokenCache = new TokenCache();
            $accessToken = $tokenCache->getAccessToken();
            $graph = new Graph();

            $viewData = $this->loadViewData($request);

            $userId = session('id');

            if($isDefaultMode){
                $accessToken = $this->getAccessTokenForDefaultVisits();
            }

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

            $name = $request->name !== null ? $request->name : 'Meeting Link ('. $forTitleFormat .')';
            $desc = $request->desc."";

            $body = json_encode([
              'subject' => $name
            ]);

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
                'name' => $name,
                'desc' => $desc
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
          $template = "https://graph.microsoft.com/v1.0/teamsTemplates('standard')";

          if($request->all()['current_mode'] === 'default'){
            $accessToken = $this->getAccessTokenForDefaultVisits();
            $template = "https://graph.microsoft.com/v1.0/teamsTemplates('educationClass')";
          }

          $graph->setAccessToken($accessToken);

          $date = new \DateTimeImmutable('@'.time());

          $formatted = $date->format('F j, Y, g:i A');



          $dataToSend = [
            'displayName' => $request->team_name . " (".$formatted.")",
            'description' => $request->team_description,
            "template@odata.bind" => $template
          ];

          $viewData['teams'] = $graph->createRequest('POST', '/teams/')->attachBody($dataToSend)->setReturnType(null)
            ->execute();

          $viewData['current_mode'] = $request->segment(1) === "default" ? "default" : "teams";

          

          $viewData['mode'] = "submitted";

          return view('teams', $viewData);

        }else{
            return redirect('/');
        }

        
    }
}