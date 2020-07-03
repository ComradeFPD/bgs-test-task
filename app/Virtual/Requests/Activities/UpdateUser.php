<?php


namespace App\Virtual\Requests\Activities;

/**
 * @OA\Schema(
 *     title="Обновление пользователя",
 *     description="Обновление данных о уже существующем пользователе",
 *     type="object",
 *     required={"user_id"}
 * )
 *
 * Class UpdateUser
 * @package App\Virtual\Requests\Activities
 */
class UpdateUser
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
     *     description="Уникальный идентификатор пользователя",
     *     example="1",
     *     type="number"
     * )
     *
     * @var integer
     */
    public $user_id;
}
