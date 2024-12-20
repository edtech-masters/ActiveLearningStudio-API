<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class H5PImageSequencingLibSubmitButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $h5pImageSequencingLibParams = ['name' => "H5P.ImageSequencing", "major_version" => 1, "minor_version" => 1];
        $h5pImageSequencingLib = DB::table('h5p_libraries')->where($h5pImageSequencingLibParams)->first();
        if ($h5pImageSequencingLib) {
            DB::table('h5p_libraries')->where($h5pImageSequencingLibParams)->update(['semantics' => $this->updatedSemantics()]);
        }
    }

    private function updatedSemantics() {
        return '[
              {
                "label": "Task Description",
                "name": "taskDescription",
                "type": "text",
                "default": "Drag to arrange the images in the correct sequence",
                "description": "A guide telling the user how to solve this task.",
                "importance": "high"
              },
              {
                "label": "Alternate Task Description",
                "name": "altTaskDescription",
                "type": "text",
                "default": "Make the following list be ordered correctly. Use the cursor keys to navigate through the list items, use space to activate or deactivate an item and the cursor keys to move it",
                "description": "A guide intended for visually impaired users on how to solve this task.",
                "importance": "high"
              },
              {
                "name": "sequenceImages",
                "type": "list",
                "widgets": [
                  {
                    "name": "VerticalTabs",
                    "label": "Default"
                  }
                ],
                "label": "Images",
                "importance": "high",
                "entity": "image",
                "min": 3,
                "field": {
                  "type": "group",
                  "name": "imageElement",
                  "label": "Image Element",
                  "importance": "high",
                  "fields": [
                    {
                      "name": "image",
                      "type": "image",
                      "label": "Image",
                      "importance": "high"
                    },
                    {
                      "name": "imageDescription",
                      "type": "text",
                      "label": "Image Description",
                      "importance": "low",
                      "description": "An image description for users who cannot recognize the image"
                    },
                    {
                      "name": "audio",
                      "description": "An optional audio for the card to play",
                      "type": "audio",
                      "label": "Audio files",
                      "importance": "low",
                      "optional": true
                    }
                  ]
                }
              },
              {
                "name": "behaviour",
                "type": "group",
                "label": "Behavioural settings",
                "importance": "low",
                "description": "These options will let you control how the game behaves.",
                "optional": true,
                "fields": [
                  {
                    "name": "enableSolution",
                    "type": "boolean",
                    "label": "Add a show solution button for the game",
                    "importance": "low",
                    "default": true
                  },
                  {
                    "name": "enableRetry",
                    "type": "boolean",
                    "label": "Add button for retrying when the game is over",
                    "importance": "low",
                    "default": true
                  },
                  {
                    "name": "enableResume",
                    "type": "boolean",
                    "label": "Add button for resuming from the current state ",
                    "importance": "low",
                    "default": true
                  }
                ]
              },
              {
                "name": "currikisettings",
                "type": "group",
                "label": "Curriki settings",
                "importance": "low",
                "description": "These options will let you control how the curriki studio behaves.",
                "optional": true,
                "fields": [
                  {
                    "label": "Do not Show Submit Button",
                    "importance": "low",
                    "name": "disableSubmitButton",
                    "type": "boolean",
                    "default": false,
                    "optional": true,
                    "description": "This option only applies to a standalone activity. The Submit button is required for grade passback to an LMS."
                  },
                  {
                    "label": "Placeholder",
                    "importance": "low",
                    "name": "placeholder",
                    "type": "boolean",
                    "default": false,
                    "optional": true,
                    "description": "This option is a place holder. will be used in future"
                  },
                  {
                    "label": "Curriki Localization",
                    "description": "Here you can edit settings or translate texts used in curriki settings",
                    "importance": "low",
                    "name": "currikil10n",
                    "type": "group",
                    "fields": [
                      {
                        "label": "Text for \"Submit\" button",
                        "name": "submitAnswer",
                        "type": "text",
                        "default": "Submit",
                        "optional": true
                      },
                      {
                        "label": "Text for \"Placeholder\" button",
                        "importance": "low",
                        "name": "placeholderButton",
                        "type": "text",
                        "default": "Placeholder",
                        "optional": true
                      }
                    ]
                  }
                ]
              },
              {
                "label": "Localization",
                "importance": "low",
                "name": "l10n",
                "type": "group",
                "common": true,
                "fields": [
                  {
                    "label": "Total Moves text",
                    "importance": "low",
                    "name": "totalMoves",
                    "type": "text",
                    "default": "Total Moves"
                  },
                  {
                    "label": "Time spent text",
                    "importance": "low",
                    "name": "timeSpent",
                    "type": "text",
                    "default": "Time spent"
                  },
                  {
                    "label": "Feedback text",
                    "importance": "low",
                    "name": "score",
                    "type": "text",
                    "default": "You got @score of @total points",
                    "description": "Feedback text, variables available: @score and @total. Example: \'You got @score of @total possible points\'"
                  },
                  {
                    "label": "Text for \"Check\" button",
                    "importance": "low",
                    "name": "checkAnswer",
                    "type": "text",
                    "default": "Check"
                  },
                  {
                    "label": "Text for \"Retry\" button",
                    "importance": "low",
                    "name": "tryAgain",
                    "type": "text",
                    "default": "Retry"
                  },
                  {
                    "label": "Text for \"Show Solution\" button",
                    "importance": "low",
                    "name": "showSolution",
                    "type": "text",
                    "default": "ShowSolution"
                  },
                  {
                    "label": "Text for \"Resume\" button",
                    "importance": "low",
                    "name": "resume",
                    "type": "text",
                    "default": "Resume"
                  },
                  {
                    "name": "audioNotSupported",
                    "type": "text",
                    "label": "Audio not supported message",
                    "importance": "low",
                    "common": true,
                    "default": "Audio Error"
                  },
                  {
                    "name": "ariaPlay",
                    "type": "text",
                    "label": "Play button (text for readspeakers)",
                    "importance": "low",
                    "common": true,
                    "default": "Play the corresponding audio"
                  },
                  {
                    "name": "ariaMoveDescription",
                    "type": "text",
                    "label": "Card moving description (text for readspeakers)",
                    "description": "@posSrc : card initial position, @posDes : card final position",
                    "importance": "low",
                    "common": true,
                    "default": "Moved @cardDesc from @posSrc to @posDes"
                  },
                  {
                    "name": "ariaCardDesc",
                    "type": "text",
                    "label": "Default Card Description (text for readspeakers)",
                    "importance": "low",
                    "common": true,
                    "default": "sequencing item"
                  }
                ]
              }
            ]';
    }
}
