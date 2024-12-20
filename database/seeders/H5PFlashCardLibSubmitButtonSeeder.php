<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class H5PFlashCardLibSubmitButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $h5pFlashCardLibParams = ['name' => "H5P.Flashcards", "major_version" => 1, "minor_version" => 5];
        $h5pFlashCardLib = DB::table('h5p_libraries')->where($h5pFlashCardLibParams)->first();
        if ($h5pFlashCardLib) {
            DB::table('h5p_libraries')->where($h5pFlashCardLibParams)->update(['semantics' => $this->updatedSemantics()]);
        }
    }
    private function updatedSemantics() {
        return '[
          {
            "name": "description",
            "type": "text",
            "label": "Task description",
            "importance": "high"
          },
          {
            "name": "cards",
            "type": "list",
            "widgets": [
              {
                "name": "VerticalTabs",
                "label": "Default"
              }
            ],
            "label": "Cards",
            "entity": "card",
            "importance": "high",
            "min": 1,
            "defaultNum": 1,
            "field": {
              "name": "card",
              "type": "group",
              "label": "Card",
              "importance": "high",
              "fields": [
                {
                  "name": "text",
                  "type": "text",
                  "label": "Question",
                  "importance": "high",
                  "optional": true,
                  "description": "Optional textual question for the card. (The card may use just an image, just a text or both)"
                },
                {
                  "name": "answer",
                  "type": "text",
                  "label": "Answer",
                  "importance": "high",
                  "description": "Answer(solution) for the card."
                },
                {
                  "name": "image",
                  "type": "image",
                  "label": "Image",
                  "importance": "high",
                  "optional": true,
                  "description": "Optional image for the card. (The card may use just an image, just a text or both)"
                },
                {
                  "name": "imageAltText",
                  "type": "text",
                  "label": "Alternative text for image",
                  "importance": "high",
                  "optional": true
                },
                {
                  "name": "tip",
                  "type": "group",
                  "label": "Tip",
                  "importance": "low",
                  "optional": true,
                  "fields": [
                    {
                      "name": "tip",
                      "label": "Tip text",
                      "importance": "low",
                      "type": "text",
                      "widget": "html",
                      "tags": [
                        "p",
                        "br",
                        "strong",
                        "em",
                        "code"
                      ],
                      "optional": true
                    }
                  ]
                }
              ]
            }
          },
          {
            "label": "Progress text",
            "name": "progressText",
            "type": "text",
            "default": "Card @card of @total",
            "importance": "low",
            "description": "Progress text, variables available: @card and @total. Example: Card @card of @total",
            "common": true
          },
          {
            "label": "Text for the next button",
            "name": "next",
            "type": "text",
            "default": "Next",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for the previous button",
            "name": "previous",
            "type": "text",
            "default": "Previous",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for the check answers button",
            "name": "checkAnswerText",
            "type": "text",
            "default": "Check",
            "importance": "low",
            "common": true
          },
          {
            "label": "Show Submit Answers Button",
            "name": "showSubmitAnswersButton",
            "type": "boolean",
            "default": true,
            "importance": "high",
            "optional": true
          },
          {
            "label": "Text for Submit Answers Button",
            "name": "submitAnswers",
            "type": "text",
            "default": "Submit Answers",
            "importance": "low",
            "common": true
          },
          {
            "label": "Require user input before the solution can be viewed",
            "name": "showSolutionsRequiresInput",
            "type": "boolean",
            "default": true,
            "importance": "low",
            "optional": true
          },
          {
            "label": "Text for the answer input field",
            "name": "defaultAnswerText",
            "type": "text",
            "default": "Your answer",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for correct answer",
            "name": "correctAnswerText",
            "type": "text",
            "default": "Correct",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for incorrect answer",
            "name": "incorrectAnswerText",
            "type": "text",
            "default": "Incorrect",
            "importance": "low",
            "common": true
          },
          {
            "label": "Show solution text",
            "name": "showSolutionText",
            "type": "text",
            "default": "Correct answer",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for results title",
            "name": "results",
            "type": "text",
            "default": "Results",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for number of correct",
            "name": "ofCorrect",
            "type": "text",
            "default": "@score of @total correct",
            "importance": "low",
            "description": "Result text, variables available: @score and @total. Example: @score of @total correct",
            "common": true
          },
          {
            "label": "Text for show results",
            "name": "showResults",
            "type": "text",
            "default": "Show results",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for short answer label",
            "name": "answerShortText",
            "type": "text",
            "default": "A:",
            "importance": "low",
            "common": true
          },
          {
            "label": "Text for \"retry\" button",
            "name": "retry",
            "type": "text",
            "default": "Retry",
            "importance": "low",
            "common": true
          },
          {
            "label": "Case sensitive",
            "name": "caseSensitive",
            "type": "boolean",
            "default": false,
            "description": "Makes sure the user input has to be exactly the same as the answer."
          },
          {
            "label": "Incorrect text for assistive technologies",
            "name": "cardAnnouncement",
            "type": "text",
            "default": "Incorrect answer. Correct answer was @answer",
            "description": "Text that will be announced to assistive technologies. Use @answer as variable.",
            "common": true
          },
          {
            "label": "Card change for assistive technologies",
            "name": "pageAnnouncement",
            "type": "text",
            "default": "Page @current of @total",
            "description": "Text that will be announced to assistive technologies when navigating between cards. Use @current and @total as variables.",
            "common": true
          }
        ]
        ';
    }
}
