<?php


namespace App\Virtual\Requests\Activities;

/**
 * @OA\Schema(
 *     title="Удаление пользователя",
 *     description="Удаление пользователя из выбранного мероприятия",
 *     type="object",
 *     required={"user_id", "activity_id"}
 * )
 *
 * Class DeleteUser
 * @package App\Virtual\Requests\Activities
 */
class DeleteUser
{
    /**
     * @OA\Property(
     *     title="Идентификатор пользователя",
     *     description="Уникальный идентификатор пользователя",
     *     example="1",
     *     type="number"
     * )
     *
     * @var integer
     */
    public $user_id;

    /**
     * @OA\Property(
     *     title="Идентификатор мероприятия",
     *     description="Уникальный идентификатор мероприятия",
     *     example="1",
     *     type="number"
     * )
     *
     * @var integer
     */
    public $activity_id;
}
