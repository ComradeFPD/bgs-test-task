<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddUserToActivity;
use App\Http\Requests\DeleteUserFromActivityRequest;
use App\Http\Requests\UpdateUserInfoInActivityRequest;
use App\Models\Activity;
use App\Models\User;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new ActivityService();
    }

    /**
     * @OA\Get(
     *     path="/api/activities",
     *     summary="Все мероприятия",
     *     description="Показ всех мероприятия с их участинками",
     *     security={{"bearerAuth":{}}},
     *     tags={"Мероприятия"},
     *     operationId="index",
     *     @OA\Response(
     *     response=200,
     *     description="Список всех мероприятия с их участниками"
     * )
     * )
     *
     * Display all activities
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $resp = Activity::all();
        $resp = $resp->map(function (Activity $activity){
            $resp = [
                'id' => $activity->id,
                'title' => $activity->title,
                'start_at' => Carbon::parse($activity->start_at)->format('d.m.Y H:i:s'),
                'users' => $activity->users->map(function (User $user){
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'patronymic' => $user->patronymic,
                        'email' => $user->email
                    ];
                })->toArray()
            ];
            return $resp;
        });
        return $this->successResponse($resp);
    }


    /**
     * @OA\Post(
     *     path="/api/activities",
     *     summary="Добавление пользователя",
     *     description="Добавление пользователя к существующему мероприятию",
     *     tags={"Мероприятия"},
     *     operationId="store",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *     mediaType="multipart/form-data",
     *     @OA\Schema(ref="#/components/schemas/AddUser")
     * ),
     * ),
     *      @OA\Response(
     *     response=200,
     *     description="Пользователь успешно добавлен к выбранному мероприятию"
     * ),
     *     @OA\Response(
     *     response=400,
     *     description="Такого мероприятия не существует или пользователь уже зарегестрирован на это мероприятие"
     * )
     * )
     *
     * Add new user to exists activity
     *
     * @param  AddUserToActivity  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function store(AddUserToActivity $request)
    {
        try {
           $activity = $this->service->addUserToActivity($request);
        } catch (\Exception $e){
            return $this->errorResponse($e->getMessage());
        }
        return $this->successResponse($this->service->formatResponse($activity,
            "Пользователь успешно добавлен к мероприятию {$activity->title}"));
    }

    /**
     * @OA\Get(
     *     path="/api/activities/{id}",
     *     summary="Отображение определенного мероприятия",
     *     description="Отображение определенного мероприятия и пользователей которые в нём участвуют",
     *     security={{"bearerAuth":{}}},
     *     tags={"Мероприятия"},
     *     operationId="show",
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Уникальный идентификатор мероприятия"
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Мероприятие и его участники"
     * ),
     *     @OA\Response(
     *     response=404,
     *     description="Такого мероприятия не существует"
     * )
     * )
     *
     * Display activity with his users
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $activity = Activity::find($id);
        if(!$activity)
            return $this->modelNotFoundResponse('Такого мероприятия не существует');
        return $this->successResponse($this->service->formatResponse($activity, ''));
    }


    /**
     * @OA\Put(
     *     path="/api/activities/{id}",
     *     summary="Обновление информации о пользователе",
     *     description="Обновление информации о, уже добавленом в мероприятие, пользователе",
     *     security={{"bearerAuth":{}}},
     *     tags={"Мероприятия"},
     *     operationId="update",
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Уникальный идентификатор мероприятия для которого обновить пользователя"
     * ),
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UpdateUser")
     * ),
     *      @OA\Response(
     *     response=200,
     *     description="Информация о пользователе успешно обновлена"
     * ),
     *     @OA\Response(
     *     response=404,
     *     description="Такого мероприятия не существует или выбранный пользователь на него не зарегестрирован"
     * )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  UpdateUserInfoInActivityRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserInfoInActivityRequest $request, $id)
    {
        try {
           $activity = $this->service->updateUserInfoInActivity($request, $id);
        } catch (\Exception $e){
            return $this->modelNotFoundResponse($e->getMessage());
        }
        return $this->successResponse($this->service->formatResponse($activity, "Данные о пользователе успешно обновленны"));
    }

    /**
     * @OA\Delete(
     *     path="/api/activity/delete-user",
     *     summary="Удаление пользователя",
     *     description="Удаление пользователя из выбранного мероприятия",
     *     tags={"Мероприятия"},
     *     security={{"bearerAuth":{}}},
     *     operationId="deleteUserFromActivity",
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/DeleteUser")
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Пользователь успешно удалён"
     * ),
     *     @OA\Response(
     *     response=404,
     *     description="Такого мероприятия не существует или пользователь не участвует в нём"
     * )
     * )
     *
     * Delete user from activity
     *
     * @param DeleteUserFromActivityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserFromActivity(DeleteUserFromActivityRequest $request)
    {
        $activity = Activity::find($request->activity_id);
        if(!$activity)
            return $this->modelNotFoundResponse('Такого мероприятия не существует');
        $user = $activity->users()->find($request->user_id);
        if(!$user)
            return $this->modelNotFoundResponse('Этот пользователь не зарегестрирован на это мероприятие');
        $activity = $this->service->deleteUserFromActivity($activity, $user);
        return $this->successResponse($this->service->formatResponse($activity,
            "Пользовать {$user->email} успешно удалён из участинков мероприятия {$activity->title}"));
    }

}
