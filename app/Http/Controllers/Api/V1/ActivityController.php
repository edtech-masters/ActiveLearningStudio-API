<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ActivityUpdatedEvent;
use App\Events\PlaylistUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ActivityCreateRequest;
use App\Http\Requests\V1\ActivityEditRequest;
use App\Http\Resources\V1\ActivityResource;
use App\Http\Resources\V1\ActivityDetailResource;
use App\Http\Resources\V1\H5pActivityResource;
use App\Http\Resources\V1\PlaylistResource;
use App\Jobs\CloneActivity;
use App\Models\Activity;
use App\Models\ActivityItem;
use App\Models\Pivots\TeamProjectUser;
use App\Models\Playlist;
use App\Models\Project;
use App\Models\Team;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\ActivityItem\ActivityItemRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Repositories\H5pContent\H5pContentRepositoryInterface;
use Djoudi\LaravelH5p\Eloquents\H5pContent;
use Djoudi\LaravelH5p\Events\H5pEvent;
use Djoudi\LaravelH5p\Exceptions\H5PException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use H5pCore;
use App\Models\Organization;

/**
 * @group 5. Activity
 *
 * APIs for activity management
 */
class ActivityController extends Controller
{

    private $playlistRepository;
    private $activityRepository;
    private $h5pContentRepository;

    /**
     * ActivityController constructor.
     *
     * @param PlaylistRepositoryInterface $playlistRepository
     * @param ActivityRepositoryInterface $activityRepository
     * @param H5pContentRepositoryInterface $h5pContentRepository
     * @param ActivityItemRepositoryInterface $activityItemRepository
     */
    public function __construct(
        PlaylistRepositoryInterface $playlistRepository,
        ActivityRepositoryInterface $activityRepository,
        H5pContentRepositoryInterface $h5pContentRepository,
        ActivityItemRepositoryInterface $activityItemRepository
    )
    {
        $this->playlistRepository = $playlistRepository;
        $this->activityRepository = $activityRepository;
        $this->h5pContentRepository = $h5pContentRepository;
        $this->activityItemRepository = $activityItemRepository;

        // $this->authorizeResource(Activity::class, 'activity');
    }

