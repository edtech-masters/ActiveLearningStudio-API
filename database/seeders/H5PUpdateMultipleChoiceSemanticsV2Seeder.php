<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class H5PUpdateMultipleChoiceSemanticsV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $h5pMultichoiceParams = ['name' => "H5P.MultiChoice", "major_version" => 1, "minor_version" => 16];
        $h5pMultichoiceLib = DB::table('h5p_libraries')->where($h5pMultichoiceParams)->pluck('id');
        if ($h5pMultichoiceLib) {
            DB::table('h5p_libraries')->where($h5pMultichoiceParams)->update([
                'semantics' => $this->updatedSemantics()
            ]);
        }

    }

    private function updatedSemantics() {
        return '[
  {
    "name": "media",
    "type": "group",
    "label": "Media",
    "importance": "medium",
    "fields": [
      {
        "name": "type",
        "type": "library",
        "label": "Type",
        "importance": "medium",
        "options": [
          "H5P.Image 1.1",
          "H5P.Video 1.6",
          "H5P.Audio 1.5"
        ],
        "optional": true,
        "description": "Optional media to display above the question."
      },
      {
        "name": "disableImageZooming",
        "type": "boolean",
        "label": "Disable image zooming",
        "importance": "low",
        "default": false,
        "optional": true,
        "widget": "showWhen",
        "showWhen": {
          "rules": [
            {
              "field": "type",
              "equals": "H5P.Image 1.1"
            }
          ]
        }
      }
    ]
  },
  {
    "name": "question",
    "type": "text",
    "importance": "medium",
    "widget": "html",
    "label": "Question",
    "enterMode": "p",
    "tags": [
      "strong",
      "em",
      "sub",
      "sup",
      "h2",
      "h3",
      "pre",
      "code"
    ]
  },
  {
    "name": "answers",
    "type": "list",
    "importance": "high",
    "label": "Available options",
    "entity": "option",
    "min": 1,
    "defaultNum": 2,
    "field": {
      "name": "answer",
      "type": "group",
      "label": "Option",
      "importance": "high",
      "fields": [
        {
          "name": "text",
          "type": "text",
          "importance": "medium",
          "widget": "html",
          "label": "Text",
          "tags": [
            "strong",
            "em",
            "sub",
            "sup",
            "code"
          ]
        },
        {
          "name": "correct",
          "type": "boolean",
          "label": "Correct",
          "importance": "low"
        },
        {
          "name": "tipsAndFeedback",
          "type": "group",
          "label": "Tips and feedback",
          "importance": "low",
          "optional": true,
          "fields": [
            {
              "name": "tip",
              "type": "text",
              "widget": "html",
              "label": "Tip text",
              "importance": "low",
              "description": "Hint for the user. This will appear before user checks his answer/answers.",
              "optional": true,
              "tags": [
                "p",
                "br",
                "strong",
                "em",
                "a",
                "code"
              ]
            },
            {
              "name": "chosenFeedback",
              "type": "text",
              "widget": "html",
              "label": "Message displayed if answer is selected",
              "importance": "low",
              "description": "Message will appear below the answer on \"check\" if this answer is selected.",
              "optional": true,
              "tags": [
                "strong",
                "em",
                "sub",
                "sup",
                "a",
                "code"
              ]
            },
            {
              "name": "notChosenFeedback",
              "type": "text",
              "widget": "html",
              "label": "Message displayed if answer is not selected",
              "importance": "low",
              "description": "Message will appear below the answer on \"check\" if this answer is not selected.",
              "optional": true,
              "tags": [
                "strong",
                "em",
                "sub",
                "sup",
                "a",
                "code"
              ]
            }
          ]
        }
      ]
    }
  },
  {
    "name": "overallFeedback",
    "type": "group",
    "label": "Overall Feedback",
    "importance": "low",
    "expanded": true,
    "fields": [
      {
        "name": "overallFeedback",
        "type": "list",
        "widgets": [
          {
            "name": "RangeList",
            "label": "Default"
          }
        ],
        "importance": "high",
        "label": "Define custom feedback for any score range",
        "description": "Click the \"Add range\" button to add as many ranges as you need. Example: 0-20% Bad score, 21-91% Average Score, 91-100% Great Score!",
        "entity": "range",
        "min": 1,
        "defaultNum": 1,
        "optional": true,
        "field": {
          "name": "overallFeedback",
          "type": "group",
          "importance": "low",
          "fields": [
            {
              "name": "from",
              "type": "number",
              "label": "Score Range",
              "min": 0,
              "max": 100,
              "default": 0,
              "unit": "%"
            },
            {
              "name": "to",
              "type": "number",
              "min": 0,
              "max": 100,
              "default": 100,
              "unit": "%"
            },
            {
              "name": "feedback",
              "type": "text",
              "label": "Feedback for defined score range",
              "importance": "low",
              "placeholder": "Fill in the feedback",
              "optional": true
            }
          ]
        }
      }
    ]
  },
  {
    "name": "UI",
    "type": "group",
    "label": "User interface translations for multichoice",
    "importance": "low",
    "common": true,
    "fields": [
      {
        "name": "checkAnswerButton",
        "type": "text",
        "label": "Check answer button label",
        "importance": "low",
        "default": "Check"
      },
      {
        "name": "submitAnswerButton",
        "type": "text",
        "label": "Submit answer button label",
        "importance": "low",
        "default": "Submit"
      },
      {
        "name": "showSolutionButton",
        "type": "text",
        "label": "Show solution button label",
        "importance": "low",
        "default": "Show solution"
      },
      {
        "name": "tryAgainButton",
        "type": "text",
        "label": "Retry button label",
        "importance": "low",
        "default": "Retry",
        "optional": true
      },
      {
        "name": "tipsLabel",
        "type": "text",
        "label": "Tip label",
        "importance": "low",
        "default": "Show tip",
        "optional": true
      },
      {
        "name": "scoreBarLabel",
        "type": "text",
        "label": "Textual representation of the score bar for those using a readspeaker",
        "description": "Available variables are :num and :total",
        "importance": "low",
        "default": "You got :num out of :total points",
        "optional": true
      },
      {
        "name": "tipAvailable",
        "type": "text",
        "label": "Tip Available (not displayed)",
        "importance": "low",
        "default": "Tip available",
        "description": "Accessibility text used for readspeakers",
        "optional": true
      },
      {
        "name": "feedbackAvailable",
        "type": "text",
        "label": "Feedback Available (not displayed)",
        "importance": "low",
        "default": "Feedback available",
        "description": "Accessibility text used for readspeakers",
        "optional": true
      },
      {
        "name": "readFeedback",
        "type": "text",
        "label": "Read Feedback (not displayed)",
        "importance": "low",
        "default": "Read feedback",
        "description": "Accessibility text used for readspeakers",
        "optional": true,
        "deprecated": true
      },
      {
        "name": "wrongAnswer",
        "type": "text",
        "label": "Wrong Answer (not displayed)",
        "importance": "low",
        "default": "Wrong answer",
        "description": "Accessibility text used for readspeakers",
        "optional": true,
        "deprecated": true
      },
      {
        "name": "correctAnswer",
        "type": "text",
        "label": "Correct Answer (not displayed)",
        "importance": "low",
        "default": "Correct answer",
        "description": "Accessibility text used for readspeakers",
        "optional": true
      },
      {
        "name": "shouldCheck",
        "type": "text",
        "label": "Option should have been checked",
        "importance": "low",
        "default": "Should have been checked",
        "optional": true
      },
      {
        "name": "shouldNotCheck",
        "type": "text",
        "label": "Option should not have been checked",
        "importance": "low",
        "default": "Should not have been checked",
        "optional": true
      },
      {
        "label": "Text for \"Requires answer\" message",
        "importance": "low",
        "name": "noInput",
        "type": "text",
        "default": "Please answer before viewing the solution",
        "optional": true
      },
      {
        "name": "a11yCheck",
        "type": "text",
        "label": "Assistive technology description for \"Check\" button",
        "default": "Check the answers. The responses will be marked as correct, incorrect, or unanswered.",
        "importance": "low",
        "common": true
      },
      {
        "name": "a11yShowSolution",
        "type": "text",
        "label": "Assistive technology description for \"Show Solution\" button",
        "default": "Show the solution. The task will be marked with its correct solution.",
        "importance": "low",
        "common": true
      },
      {
        "name": "a11yRetry",
        "type": "text",
        "label": "Assistive technology description for \"Retry\" button",
        "default": "Retry the task. Reset all responses and start the task over again.",
        "importance": "low",
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
  },
  {
    "name": "behaviour",
    "type": "group",
    "label": "Behavioural settings",
    "importance": "low",
    "description": "These options will let you control how the task behaves.",
    "optional": true,
    "fields": [
      {
        "name": "enableRetry",
        "type": "boolean",
        "label": "Enable \"Retry\" button",
        "importance": "low",
        "default": true,
        "optional": true
      },
      {
        "name": "enableSolutionsButton",
        "type": "boolean",
        "label": "Enable \"Show Solution\" button",
        "importance": "low",
        "default": true,
        "optional": true
      },
      {
        "name": "enableCheckButton",
        "type": "boolean",
        "label": "Enable \"Check\" button",
        "widget": "none",
        "importance": "low",
        "default": true,
        "optional": true
      },
      {
        "name": "type",
        "type": "select",
        "label": "Question Type",
        "importance": "low",
        "description": "Select the look and behaviour of the question.",
        "default": "auto",
        "options": [
          {
            "value": "auto",
            "label": "Automatic"
          },
          {
            "value": "multi",
            "label": "Multiple Choice (Checkboxes)"
          },
          {
            "value": "single",
            "label": "Single Choice (Radio Buttons)"
          }
        ]
      },
      {
        "name": "singlePoint",
        "type": "boolean",
        "label": "Give one point for the whole task",
        "importance": "low",
        "description": "Enable to give a total of one point for multiple correct answers. This will not be an option in \"Single answer\" mode.",
        "default": false
      },
      {
        "name": "randomAnswers",
        "type": "boolean",
        "label": "Randomize answers",
        "importance": "low",
        "description": "Enable to randomize the order of the answers on display.",
        "default": true
      },
      {
        "label": "Require answer before the solution can be viewed",
        "importance": "low",
        "name": "showSolutionsRequiresInput",
        "type": "boolean",
        "default": true,
        "optional": true
      },
      {
        "label": "Show confirmation dialog on \"Check\"",
        "importance": "low",
        "name": "confirmCheckDialog",
        "type": "boolean",
        "default": false
      },
      {
        "label": "Show confirmation dialog on \"Retry\"",
        "importance": "low",
        "name": "confirmRetryDialog",
        "type": "boolean",
        "default": false
      },
      {
        "label": "Automatically check answers",
        "importance": "low",
        "name": "autoCheck",
        "type": "boolean",
        "default": false,
        "description": "Enabling this option will make accessibility suffer, make sure you know what you\'re doing."
      },
      {
        "label": "Pass percentage",
        "name": "passPercentage",
        "type": "number",
        "description": "This setting often won\'t have any effect. It is the percentage of the total score required for getting 1 point when one point for the entire task is enabled, and for getting result.success in xAPI statements.",
        "min": 0,
        "max": 100,
        "step": 1,
        "default": 100
      },
      {
        "name": "showScorePoints",
        "type": "boolean",
        "label": "Show score points",
        "description": "Show points earned for each answer. This will not be an option in \'Single answer\' mode or if \'Give one point for the whole task\' option is enabled.",
        "importance": "low",
        "default": true
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
        "name": "enableSubmitAnswerFeedback",
        "label": "Enable submit answer feedback",
        "type": "boolean",
        "importance": "low",
        "description": "If checked, learners will see the feedback that you provided after submission",
        "default": false,
        "optional": true
      },
      {
        "name": "ignoreScoring",
        "label": "Ignore scoring",
        "type": "boolean",
        "importance": "low",
        "description": "If checked, learners will only see the feedback that you provided for the keywords, but no score.",
        "default": false,
        "optional": true
      },
      {
        "name": "ignoreAnswerEvaluation",
        "label": "Ignore answer evaluation",
        "type": "boolean",
        "importance": "low",
        "description": "If checked, learners will not able see the answer is correct or not indication after submission.",
        "default": false,
        "optional": true
      }
    ]
  },
  {
    "label": "Check confirmation dialog",
    "importance": "low",
    "name": "confirmCheck",
    "type": "group",
    "common": true,
    "fields": [
      {
        "label": "Header text",
        "importance": "low",
        "name": "header",
        "type": "text",
        "default": "Finish ?"
      },
      {
        "label": "Body text",
        "importance": "low",
        "name": "body",
        "type": "text",
        "default": "Are you sure you wish to finish ?",
        "widget": "html",
        "enterMode": "p",
        "tags": [
          "strong",
          "em",
          "del",
          "u",
          "code"
        ]
      },
      {
        "label": "Cancel button label",
        "importance": "low",
        "name": "cancelLabel",
        "type": "text",
        "default": "Cancel"
      },
      {
        "label": "Confirm button label",
        "importance": "low",
        "name": "confirmLabel",
        "type": "text",
        "default": "Finish"
      }
    ]
  },
  {
    "label": "Retry confirmation dialog",
    "importance": "low",
    "name": "confirmRetry",
    "type": "group",
    "common": true,
    "fields": [
      {
        "label": "Header text",
        "importance": "low",
        "name": "header",
        "type": "text",
        "default": "Retry ?"
      },
      {
        "label": "Body text",
        "importance": "low",
        "name": "body",
        "type": "text",
        "default": "Are you sure you wish to retry ?",
        "widget": "html",
        "enterMode": "p",
        "tags": [
          "strong",
          "em",
          "del",
          "u",
          "code"
        ]
      },
      {
        "label": "Cancel button label",
        "importance": "low",
        "name": "cancelLabel",
        "type": "text",
        "default": "Cancel"
      },
      {
        "label": "Confirm button label",
        "importance": "low",
        "name": "confirmLabel",
        "type": "text",
        "default": "Confirm"
      }
    ]
  }
]';
    }
}
