<?php


namespace App\Services;


use App\Http\Requests\AddUserToActivity;
use App\Http\Requests\UpdateUserInfoInActivityRequest;
use App\Jobs\UserSuccessfulAddedToActivityJob;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

/**
 * Service to work with activities
 *
 * Class ActivityService
 * @package App\Services
 */
class ActivityService
{

    /**
     * Add new user to existing activity
     *
     * @param AddUserToActivity $request
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function addUserToActivity(AddUserToActivity $request)
    {
        $activity = Activity::find($request->activity_id);
        if (!$activity)
            throw new \Exception('Такого мероприятия не существует');
        if ($activity->users()->whereEmail($request->email)->exists())
            throw new \Exception('Пользовател уже зарегестрирован на это мероприятие');
        if($activity->start_at < Carbon::now())
            throw new \Exception('Это мероприятие уже завершилось');
        $user = User::create($request->all());
        $activity->users()->attach($user->id);
        dispatch(new UserSuccessfulAddedToActivityJob($activity, $user));
        return $activity;
    }

    /**
     * Delete user from existing activity
     *
     * @param Activity $activity
     * @param $user
     *
     * @return Activity
     */
    public function deleteUserFromActivity(Activity $activity, $user)
    {
        $activity->users()->detach($user->id);
        $user->delete();
        return $activity;
    }

    /**
     * Update user info registered to activity
     *
     * @param UpdateUserInfoInActivityRequest $request
     * @param $id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function updateUserInfoInActivity(UpdateUserInfoInActivityRequest $request, $id)
    {
        $activity = Activity::find($id);
        if(!$activity)
            throw new \Exception('Такого мероприятия не существует');
        $user = $activity->users()->find($request->user_id);
        if(!$user)
            throw new \Exception('Такой пользователь не зарегестрирован на данное мероприятие');
        $user->fill($request->except('user_id'));
        $user->save();
        return $activity->fresh();
    }

    /**
     * Format response to proper format
     *
     * @param $activity
     * @param $message
     *
     * @return array
     */
    public function formatResponse($activity, $message)
    {
        return [
            'message' => $message,
            'activityTitile' => $activity->title,
            'startAt' => Carbon::parse($activity->start_at)->format('d.m.Y H:i'),
            'users' => $activity->users->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'patronymic' => $user->patronymic,
                    'email' => $user->email
                ];
            })->toArray()
        ];
    }
}