    /**
     * Get Activities
     *
     * Get a list of activities
     *
     * @urlParam playlist required The Id of a playlist Example: 1
     *
     * @responseFile responses/activity/activities.json
     *
     * @param Playlist $playlist
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Playlist $playlist)
    {
        $this->authorize('viewAny', [Activity::class, $playlist->project->organization]);

        return response([
            'activities' => ActivityResource::collection($playlist->activities),
        ], 200);
    }

    /**
     * Upload Activity thumbnail
     *
     * Upload thumbnail image for a activity
     *
     * @bodyParam thumb image required Thumbnail image to upload Example: (binary)
     *
     * @response {
     *   "thumbUrl": "/storage/activities/1fqwe2f65ewf465qwe46weef5w5eqwq.png"
     * }
     *
     * @response 400 {
     *   "errors": [
     *     "Invalid image."
     *   ]
     * }
     *
     * @param Request $request
     * @return Response
     */
    public function uploadThumb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'thumb' => 'required|image|max:102400',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => ['Invalid image.']
            ], 400);
        }

        $path = $request->file('thumb')->store('/public/activities');

        return response([
            'thumbUrl' => Storage::url($path),
        ], 200);
    }

    /**
     * Create Activity
     *
     * Create a new activity.
     *
     * @urlParam playlist required The Id of a playlist Example: 1
     * @bodyParam title string required The title of a activity Example: Science of Golf: Why Balls Have Dimples
     * @bodyParam type string required The type of a activity Example: h5p
     * @bodyParam content string required The content of a activity Example:
     * @bodyParam order int The order number of a activity Example: 2
     * @bodyParam h5p_content_id int The Id of H5p content Example: 59
     * @bodyParam thumb_url string The image url of thumbnail Example: null
     * @bodyParam subject_id array The Ids of a subject Example: [1, 2]
     * @bodyParam education_level_id array The Ids of a education level Example: [1, 2]
     * @bodyParam author_tag_id array The Ids of a author tag Example: [1, 2]
     *
     * @responseFile 201 responses/activity/activity.json
     *
     * @response 500 {
     *   "errors": [
     *     "Could not create activity. Please try again later."
     *   ]
     * }
     *
     * @param ActivityCreateRequest $request
     * @param Playlist $playlist
     * @return Response
     * @throws AuthorizationException
     */
    public function store(ActivityCreateRequest $request, Playlist $playlist)
    {
        $this->authorize('create', [Activity::class, $playlist->project]);

        $data = $request->validated();

        $data['order'] = $this->activityRepository->getOrder($playlist->id) + 1;
        $data['shared'] = $playlist->project->shared;

        return \DB::transaction(function () use ($data, $playlist) {

            $attributes = Arr::except($data, ['subject_id', 'education_level_id', 'author_tag_id']);
            $activity = $playlist->activities()->create($attributes);

            if ($activity) {
                if (isset($data['subject_id']) && isset($data['subject_id'][0])) {
                    $activity->subjects()->attach($data['subject_id']);
                }
                if (isset($data['education_level_id']) && isset($data['education_level_id'][0])) {
                    $activity->educationLevels()->attach($data['education_level_id']);
                }
                if (isset($data['author_tag_id']) && isset($data['author_tag_id'][0])) {
                    $activity->authorTags()->attach($data['author_tag_id']);
                }

                $updated_playlist = new PlaylistResource($this->playlistRepository->find($playlist->id));
                event(new PlaylistUpdatedEvent($updated_playlist->project, $updated_playlist));

                return response([
                    'activity' => new ActivityResource($activity),
                ], 201);
            }

            return response([
                'errors' => ['Could not create activity. Please try again later.'],
            ], 500);

        });
    }

    /**
     * Get Activity
     *
     * Get the specified activity.
     *
     * @urlParam playlist required The Id of a playlist Example: 1
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/activity/activity.json
     *
     * @response 400 {
     *   "errors": [
     *     "Invalid playlist or activity id."
     *   ]
     * }
     *
     * @param Playlist $playlist
     * @param Activity $activity
     * @return Response
     */
    public function show(Playlist $playlist, Activity $activity)
    {
        $this->authorize('view', [Activity::class, $playlist->project]);

        if ($activity->playlist_id !== $playlist->id) {
            return response([
                'errors' => ['Invalid playlist or activity id.'],
            ], 400);
        }

        return response([
            'activity' => new ActivityResource($activity),
        ], 200);
    }

    /**
     * Update Activity
     *
     * Update the specified activity.
     *
     * @urlParam playlist required The Id of a playlist Example: 1
     * @urlParam activity required The Id of a activity Example: 1
     * @bodyParam title string required The title of a activity Example: Science of Golf: Why Balls Have Dimples
     * @bodyParam type string required The type of a activity Example: h5p
     * @bodyParam content string required The content of a activity Example:
     * @bodyParam shared bool The status of share of a activity Example: false
     * @bodyParam order int The order number of a activity Example: 2
     * @bodyParam h5p_content_id int The Id of H5p content Example: 59
     * @bodyParam thumb_url string The image url of thumbnail Example: null
     * @bodyParam subject_id array The Ids of a subject Example: [1, 2]
     * @bodyParam education_level_id array The Ids of a education level Example: [1, 2]
     * @bodyParam author_tag_id array The Ids of a author tag Example: [1, 2]
     *
     * @responseFile responses/activity/activity.json
     *
     * @response 400 {
     *   "errors": [
     *     "Invalid playlist or activity id."
     *   ]
     * }
     *
     * @response 500 {
     *   "errors": [
     *     "Failed to update activity."
     *   ]
     * }
     *
     * @param ActivityEditRequest $request
     * @param Playlist $playlist
     * @param Activity $activity
     * @return Response
     */
    public function update(ActivityEditRequest $request, Playlist $playlist, Activity $activity)
    {
        $this->authorize('update', [Activity::class, $playlist->project]);

        if ($activity->playlist_id !== $playlist->id) {
            return response([
                'errors' => ['Invalid playlist or activity id.'],
            ], 400);
        }
        $validated = $request->validated();

        return \DB::transaction(function () use ($validated, $playlist, $activity) {

            $attributes = Arr::except($validated, ['data', 'subject_id', 'education_level_id', 'author_tag_id']);
            $is_updated = $this->activityRepository->update($attributes, $activity->id);

            if ($is_updated) {
                if (isset($validated['subject_id'])) {
                    $activity->subjects()->sync($validated['subject_id']);
                }
                if (isset($validated['education_level_id'])) {
                    $activity->educationLevels()->sync($validated['education_level_id']);
                }
                if (isset($validated['author_tag_id'])) {
                    $activity->authorTags()->sync($validated['author_tag_id']);
                }

                // H5P meta is in 'data' index of the payload.
                $this->update_h5p($validated['data'], $activity->h5p_content_id);

                $updated_activity = new ActivityResource($this->activityRepository->find($activity->id));
                $playlist = new PlaylistResource($updated_activity->playlist);
                event(new ActivityUpdatedEvent($playlist->project, $playlist, $updated_activity));

                return response([
                    'activity' => $updated_activity,
                ], 200);
            }

            return response([
                'errors' => ['Failed to update activity.'],
            ], 500);

        });
    }

    /**
     * Update H5P
     *
     * Update H5P content
     *
     * @param $request
     * @param int $id
     *
     * @return mixed
     * @throws H5PException
     */
    public function update_h5p($request, $id)
    {
        $h5p = App::make('LaravelH5p');
        $core = $h5p::$core;
        $editor = $h5p::$h5peditor;
        $request['action'] = 'create';
        $event_type = 'update';
        $content = $h5p->load_content($id);
        $content['disable'] = H5PCore::DISABLE_NONE;

        $oldLibrary = $content['library'];
        $oldParams = json_decode($content['params']);

        $content['library'] = $core->libraryFromString($request['library']);
        if (!$content['library']) {
            throw new H5PException('Invalid library.');
        }

        // Check if library exists.
        $content['library']['libraryId'] = $core->h5pF->getLibraryId(
            $content['library']['machineName'],
            $content['library']['majorVersion'],
            $content['library']['minorVersion']
        );
        if (!$content['library']['libraryId']) {
            throw new H5PException('No such library');
        }

        $content['params'] = $request['parameters'];
        $params = json_decode($content['params']);
        // $content['title'] = $params->metadata->title;

        if ($params === NULL) {
            throw new H5PException('Invalid parameters');
        }

        $content['params'] = json_encode($params->params);
        $content['metadata'] = $params->metadata;

        // Trim title and check length
        $trimmed_title = empty($content['metadata']->title) ? '' : trim($content['metadata']->title);
        if ($trimmed_title === '') {
            throw new H5PException('Missing title');
        }

        if (strlen($trimmed_title) > 255) {
            throw new H5PException('Title is too long. Must be 256 letters or shorter.');
        }
        // Set disabled features
        $set = array(
            H5PCore::DISPLAY_OPTION_FRAME => filter_input(INPUT_POST, 'frame', FILTER_VALIDATE_BOOLEAN),
            H5PCore::DISPLAY_OPTION_DOWNLOAD => filter_input(INPUT_POST, 'download', FILTER_VALIDATE_BOOLEAN),
            H5PCore::DISPLAY_OPTION_EMBED => filter_input(INPUT_POST, 'embed', FILTER_VALIDATE_BOOLEAN),
            H5PCore::DISPLAY_OPTION_COPYRIGHT => filter_input(INPUT_POST, 'copyright', FILTER_VALIDATE_BOOLEAN),
        );
        $content['disable'] = $core->getStorableDisplayOptions($set, $content['disable']);
        // Save new content
        $core->saveContent($content);
        // Move images and find all content dependencies
        $editor->processParameters($content['id'], $content['library'], $params->params, $oldLibrary, $oldParams);

        return $content['id'];
    }

    /**
     * Get Activity Detail
     *
     * Get the specified activity in detail.
     *
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/activity/activity-with-detail.json
     *
     * @param Activity $activity
     * @return Response
     */
    public function detail(Activity $activity)
    {
        $this->authorize('view', [Activity::class, $activity->playlist->project]);

        $data = ['h5p_parameters' => null, 'user_name' => null, 'user_id' => null];

        if ($activity->playlist->project->user) {
            $data['user_name'] = $activity->playlist->project->user;
            $data['user_id'] = $activity->playlist->project->id;
        }

        if ($activity->type === 'h5p') {
            $h5p = App::make('LaravelH5p');
            $core = $h5p::$core;
            $editor = $h5p::$h5peditor;
            $content = $h5p->load_content($activity->h5p_content_id);
            $library = $content['library'] ? \H5PCore::libraryToString($content['library']) : 0;
            $data['h5p_parameters'] = '{"params":' . $core->filterParameters($content) . ',"metadata":' . json_encode((object)$content['metadata']) . '}';
        }

        return response([
            'activity' => new ActivityDetailResource($activity, $data),
        ], 200);
    }

    /**
     * Share Activity
     *
     * Share the specified activity.
     *
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/activity/activity-shared.json
     *
     * @response 500 {
     *   "errors": [
     *     "Failed to share activity."
     *   ]
     * }
     *
     * @param Activity $activity
     * @return Response
     */
    public function share(Activity $activity)
    {
        $this->authorize('share', [Activity::class, $activity->playlist->project]);

        $is_updated = $this->activityRepository->update([
            'shared' => true,
        ], $activity->id);

        if ($is_updated) {
            $updated_activity = new ActivityResource($this->activityRepository->find($activity->id));
            $playlist = new PlaylistResource($updated_activity->playlist);
            event(new ActivityUpdatedEvent($playlist->project, $playlist, $updated_activity));

            return response([
                'activity' => $updated_activity,
            ], 200);
        }

        return response([
            'errors' => ['Failed to share activity.'],
        ], 500);
    }

    /**
     * Remove Share Activity
     *
     * Remove share the specified activity.
     *
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/activity/activity.json
     *
     * @response 500 {
     *   "errors": [
     *     "Failed to remove share activity."
     *   ]
     * }
     *
     * @param Activity $activity
     * @return Response
     */
    public function removeShare(Activity $activity)
    {
        $this->authorize('share', [Activity::class, $activity->playlist->project]);

        $is_updated = $this->activityRepository->update([
            'shared' => false,
        ], $activity->id);

        if ($is_updated) {
            $updated_activity = new ActivityResource($this->activityRepository->find($activity->id));
            $playlist = new PlaylistResource($updated_activity->playlist);
            event(new ActivityUpdatedEvent($playlist->project, $playlist, $updated_activity));

            return response([
                'activity' => $updated_activity,
            ], 200);
        }

        return response([
            'errors' => ['Failed to remove share activity.'],
        ], 500);
    }

    /**
     * Remove Activity
     *
     * Remove the specified activity.
     *
     * @urlParam playlist required The Id of a playlist Example: 1
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @response {
     *   "message": "Activity has been deleted successfully."
     * }
     *
     * @response 500 {
     *   "errors": [
     *     "Failed to delete activity."
     *   ]
     * }
     *
     * @param Playlist $playlist
     * @param Activity $activity
     * @return Response
     */
    public function destroy(Playlist $playlist, Activity $activity)
    {
        $this->authorize('delete', [Activity::class, $activity->playlist->project]);

        if ($activity->playlist_id !== $playlist->id) {
            return response([
                'errors' => ['Invalid playlist or activity id.'],
            ], 400);
        }

        $is_deleted = $this->activityRepository->delete($activity->id);

        if ($is_deleted) {
            return response([
                'message' => 'Activity has been deleted successfully.',
            ], 200);
        }

        return response([
            'errors' => ['Failed to delete activity.'],
        ], 500);
    }

    /**
     * Clone Activity
     *
     * Clone the specified activity of a playlist.
     *
     * @urlParam playlist required The Id of a playlist Example: 1
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @response {
     *   "message": "Activity is being cloned|duplicated in background!"
     * }
     *
     * @response 400 {
     *   "errors": [
     *     "Not a Public Activity."
     *   ]
     * }
     *
     * @response 500 {
     *   "errors": [
     *     "Failed to clone activity."
     *   ]
     * }
     *
     * @param Request $request
     * @param Playlist $playlist
     * @param Activity $activity
     * @return Response
     */
    public function clone(Request $request, Playlist $playlist, Activity $activity)
    {
        $this->authorize('clone', [Activity::class, $activity->playlist->project]);

        CloneActivity::dispatch($playlist, $activity, $request->bearerToken())->delay(now()->addSecond());
        $isDuplicate = ($activity->playlist_id == $playlist->id);
        $process = ($isDuplicate) ? "duplicate" : "clone";
        return response([
            "message" => "Your request to $process  activity [$activity->title] has been received and is being processed. <br>
            You will be alerted in the notification section in the title bar when complete.",
        ], 200);
    }

    /**
     * H5P Activity
     *
     * Get H5P Activity details
     *
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/activity/activity-playlists.json
     *
     * @param Activity $activity
     * @return Response
     */
    public function h5p(Activity $activity)
    {
        $this->authorize('view', [Project::class, $activity->playlist->project]);
        $h5p = App::make('LaravelH5p');
        $core = $h5p::$core;
        $settings = $h5p::get_editor($content = null, 'preview');
        $content = $h5p->load_content($activity->h5p_content_id);
        $content['disable'] = config('laravel-h5p.h5p_preview_flag');
        $embed = $h5p->get_embed($content, $settings);
        $embed_code = $embed['embed'];
        $settings = $embed['settings'];
        $user = Auth::user();

        // create event dispatch
        event(new H5pEvent(
            'content',
            NULL,
            $content['id'],
            $content['title'],
            $content['library']['name'],
            $content['library']['majorVersion'] . '.' . $content['library']['minorVersion']
        ));
        $user_data = $user->only(['id', 'name', 'email']);

        $h5p_data = ['settings' => $settings, 'user' => $user_data, 'embed_code' => $embed_code];
        return response([
            'activity' => new H5pActivityResource($activity, $h5p_data),
            'playlist' => new PlaylistResource($activity->playlist),
        ], 200);
    }

    /**
     * Get H5P Resource Settings
     *
     * Get H5P Resource Settings for a activity
     *
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/h5p/h5p-resource-settings-open.json
     *
     * @response 500 {
     *   "errors": [
     *     "Activity doesn't belong to this user."
     *   ]
     * }
     *
     * @param Activity $activity
     * @return Response
     */
    public function getH5pResourceSettings(Activity $activity)
    {
        $this->authorize('view', [Project::class, $activity->playlist->project]);

        if ($activity->type === 'h5p') {
            $h5p = App::make('LaravelH5p');
            $core = $h5p::$core;
            $editor = $h5p::$h5peditor;
            $content = $h5p->load_content($activity->h5p_content_id);
        }

        return response([
            'h5p' => $content,
            'activity' => new ActivityResource($activity),
            'playlist' => new PlaylistResource($activity->playlist),
        ], 200);
    }

    /**
     * Get H5P Resource Settings (Open)
     *
     * Get H5P Resource Settings for a activity
     *
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/h5p/h5p-resource-settings-open.json
     *
     * @param Activity $activity
     * @return Response
     */
    public function getH5pResourceSettingsOpen(Activity $activity)
    {
        $this->authorize('view', [Project::class, $activity->playlist->project]);

        if ($activity->type === 'h5p') {
            $h5p = App::make('LaravelH5p');
            $core = $h5p::$core;
            $editor = $h5p::$h5peditor;
            $content = $h5p->load_content($activity->h5p_content_id);
        }

        $activity->shared = isset($activity->shared) ? $activity->shared : false;

        return response([
            'h5p' => $content,
            'activity' => new ActivityResource($activity),
            'playlist' => new PlaylistResource($activity->playlist),
        ], 200);
    }

    /**
     * Get H5P Resource Settings (Shared)
     *
     * Get H5P Resource Settings for a shared activity
     *
     * @urlParam activity required The Id of a activity
     *
     * @responseFile responses/h5p/h5p-resource-settings-open.json
     *
     * @response 400 {
     *   "errors": [
     *     "Activity not found."
     *   ]
     * }
     *
     * @param Activity $activity
     * @return Response
     */
    public function getH5pResourceSettingsShared(Activity $activity)
    {
        // 3 is for indexing approved - see Project Model @indexing property
        if ($activity->shared || ($activity->playlist->project->indexing === (int)config('constants.indexing-approved'))) {
            $h5p = App::make('LaravelH5p');
            $core = $h5p::$core;
            $settings = $h5p::get_editor($content = null, 'preview');
            $content = $h5p->load_content($activity->h5p_content_id);
            $content['disable'] = config('laravel-h5p.h5p_preview_flag');
            $embed = $h5p->get_embed($content, $settings);
            $embed_code = $embed['embed'];
            $settings = $embed['settings'];
            $user_data = null;
            $h5p_data = ['settings' => $settings, 'user' => $user_data, 'embed_code' => $embed_code];

            return response([
                'h5p' => $h5p_data,
                'activity' => new ActivityResource($activity),
                'playlist' => new PlaylistResource($activity->playlist),
            ], 200);
        }

        return response([
            'errors' => ['Activity not found.']
        ], 400);
    }

    /**
     * @uses One time script to populate all missing order number
     */
    public function populateOrderNumber()
    {
        $this->activityRepository->populateOrderNumber();
    }

    /**
     * Get Activity Search Preview
     *
     * Get the specified activity search preview.
     *
     * @urlParam suborganization required The Id of a suborganization Example: 1
     * @urlParam activity required The Id of a activity Example: 1
     *
     * @responseFile responses/h5p/h5p-resource-settings-open.json
     *
     * @param Organization $suborganization
     * @param Activity $activity
     * @return Response
     */
    public function searchPreview(Organization $suborganization, Activity $activity)
    {
        $this->authorize('searchPreview', [$activity->playlist->project, $suborganization]);

        $h5p = App::make('LaravelH5p');
        $core = $h5p::$core;
        $settings = $h5p::get_editor();
        $content = $h5p->load_content($activity->h5p_content_id);
        $content['disable'] = config('laravel-h5p.h5p_preview_flag');
        $embed = $h5p->get_embed($content, $settings);
        $embed_code = $embed['embed'];
        $settings = $embed['settings'];
        $user_data = null;
        $h5p_data = ['settings' => $settings, 'user' => $user_data, 'embed_code' => $embed_code];

        return response([
            'h5p' => $h5p_data,
            'activity' => new ActivityResource($activity),
            'playlist' => new PlaylistResource($activity->playlist),
        ], 200);
    }

    /**
     * Migrate h5p keywords
     */
    public function migrateH5pKeyWords()
    {
        foreach (H5pContent::cursor() as $h5pContent) {
            $record = $this->h5pContentRepository->getLibrary($h5pContent->id);
            $library = $record->library;
            $libraryName = $library->name;

            if ($libraryName == 'H5P.CoursePresentation') {
                $h5p = App::make('LaravelH5p');
                $interface = $h5p::$interface;
                $contentKeywords = $interface->getContentKeywords(array('params' => $h5pContent->parameters));
                $this->h5pContentRepository->update(
                    array(
                        'content_keywords' => $contentKeywords
                    ),
                    $h5pContent->id
                );
            }
        }
    }

    public function searchH5pKeyword(Request $request) {
        $keyword = $request->query('keyword');
        $perPage = isset($request['size']) ? $request['size'] : config('constants.default-pagination-per-page');
        $response = $this->activityRepository->searchByH5pKeyword($keyword, $perPage);
        $this->updateContentKeywords($response->items(), $keyword);
        return response([
            'activities' => $response,
            'searchKeyword' => $keyword
        ]);
    }

    private function updateContentKeywords($activities, $searchKeyword)
    {
        foreach ($activities as $activity) {
            if (isset($activity['h5p_content']) && isset($activity['h5p_content']['content_keywords'])) {
                $contentKeywords = $activity['h5p_content']['content_keywords'];
                if (!empty($contentKeywords)) {
                    $slideNumbers = array();
                    foreach (json_decode($contentKeywords) as $contentKeyword) {
                        $keywords = $contentKeyword->keywords;
                        $matched = false;
                        foreach ($keywords as $keyword) {
                            if (preg_match("/$searchKeyword/i", $keyword)) {
                                $matched = true;
                                break;
                            }
                        }

                        if ($matched) {
                            $slideNumbers[] = $contentKeyword->slideIndex;
                        }
                    }
                    if (!empty($slideNumbers)) {
                        $activity['h5p_content']['matching_slides'] = $slideNumbers;
                    }
                }
            }
        }

    }
}

