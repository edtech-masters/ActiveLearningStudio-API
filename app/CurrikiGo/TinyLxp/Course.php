<?php

namespace App\CurrikiGo\TinyLxp;

use App\Models\Playlist as PlaylistModel;
use GuzzleHttp;
use App\CurrikiGo\TinyLxp\Tags;
use App\CurrikiGo\TinyLxp\Lesson;

class Course
{
    private $lmsSetting;
    private $client;
    private $lmsAuthToken;
    private $lessonObj;

    public function __construct($lmsSetting)
    {
        $this->lmsSetting = $lmsSetting;
        $this->client = new GuzzleHttp\Client();
        $this->lmsAuthToken = base64_encode($lmsSetting->lms_login_id . ":" . $lmsSetting->lms_access_token);
        $this->lessonObj = new Lesson($lmsSetting);
    }

    public function send(PlaylistModel $playlist)
    {
        $lmsHost = $this->lmsSetting->lms_url;
        $webServiceURL = $lmsHost . "/wp-json/learnpress/v1/course/create"; // web service endpoint
        // $requestParams = [
        //     "post_title" => $playlist->project->name,
        //     "status" => "publish",
        //     "content" => $playlist->project->description,
        //     "post_type" => "lp_course",
        //     'post_meta' => [ 'key' => 'lti_content_id', 'value' => $playlist->project->id],
        //     'terms' => $this->getCourseCategoryAndTags($playlist),
        //     'sections' => $this->lessonObj->playListsActivityFetchLoop($playlist) // section must be attached -> loop run on sections
        // ];
        $fileName = basename(parse_url($playlist->project->thumb_url, PHP_URL_PATH));//pexels-photo-593158.jpeg
        $thumbnailData = [
            'name'     => ((@file_get_contents($playlist->project->thumb_url) == '') || ($fileName == 'pexels-photo-593158.jpeg')) ? 'no_thumbnail' : 'course_thumbnail',
            'contents' => @file_get_contents($playlist->project->thumb_url) != '' ? fopen($playlist->project->thumb_url, 'r') : '',
            'filename' => $fileName,
        ];

        $response = $this->client->request('POST', $webServiceURL, [
            'headers' => [
                'Authorization' => "Basic  " . $this->lmsAuthToken,
                'Accept'        => 'application/json',
            ],
            'verify' => false, // Disable SSL certificate verification
            'multipart' => [
                [ 'name'     => 'post_title', 'contents' => $playlist->project->name ],
                [ 'name'     => 'status', 'contents' => 'publish' ],
                [ 'name'     => 'content', 'contents' => $playlist->project->description ],
                [ 'name'     => 'post_type', 'contents' => 'lp_course' ],
                [ 'name'     => 'post_meta', 'contents' => json_encode([ 'key' => 'lti_content_id', 'value' => $playlist->project->id]) ],
                [ 'name'     => 'terms', 'contents' => $this->getCourseCategoryAndTags($playlist) ],
                [ 'name'     => 'sections', 'contents' => $this->lessonObj->playListsActivityFetchLoop($playlist) /* section must be attached -> loop run on sections */ ],
                $thumbnailData,
            ],
        ]);
        // $response = $this->client->request('POST', $webServiceURL, [
        //     'headers' => [
        //         'Authorization' => "Basic  " . $this->lmsAuthToken
        //     ],
        //     'verify' => false,  // Disable SSL certificate verification,
        //     'json' => $requestParams
        // ]);
        return $response;
    }

    public function update(PlaylistModel $playlist, $projectId)
    {        
        $lmsHost = $this->lmsSetting->lms_url;
        $webServiceURL = $lmsHost . "/wp-json/learnpress/v1/course/update/" . $projectId;
        $requestParams = [
            "post_title" => $playlist->project->name,
            "content" => $playlist->project->description,
            'terms' => $this->getCourseCategoryAndTags($playlist),
            'sections' => $this->lessonObj->playListsActivityFetchLoop($playlist) // section lazmi lagana hy kiu k pahla loop sections ka hi chaly ga
        ];

        $response = $this->client->request('POST', $webServiceURL, [
        'headers' => [
            'Authorization' => "Basic  " . $this->lmsAuthToken
        ],
        'verify' => false,  // Disable SSL certificate verification,
        'json' => $requestParams
        ]);
        return $response;
    }

    public function fetch(PlaylistModel $playlist)
    {        
        $lmsHost = $this->lmsSetting->lms_url;
        $webServiceURL = $lmsHost . "/wp-json/learnpress/v1/course/check-course?meta_key=lti_content_id&meta_value=". $playlist->project->id;
        
        $response = $this->client->request('GET', $webServiceURL, [
            'headers' => [
                'Authorization' => "Basic " . $this->lmsAuthToken,
            ],
            'verify' => false,  // Disable SSL certificate verification
        ]);
        return $response;
    }


    public function getCourseCategoryAndTags($playlist) : string {
        $tagsObj = new Tags($this->lmsSetting);
        $result = $tagsObj->fetch($playlist);
        return json_encode($result);
    }

}
