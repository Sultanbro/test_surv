<?php

namespace Tests\Feature\API;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BitrixIntegrationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetTasksFromBitrixForCurrentUser(): void
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->json('GET', '/bitrix/tasks/list', [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json'
        ]);


        $response->assertStatus(200)->assertSee([
            "id",
            "parentId",
            "title",
            "description",
            "mark",
            "priority",
            "status",
            "multitask",
            "notViewed",
            "replicate",
            "groupId",
            "stageId",
            "createdBy",
            "createdDate",
            "responsibleId",
            "changedBy",
            "changedDate",
            "statusChangedBy",
            "statusChangedDate",
            "closedBy",
            "closedDate",
            "activityDate",
            "dateStart",
            "deadline",
            "startDatePlan",
            "endDatePlan",
            "guid",
            "xmlId",
            "commentsCount",
            "serviceCommentsCount",
            "allowChangeDeadline",
            "allowTimeTracking",
            "taskControl",
            "addInReport",
            "forkedByTemplateId",
            "timeEstimate",
            "timeSpentInLogs",
            "matchWorkTime",
            "forumTopicId",
            "forumId",
            "siteId",
            "subordinate",
            "favorite",
            "exchangeModified",
            "exchangeId",
            "outlookVersion",
            "viewedDate",
            "sorting",
            "durationPlan",
            "durationFact",
            "durationType",
            "isMuted",
            "isPinned",
            "isPinnedInGroup",
            "descriptionInBbcode",
            "auditors",
            "accomplices",
            "newCommentsCount",
            "group",
            "creator",
            "responsible",
            "subStatus"

        ]);
    }
}
