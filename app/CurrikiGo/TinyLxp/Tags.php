<?php

namespace App\CurrikiGo\TinyLxp;

use App\Models\Playlist as PlaylistModel;
use App\Models\Activity as ActivityModel;

class Tags
{
    private $lmsSetting;
    private $client;
    private $lmsAuthToken;
    private $existingTagArrayIndex;
    private $type;
    private $existingTags;

    public function __construct($lmsSetting)
    {
        $this->lmsSetting = $lmsSetting;
        $this->existingTagArrayIndex = [];
        $this->existingTags = [];
    }


    public function fetch(PlaylistModel $playlist)
    {
        $this->playlistTags($playlist, 'subjects');
        $this->playlistTags($playlist, 'authorTags');
        $this->playlistTags($playlist, 'educationLevels');
        return $this->existingTags;
    }

    private function playlistTags($playlist, $category)
    {
        $activities = ActivityModel::where('playlist_id', $playlist->id)->with($category)->get();
        foreach ($activities as $activity) {
            foreach ($activity->$category as $item) {
                    $this->existingTagArrayIndex['slug'] =  $item->name;
                    $this->existingTagArrayIndex['name'] = $item->name;
                if ($category == 'educationLevels') {
                    $this->existingTagArrayIndex['domain'] = 'course_category';
                } else {
                    $this->existingTagArrayIndex['domain'] = 'course_tag';
                }
                $this->existingTags[] = $this->existingTagArrayIndex;
            }
        }
    }
}
