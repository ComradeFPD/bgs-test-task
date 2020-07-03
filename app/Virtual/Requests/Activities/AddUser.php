<?php


namespace App\Virtual\Requests\Activities;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Добавление пользователя",
 *     description="Регистрация нового пользователя на мероприятие",
 *     required={"name", "surname", "patronymic", "email", "activity_id"}
 * )
 *
 * Class AddUser
 * @package App\Virtual\Requests\Activities
 */
class AddUser
{
    /**
     * @OA\Property(
     *     title="Имя",
     *     description="Имя пользователя",
     *     example="Иван",
     *     minLength=4,
     *     maxLength=200
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="Фамилия",
     *     description="Фамилия пользователя",
     *     example="Иванов",
     *     minLength=4,
     *     maxLength=200
     * )
     *
     * @var string
     */
    public $surname;

    /**
     * @OA\Property(
     *     title="Отчество",
     *     description="Отчество пользователя",
     *     example="Иванович",
     *     minLength=4,
     *     maxLength=200
     * )
     *
     * @var string
     */
    public $patronymic;

    /**
     * @OA\Property(
     *     title="Email",
     *     description="Email пользователя",
     *     example="test@test.ru",
     *     type="email"
     * )
     *
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *     title="Идентификатор",
     *     description="Уникальный идентификатор мероприятия",
     *     example="1",
     *     type="number"
     * )
     *
     * @var integer
     */
    public $activity_id;
}
