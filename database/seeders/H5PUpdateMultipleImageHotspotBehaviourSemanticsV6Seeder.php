<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class H5PUpdateMultipleImageHotspotBehaviourSemanticsV6Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $h5pEssayLibParams = ['name' => "H5P.ImageMultipleHotspotQuestion", "major_version" => 1, "minor_version" => 0];
        $h5pEssayLib = DB::table('h5p_libraries')->where($h5pEssayLibParams)->first();
        if ($h5pEssayLib) {
            DB::table('h5p_libraries')->where($h5pEssayLibParams)->update([
                'semantics' => $this->updatedSemantics()
            ]);

            $h5pEditorShowWhenLibParams = ['name' => "H5PEditor.ShowWhen", "major_version" => 1, "minor_version" => 0];
            $h5pEditorShowWhenLib = DB::table('h5p_libraries')->where($h5pEditorShowWhenLibParams)->first();
            $h5pEditorShowWhenLibId = $h5pEditorShowWhenLib->id;

            DB::table('h5p_libraries_libraries')->insert([
                [
                    'library_id' => $h5pEssayLib->id,
                    'required_library_id' => $h5pEditorShowWhenLibId,
                    'dependency_type' => 'editor'
                ],
            ]);
        }


    }

    private function updatedSemantics() {
        return '[
  {
    "name": "imageMultipleHotspotQuestion",
    "type": "group",
    "widget": "wizard",
    "label": "Image Multiple Hotspot Question Editor",
    "importance": "high",
    "fields": [
      {
        "name": "backgroundImageSettings",
        "type": "group",
        "label": "Background image",
        "importance": "high",
        "fields": [
          {
            "name": "questionTitle",
            "type": "text",
            "label": "The title of this question",
            "importance": "high",
            "default": "Image hotspot question",
            "description": "Used in summaries, statistics etc."
          },
          {
            "name": "backgroundImage",
            "type": "image",
            "label": "Background image",
            "importance": "high",
            "description": "Select an image to use as background the image hotspot question."
          }
        ]
      },
      {
        "name": "hotspotSettings",
        "type": "group",
        "label": "Hotspots",
        "importance": "high",
        "widget": "imageMultipleHotspotQuestion",
        "description": "Choose appropriate figure for your hotspot, configure it, then drag and resize it into place.",
        "fields": [
          {
            "name": "taskDescription",
            "type": "text",
            "label": "Task description",
            "importance": "high",
            "description": "Instructions to the user.",
            "optional": true
          },
          {
            "name": "hotspotName",
            "type": "text",
            "label": "Hotspot Name",
            "importance": "high",
            "description": "Please enter what the user is trying to find i.e. risks, objects, errors (this will be used in feedback statements).",
            "optional": true
          },
          {
            "name": "numberHotspots",
            "type": "number",
            "label": "Number of correct hotspots that need to be found for question completion",
            "importance": "high",
            "description": "If left blank, will default to the number of correct hotspots created.",
            "optional": true
          },
          {
            "name": "hotspot",
            "type": "list",
            "label": "Hotspot",
            "importance": "high",
            "entity": "Hotspot",
            "field": {
              "type": "group",
              "label": "Hotspot",
              "importance": "high",
              "fields": [
                {
                  "name": "userSettings",
                  "type": "group",
                  "label": "userSettings",
                  "importance": "low",
                  "fields": [
                    {
                      "name": "correct",
                      "type": "boolean",
                      "label": "Correct",
                      "importance": "low",
                      "description": "There can be multiple correct hotspots. The user gets correct/incorrect feedback immediately after each click. The feedback will be displayed in the form of - (Text entered below) (Number of hotspots found) of (Correct hotspots needed) (Hotspot Name entered above)."
                    },
                    {
                      "name": "feedbackText",
                      "type": "text",
                      "label": "Feedback",
                      "importance": "low",
                      "placeholder": "Correct, you have found",
                      "optional": true
                    }
                  ]
                },
                {
                  "name": "computedSettings",
                  "type": "group",
                  "label": "computedSettings",
                  "importance": "low",
                  "fields": [
                    {
                      "name": "x",
                      "type": "number",
                      "optional": true
                    },
                    {
                      "name": "y",
                      "type": "number",
                      "optional": true
                    },
                    {
                      "name": "width",
                      "type": "number",
                      "optional": true
                    },
                    {
                      "name": "height",
                      "type": "number",
                      "optional": true
                    },
                    {
                      "name": "figure",
                      "type": "text",
                      "optional": true
                    }
                  ]
                }
              ]
            }
          },
          {
            "name": "noneSelectedFeedback",
            "type": "text",
            "label": "Feedback if the user selects an empty spot:",
            "importance": "low",
            "placeholder": "You didn\'t locate any hotspots, try again!",
            "optional": true
          },
          {
            "name": "alreadySelectedFeedback",
            "type": "text",
            "label": "Feedback if the user selects an already found hotspot:",
            "placeholder": "You have already found this one!",
            "importance": "low",
            "optional": true
          }
        ]
      }
    ]
  },
  {
    "name": "behaviour",
    "type": "group",
    "label": "Behavioural settings",
    "importance": "low",
    "description": "These options will let you control how the task behaves.",
    "fields": [
      {
        "name": "enableSubmitAnswer",
        "label": "Enable \"Submit\" button",
        "type": "boolean",
        "importance": "low",
        "description": "If checked, learners can submit the task.",
        "default": false,
        "optional": true
      },
      {
        "name": "enableRetry",
        "type": "boolean",
        "label": "Enable retry",
        "description": "Add a retry button for the game",
        "default": false
      },
      {
        "name": "ignoreScoring",
        "label": "Ignore scoring",
        "type": "boolean",
        "importance": "low",
        "description": "If checked, score will be ignored in the feedback",
        "default": false,
        "optional": true
      },
      {
        "name": "enableSubmitAnswerFeedback",
        "label": "Enable submit answer feedback",
        "type": "boolean",
        "importance": "low",
        "description": "If checked, learners will see the feedback that you provided after submission",
        "default": false,
        "optional": true
      },
      {
        "name": "submissionButtonsAlignment",
        "type": "select",
        "label": "Submission buttons alignment",
        "importance": "low",
        "description": "This option determines alignment for submission buttons",
        "optional": true,
        "default": "left",
        "options": [
          {
            "value": "left",
            "label": "Left"
          },
          {
            "value": "right",
            "label": "Right"
          }
        ]
      },
      {
        "name": "answerType",
        "type": "select",
        "label": "Answer Type",
        "importance": "low",
        "description": "Select the answer type of the question.",
        "default": "multi",
        "options": [
          {
            "value": "multi",
            "label": "Multiple"
          },
          {
            "value": "single",
            "label": "Single"
          }
        ]
      },
      {
        "name": "noOfAnswerSelectionAllowed",
        "type": "number",
        "label": "No of answer selection allowed",
        "description": "No of answer allowed to select by the user",
        "importance": "high",
        "widget": "showWhen",
        "showWhen": {
          "rules": [
            {
              "field": "answerType",
              "equals": "multi"
            }
          ]
        }
      }
    ]
  },
  {
    "name": "submitAnswer",
    "type": "text",
    "label": "Text for \"Submit\" button",
    "importance": "low",
    "default": "Submit",
    "common": true
  },
  {
    "name": "retryAnswer",
    "type": "text",
    "label": "Text for \"Retry\" button",
    "importance": "low",
    "default": "Retry",
    "common": true
  },
  {
    "name": "submitAnswerFeedback",
    "type": "text",
    "label": "Submit answers feedback",
    "description": "Submit answer feedback text",
    "importance": "low",
    "common": true,
    "default": "Your answer has been submitted!"
  }
]
';
    }
}
