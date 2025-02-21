<?php

namespace App\Models;

use App\Traits\UserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use UserTrait;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'parent',
        'is_enable_login',
        'avatar',
        'category_id',
        'subcategory',
    ];

    protected $appends = [
        'allPermissions',
        'profilelink',
        'avatarlink',
        'isme',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAllpermissionsAttribute()
    {
        $res            = [];
        $allPermissions = $this->getAllPermissions();
        foreach($allPermissions as $p)
        {
            $res[] = $p->name;
        }

        return $res;
    }

    public function languages()
    {

        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir){
                return str_replace($dir, '', $value);
            }, $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir){
                return preg_replace('/[0-9]+/', '', $value);
            }, $arrLang
        );
        $arrLang = array_filter($arrLang);

        return $arrLang;
    }

    public function currantLang()
    {
        return $this->can('lang-change') ? $this->lang : $this->parentUser()->lang;
    }

    public function currantLangPath()
    {
        if($this->can('lang-change'))
        {
            $lang = $this->lang;
            $dir  = base_path() . '/resources/lang/' . $lang . "/";
            if(!is_dir($dir) && $this->roles[0]->name != 'Admin')
            {
                $lang = $this->lang;
            }
        }
        else
        {
            $lang = $this->parentUser()->lang;
        }
        $dir = base_path() . '/resources/lang/' . $lang . "/";
        return is_dir($dir) ? $lang : 'en';
    }

    public function getCreatedBy()
    {
        $roles = $this->getRoleNames();
        return $roles == '["Admin"]' ? $this->id : $this->parent;
    }

    public function parentUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'parent')->first();
    }

    public function unread()
    {
        return Message::where('from', '=', $this->id)->where('is_read', '=', 0)->count();
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1) . "\n";

        return file_put_contents($envFile, $str) ? true : false;
    }

    public static function delete_directory($dir)
    {
        if(!file_exists($dir))
        {
            return true;
        }
        if(!is_dir($dir))
        {
            return unlink($dir);
        }
        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..')
            {
                continue;
            }
            if(!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item))
            {
                return false;
            }
        }
        return rmdir($dir);
    }
    public static function userDefaultDataRegister($user_id)
    {
        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();

        foreach ($allEmail as $email) {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => $user_id,
                    'is_active' => 1,
                ]
            );
        }
    }

    // public function createId()
    // {
    //     if($this->parent == "0")
    //     {
    //         return $this->id;
    //     }
    //     else
    //     {
    //         return $this->parent;
    //     }
    // }

    public function createId()
    {
        return $this->parent == 0 ? $this->id : $this->parent;

    }


    public function getCategoryIdsAttribute()
    {
        return explode(',', $this->category_id);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'id', 'category_id')
                    ->whereIn('id', $this->getCategoryIdsAttribute());
    }

    // public function categories()
    // {
    //     return Category::whereIn('id', $this->getCategoryIdsAttribute())->get();
    // }

    public function Category()
    {
        $categoryIds = explode(',', $this->category_id);
        return  Category::whereIn('id',$categoryIds)->get();
    }
    public function Subcategories()
    {
        $subcategoryIds = explode(',', $this->subcategory);
        return  SubCategory::whereIn('id',$subcategoryIds)->get();
    }

    // public function categories()
    // {
    //     return $this->belongsToMany(Category::class, 'category_user', 'user_id', 'category_id');
    // }

    // public function categories()
    // {
    //     return $this->belongsToMany('App\Models\Category', 'user_categories', 'user_id', 'category_id');
    // }

}
