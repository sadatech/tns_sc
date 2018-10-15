<?php
namespace App\Http\Adapter;
use Illuminate\Database\Eloquent\Model;

class EloquentEmployeeAdapter implements UserInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $employee;
    /**
     * Create a new User instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $user
     */
    public function __construct(Model $employee)
    {
        $this->employee = $employee;
    }
    /**
     * Get the user by the given key, value.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getBy($key, $value)
    {
        return $this->employee->where($key, $value)->first();
    }
}