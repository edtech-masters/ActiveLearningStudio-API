<?php

namespace App\CurrikiGo\TinyLxp;

use App\Models\Playlist as PlaylistModel;
use App\Models\Project;
use App\CurrikiGo\TinyLxp\JWTAuth;

class Lesson
{
    private $lmsSetting;
    private $client;
    // private $lmsAuthToken;

    public function __construct($lmsSetting)
    {
        $this->lmsSetting = $lmsSetting;
        $this->client = new \GuzzleHttp\Client();
        // $this->lmsAuthToken = $lmsSetting->lms_access_token;
    }

    public function send(PlaylistModel $playlist )
    { 
        $response = null;
        $jwtObj = new JWTAuth($this->lmsSetting);
        $lmsHost = $this->lmsSetting->lms_url;
        $token = $jwtObj->createToken();
        $webServiceURL = $lmsHost . "/wp-json/learnpress/v1/section/create/";
        $lessonArray = $this->playListsActivityFetchLoop($playlist);
        $response = $this->client->request('POST', $webServiceURL, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'verify' => false,  // Disable SSL certificate verification,
            'json' => $lessonArray
        ]);
        return $response;
    }

    public function fetch(Project $project)
    {
        $jwtObj = new JWTAuth($this->lmsSetting);
        $lmsHost = $this->lmsSetting->lms_url;
        $token = $jwtObj->createToken();
        $webServiceURL = $lmsHost . "/wp-json/learnpress/v1/lesson/check-lesson?meta_key=lti_content_id&meta_value=". $project->id;
        $response = $this->client->request('GET', $webServiceURL, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);
        return $response;
    }

    public function playListsActivityFetchLoop($playlist) : string {
        $lessonArray['section_name'] = $playlist["title"];
        $lessonArray['section_description'] = '';
        foreach($playlist->activities->sortBy('order')->toArray() as $activity) {
            $lesson = [];
            $lesson['post_title'] = $activity["title"];
            $lesson['post_content'] = "[selected_activity]";
            $lesson['post_type'] = 'lp_lesson';
            $lesson['postmeta'][] = [ 'key' => 'lti_content_id', 'value' => $playlist->id];
            // $lesson['postmeta'][] = [ 'key' => 'tl_course_id', 'value' => $course_id];
            $lesson['postmeta'][] = [ 'key' => 'lti_tool_url', 'value' => config('constants.curriki-tsugi-host') . "?activity=" . $activity["id"]];
            $lesson['postmeta'][] = [ 'key' => 'lti_tool_code', 'value' => $this->lmsSetting->lti_client_id];
            $lesson['postmeta'][] = [ 'key' => 'lti_custom_attr', 'value' => 'custom=activity='. $activity["id"]];
            $lesson['postmeta'][] = [ 'key' => 'lti_content_title', 'value' => $playlist->title];
            $lesson['postmeta'][] = [ 'key' => 'lti_post_attr_id', 'value' => uniqid()];
            $lesson['postmeta'][] = [ 'key' => 'lti_course_id', 'value' => $playlist->project->id];
            $lesson['postmeta'][] = [ 'key' => '_lp_duration', 'value' => '4 minute'];
            // $lesson['postmeta'][] = [ 'key' => '_lp_preview', 'value' => 'yes'];
            $lessonArray['section_items'][] = $lesson;
        }
        return json_encode($lessonArray);
    }
}
