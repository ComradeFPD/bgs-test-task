<?php

namespace Tests\Feature;

use App\Jobs\UserSuccessfulAddedToActivityJob;
use App\Mail\UserAddedMail;
use App\Models\Activity;
use App\Models\User;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ActivitiesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    protected $token;

    public function setUp() :void
    {
        parent::setUp();
        $this->token = '2a3efea996eafb600a67148a84d1481b';
    }

    /**
     * @test Can i add user to existing activity
     *
     * @return void
     */
    public function canIAddUserToActivity()
    {
        $activity = factory(Activity::class)->create();
        $request = factory(User::class)->make()->toArray();
        $request['activity_id'] = $activity->id;
        $resp = $this->withHeader('Authorization', $this->token)
            ->post(route('activities.store'), $request);
        $resp->assertStatus(200);
        $this->assertDatabaseHas('users', ['name' => $request['name']]);
        $this->assertNotEmpty($activity->users);
    }

    /**
     * @test Can i delete existing user from activity
     *
     * @return void
     */
    public function canIDeleteUserFromActivity()
    {
        $activity = factory(Activity::class)->create();
        $user = factory(User::class)->create();
        $activity->users()->attach($user->id);
        $resp = $this->withHeader('Authorization', $this->token)
            ->delete(route('activities.delete-user'), [
                'user_id' => $user->id,
                'activity_id' => $activity->id
            ]);
        $resp->assertStatus(200);
        $this->assertDatabaseMissing('users', ['email' => $user->email]);
        $this->assertEmpty($activity->users);
    }

    /**
     * @test Can i see exact activity with his users
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function canISeeActivityWithHisUsers()
    {
        $service = new ActivityService();
        $activity = factory(Activity::class)->create();
        $activity->users()->attach(factory(User::class, 10)->create()->pluck('id')->toArray());
        $resp = $this->withHeader('Authorization', $this->token)
            ->get(route('activities.show', $activity->id));
        $resp->assertStatus(200);
        $this->assertEquals($resp->decodeResponseJson(), $service->formatResponse($activity, ''));

    }

    /**
     * @test Can i update existing user info in activity
     *
     * @return void
     */
    public function canIUpdateUserInfoInActivity()
    {
        $activity = factory(Activity::class)->create();
        $activity->users()->attach(factory(User::class)->create()->pluck('id')->toArray());
        $updatedUser = $activity->users()->first()->toArray();
        $updatedUser['name'] = 'Test123';
        $updatedUser['user_id'] = $updatedUser['id'];
        $resp = $this->withHeader('Authorization', $this->token)
            ->put(route('activities.update', $activity->id), $updatedUser);
        $resp->assertStatus(200);
        $this->assertDatabaseHas('users', ['name' => 'Test123']);
        $this->assertEquals('Test123', $activity->users->first()->name);
    }

    /**
     * @test Has job pushed when new user added to activity
     *
     * @return void
     */
    public function hasJobPushedWhenNewUserAdded()
    {
        Queue::fake();
        $activity = factory(Activity::class)->create();
        $user = factory(User::class)->make()->toArray();
        $user['activity_id'] = $activity->id;
        $resp = $this->withHeader('Authorization', $this->token)
            ->post(route('activities.store', $user));
        $resp->assertStatus(200);
        Queue::assertPushed(UserSuccessfulAddedToActivityJob::class, function ($job) use ($user){
            return $user['email'] === $job->user->email;
        });
    }

    /**
     * @test Has mail send to user when his added
     *
     * @return void
     */
    public function hasMailSendWhenUserAdded()
    {
        $this->withoutExceptionHandling();
        Mail::fake();
        $activity = factory(Activity::class)->create();
        $user = factory(User::class)->make()->toArray();
        $user['activity_id'] = $activity->id;
        $resp = $this->withHeader('Authorization', $this->token)
            ->post(route('activities.store', $user));
        $resp->assertStatus(200);
        Mail::assertSent(UserAddedMail::class, function ($mail) use($activity){
            return $activity->title === $mail->activity->title;
        });
    }

    /**
     * @test Can i add user to activity who finished already
     *
     * @return void
     */
    public function canIAddUserToActivityWhoFinished()
    {
        $activity = factory(Activity::class)->create([
            'start_at' => Carbon::now()->subDay()
        ]);
        $user = factory(User::class)->make()->toArray();
        $user['activity_id'] = $activity->id;
        $resp = $this->withHeader('Authorization', $this->token)
            ->post(route('activities.store'), $user);
        $resp->assertStatus(400);
        $this->assertDatabaseMissing('users', ['email' => $user['email']]);
    }
}
