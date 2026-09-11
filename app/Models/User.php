<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Loggable;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        "user_type_id",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getMenuList(){
        return $this->hasMany(MenuPermission::class,"user_type_master_id","user_type_id")->where("lock_status",false);
    }

    public function store($request){        
        $inputs = snakeCase($request);
        $user = new User($inputs->all());
        $user->save();
        return $user->id;
    }

    public function edit($id,$request){
        $inputs = snakeCase($request)->filter(function($val,$index){
            return (in_array($index,$this->fillable));
        });
        $model = self::find($request->id);
        if($model){
            $model->fill($inputs->all());
            $model->update();
            return true;
        }
        return false;
    }

    public function getHelperList(){
        return self::where("user_type_id",5)->where("lock_status",false)->orderBy("id","ASC")->get();
    }
    public function getOperateList(){
        return self::where("user_type_id",6)->where("lock_status",false)->orderBy("id","ASC")->get();
    }
}
